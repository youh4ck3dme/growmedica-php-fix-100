import { getCategoryDefinition, getNavCategories, type MainCategory } from '@/lib/category-map'
import type { Collection, Connection, Product, ProductListItem, ShopifyImage } from './types'

type Variables = Record<string, unknown>

const MOCK_UPDATED_AT = '2026-01-01T00:00:00Z'
const MOCK_IMAGE: ShopifyImage = {
  id: 'gid://shopify/Image/mock',
  url: '/logo-icon.svg',
  altText: 'GrowMedica produkt',
  width: 512,
  height: 512,
}

function pageInfo(hasNextPage = false, endCursor: string | null = null) {
  return {
    hasNextPage,
    hasPreviousPage: false,
    startCursor: null,
    endCursor,
  }
}

function connection<T>(nodes: T[], hasNextPage = false): Connection<T> {
  return {
    edges: nodes.map((node, index) => ({
      node,
      cursor: `mock-cursor-${index + 1}`,
    })),
    pageInfo: pageInfo(hasNextPage, hasNextPage ? `mock-cursor-${nodes.length}` : null),
  }
}

function categorySeed(slug: MainCategory) {
  const def = getCategoryDefinition(slug)
  const productTypeRule = def.rules.find((rule) => rule.kind === 'productType')
  const tagRules = def.rules.filter((rule) => rule.kind === 'tag')

  return {
    productType: productTypeRule?.value ?? def.title,
    tags: tagRules.length > 0 ? tagRules.map((rule) => rule.value) : [def.title],
  }
}

function productListItem(slug: MainCategory, index: number): ProductListItem {
  const def = getCategoryDefinition(slug)
  const seed = categorySeed(slug)
  const price = {
    amount: String(9 + index * 3),
    currencyCode: 'EUR',
  }

  return {
    id: `gid://shopify/Product/mock-${slug}-${index}`,
    handle: `${slug}-mock-${index}`,
    title: `${def.title} Mock produkt ${index}`,
    vendor: 'GrowMedica',
    productType: seed.productType,
    tags: seed.tags,
    availableForSale: true,
    priceRange: {
      minVariantPrice: price,
      maxVariantPrice: price,
    },
    compareAtPriceRange: {
      minVariantPrice: price,
      maxVariantPrice: price,
    },
    featuredImage: null,
    variants: {
      edges: [
        {
          node: {
            id: `gid://shopify/ProductVariant/mock-${slug}-${index}`,
            title: 'Default Title',
            availableForSale: true,
            selectedOptions: [{ name: 'Title', value: 'Default Title' }],
            price,
            compareAtPrice: null,
          },
        },
      ],
    },
  }
}

const MOCK_PRODUCTS = getNavCategories().flatMap((category) => [
  productListItem(category.slug, 1),
  productListItem(category.slug, 2),
])

const MOCK_COLLECTIONS: Collection[] = getNavCategories().map((category) => ({
  id: `gid://shopify/Collection/mock-${category.slug}`,
  handle: category.slug,
  title: category.title,
  description: category.description ?? '',
  descriptionHtml: category.description ?? '',
  image: MOCK_IMAGE,
  seo: {
    title: category.title,
    description: category.description ?? null,
  },
  updatedAt: MOCK_UPDATED_AT,
}))

function productsForHandle(handle: string): ProductListItem[] {
  return MOCK_PRODUCTS.filter((product) => product.handle.startsWith(`${handle}-`))
}

function productsForQuery(query: unknown, first: unknown): ProductListItem[] {
  const limit = typeof first === 'number' ? first : 24
  if (typeof query !== 'string' || query.trim() === '') {
    return MOCK_PRODUCTS.slice(0, limit)
  }

  const normalizedQuery = query.toLowerCase()
  const matched = MOCK_PRODUCTS.filter((product) => {
    const haystack = [
      product.title,
      product.productType,
      product.vendor,
      ...product.tags,
    ]
      .join(' ')
      .toLowerCase()

    return normalizedQuery
      .replace(/[()']/g, ' ')
      .split(/\s+or\s+|\s+and\s+|\s+/)
      .filter((token) => token.length > 2 && !token.includes(':'))
      .some((token) => haystack.includes(token))
  })

  return (matched.length > 0 ? matched : MOCK_PRODUCTS).slice(0, limit)
}

function productDetail(handle: string): Product | null {
  const item = MOCK_PRODUCTS.find((product) => product.handle === handle)
  if (!item) return null

  return {
    ...item,
    description: `${item.title} je testovací produkt pre integritné testy.`,
    descriptionHtml: `<p>${item.title} je testovací produkt pre integritné testy.</p>`,
    options: [{ id: 'gid://shopify/ProductOption/mock-title', name: 'Title', values: ['Default Title'] }],
    variants: {
      edges: item.variants.edges.map((edge) => ({
        node: {
          ...edge.node,
          sku: `MOCK-${item.handle}`,
          quantityAvailable: 10,
          image: null,
        },
      })),
    },
    images: { edges: [] },
    featuredImage: item.featuredImage,
    seo: {
      title: item.title,
      description: `${item.title} je testovací produkt pre integritné testy.`,
    },
    metafields: [],
    updatedAt: MOCK_UPDATED_AT,
  }
}

export function isShopifyMockMode(): boolean {
  return process.env.SHOPIFY_MOCK_MODE === '1'
}

export function getMockShopifyResponse<T>(query: string, variables: Variables = {}): T {
  if (query.includes('query GetCollectionByHandle')) {
    const handle = String(variables.handle ?? '')
    const collection = MOCK_COLLECTIONS.find((item) => item.handle === handle)
    return {
      collection: collection
        ? {
            ...collection,
            products: connection(productsForHandle(handle).slice(0, Number(variables.first ?? 24))),
          }
        : null,
    } as T
  }

  if (query.includes('query GetCollectionsPaginated') || query.includes('query GetCollections(')) {
    return {
      collections: connection(MOCK_COLLECTIONS.slice(0, Number(variables.first ?? 250))),
    } as T
  }

  if (query.includes('query CatalogCategoryCounts')) {
    return {
      products: connection(MOCK_PRODUCTS.map((product) => ({
        productType: product.productType,
        tags: product.tags,
      }))),
    } as T
  }

  if (query.includes('query GetFeaturedProducts')) {
    return {
      products: connection(MOCK_PRODUCTS.slice(0, Number(variables.first ?? 8))),
    } as T
  }

  if (query.includes('query GetProducts')) {
    return {
      products: connection(productsForQuery(variables.query, variables.first)),
    } as T
  }

  if (query.includes('query GetProductByHandle')) {
    return {
      product: productDetail(String(variables.handle ?? '')),
    } as T
  }

  if (query.includes('query GetAllProductsForSitemap')) {
    return {
      products: connection(MOCK_PRODUCTS.map((product) => ({
        handle: product.handle,
        updatedAt: MOCK_UPDATED_AT,
      }))),
    } as T
  }

  if (query.includes('query GetAllCollectionsForSitemap')) {
    return {
      collections: connection(MOCK_COLLECTIONS.map((collection) => ({
        handle: collection.handle,
        updatedAt: collection.updatedAt,
      }))),
    } as T
  }

  throw new Error('Unhandled Shopify mock query')
}
