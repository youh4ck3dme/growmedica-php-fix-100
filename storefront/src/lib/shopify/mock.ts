import { getCategoryDefinition, getNavCategories, type MainCategory } from '@/lib/category-map'
import type { Cart, CartLine, Collection, Connection, Product, ProductListItem, ShopifyImage } from './types'

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

function cordycepsProduct(): ProductListItem {
  const price = {
    amount: '16.20',
    currencyCode: 'EUR',
  }

  return {
    id: 'gid://shopify/Product/mock-cordyceps',
    handle: 'mycomedica-cordyceps-50-90-rastlinnych-kapsul',
    title: 'MycoMedica Cordyceps 50% 90 rastlinných kapsúl',
    vendor: 'MycoMedica',
    productType: 'Regeneračné doplnky',
    tags: ['Regeneračné doplnky', 'Cordyceps', 'Energia'],
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
            id: 'gid://shopify/ProductVariant/mock-cordyceps-default',
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
]).concat(cordycepsProduct())

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

  return matched.slice(0, limit)
}

interface MockCartEntry {
  lines: Array<{ id: string; merchandiseId: string; quantity: number }>
}

const mockCarts = new Map<string, MockCartEntry>()
let mockCartCounter = 0
let mockLineCounter = 0

function findVariantById(variantId: string) {
  for (const product of MOCK_PRODUCTS) {
    for (const edge of product.variants.edges) {
      if (edge.node.id === variantId) {
        return { product, variant: edge.node }
      }
    }
  }
  return null
}

function buildMockCartLine(
  lineId: string,
  merchandiseId: string,
  quantity: number,
): CartLine | null {
  const match = findVariantById(merchandiseId)
  if (!match) return null

  const { product, variant } = match
  const unitAmount = variant.price.amount
  const totalAmount = (parseFloat(unitAmount) * quantity).toFixed(2)

  return {
    id: lineId,
    quantity,
    merchandise: {
      id: variant.id,
      title: variant.title,
      selectedOptions: variant.selectedOptions,
      product: {
        id: product.id,
        handle: product.handle,
        title: product.title,
        featuredImage: product.featuredImage,
      },
    },
    cost: {
      totalAmount: { amount: totalAmount, currencyCode: variant.price.currencyCode },
      subtotalAmount: { amount: totalAmount, currencyCode: variant.price.currencyCode },
    },
  }
}

function buildMockCart(cartId: string, entry: MockCartEntry): Cart {
  const lines = entry.lines
    .map((line) => buildMockCartLine(line.id, line.merchandiseId, line.quantity))
    .filter((line): line is CartLine => line !== null)

  const subtotal = lines.reduce(
    (sum, line) => sum + parseFloat(line.cost.subtotalAmount.amount),
    0,
  )
  const currencyCode = lines[0]?.cost.subtotalAmount.currencyCode ?? 'EUR'
  const subtotalAmount = { amount: subtotal.toFixed(2), currencyCode }

  return {
    id: cartId,
    checkoutUrl: 'https://checkout.shopify.com/mock-checkout',
    totalQuantity: lines.reduce((sum, line) => sum + line.quantity, 0),
    lines: connection(lines),
    cost: {
      subtotalAmount,
      totalAmount: subtotalAmount,
      totalTaxAmount: { amount: '0.00', currencyCode },
    },
  }
}

function mergeCartLines(
  existing: MockCartEntry['lines'],
  incoming: Array<{ merchandiseId: string; quantity: number }>,
): MockCartEntry['lines'] {
  const merged = existing.map((line) => ({ ...line }))

  for (const line of incoming) {
    const existingLine = merged.find((item) => item.merchandiseId === line.merchandiseId)
    if (existingLine) {
      existingLine.quantity += line.quantity
    } else {
      merged.push({
        id: `gid://shopify/CartLine/mock-${++mockLineCounter}`,
        merchandiseId: line.merchandiseId,
        quantity: line.quantity,
      })
    }
  }

  return merged
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

  if (query.includes('query GetCart')) {
    const cartId = String(variables.cartId ?? '')
    const entry = mockCarts.get(cartId)
    return {
      cart: entry ? buildMockCart(cartId, entry) : null,
    } as T
  }

  if (query.includes('mutation CreateCart')) {
    const lines = (variables.lines ?? []) as Array<{ merchandiseId: string; quantity: number }>
    const cartId = `gid://shopify/Cart/mock-${++mockCartCounter}`
    const entry = { lines: mergeCartLines([], lines) }
    mockCarts.set(cartId, entry)
    return {
      cartCreate: {
        cart: buildMockCart(cartId, entry),
        userErrors: [],
      },
    } as T
  }

  if (query.includes('mutation AddToCart')) {
    const cartId = String(variables.cartId ?? '')
    const lines = (variables.lines ?? []) as Array<{ merchandiseId: string; quantity: number }>
    const entry = mockCarts.get(cartId)
    if (!entry) {
      return {
        cartLinesAdd: {
          cart: null,
          userErrors: [{ field: ['cartId'], message: 'Cart not found' }],
        },
      } as T
    }
    entry.lines = mergeCartLines(entry.lines, lines)
    mockCarts.set(cartId, entry)
    return {
      cartLinesAdd: {
        cart: buildMockCart(cartId, entry),
        userErrors: [],
      },
    } as T
  }

  if (query.includes('mutation UpdateCartLines')) {
    const cartId = String(variables.cartId ?? '')
    const lines = (variables.lines ?? []) as Array<{ id: string; quantity: number }>
    const entry = mockCarts.get(cartId)
    if (!entry) {
      return {
        cartLinesUpdate: {
          cart: null,
          userErrors: [{ field: ['cartId'], message: 'Cart not found' }],
        },
      } as T
    }
    for (const update of lines) {
      const line = entry.lines.find((item) => item.id === update.id)
      if (line) line.quantity = update.quantity
    }
    entry.lines = entry.lines.filter((line) => line.quantity > 0)
    mockCarts.set(cartId, entry)
    return {
      cartLinesUpdate: {
        cart: buildMockCart(cartId, entry),
        userErrors: [],
      },
    } as T
  }

  if (query.includes('mutation RemoveCartLines')) {
    const cartId = String(variables.cartId ?? '')
    const lineIds = (variables.lineIds ?? []) as string[]
    const entry = mockCarts.get(cartId)
    if (!entry) {
      return {
        cartLinesRemove: {
          cart: null,
          userErrors: [{ field: ['cartId'], message: 'Cart not found' }],
        },
      } as T
    }
    entry.lines = entry.lines.filter((line) => !lineIds.includes(line.id))
    mockCarts.set(cartId, entry)
    return {
      cartLinesRemove: {
        cart: buildMockCart(cartId, entry),
        userErrors: [],
      },
    } as T
  }

  throw new Error('Unhandled Shopify mock query')
}
