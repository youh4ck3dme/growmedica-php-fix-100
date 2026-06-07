/**
 * Resolves navigable categories from Shopify catalog via category-map.
 */

import {
  HIDDEN_COLLECTION_HANDLES,
  buildCategorySearchQuery,
  getCategoryDefinition,
  getNavCategories,
  normalizeCategorySlug,
  resolveCategory,
  type MainCategory,
} from '@/lib/category-map'
import { getCollectionUrl } from '@/lib/utils'
import { shopifyFetch } from './client'
import { getCollectionByHandle, getAllShopifyCollections } from './collections'
import { getProducts } from './products'
import type { ProductListItem } from './types'

export type NavCollectionItem = {
  handle: string
  title: string
  description: string | null
  href: string
  productCount: number
  icon?: string
  menuLabel: string
  source: 'shopify' | 'catalog'
}

export type CollectionView = {
  handle: string
  title: string
  description: string | null
  products: ProductListItem[]
  source: 'shopify' | 'catalog'
  page: number
  hasNextPage: boolean
  hasPreviousPage: boolean
  totalOnPage: number
}

const PAGE_SIZE = 24

async function getCatalogCategoryCounts(): Promise<Map<MainCategory, number>> {
  const counts = new Map<MainCategory, number>()
  for (const def of getNavCategories()) {
    counts.set(def.slug, 0)
  }
  counts.set('ostatne', 0)

  let hasNextPage = true
  let after: string | undefined

  while (hasNextPage) {
    const data = await shopifyFetch<{
      products: {
        edges: Array<{ node: { productType: string; tags: string[] } }>
        pageInfo: { hasNextPage: boolean; endCursor: string | null }
      }
    }>({
      query: /* GraphQL */ `
        query CatalogCategoryCounts($first: Int!, $after: String) {
          products(first: $first, after: $after) {
            pageInfo { hasNextPage endCursor }
            edges { node { productType tags } }
          }
        }
      `,
      variables: { first: 250, after },
      tags: ['products', 'catalog-nav'],
      revalidate: 3600,
    })

    for (const { node } of data.products.edges) {
      const slug = resolveCategory(node)
      counts.set(slug, (counts.get(slug) ?? 0) + 1)
    }

    hasNextPage = data.products.pageInfo.hasNextPage
    after = data.products.pageInfo.endCursor ?? undefined
  }

  return counts
}

export async function getNavCollectionItems(): Promise<NavCollectionItem[]> {
  const [shopifyCollections, catalogCounts] = await Promise.all([
    getAllShopifyCollections(),
    getCatalogCategoryCounts(),
  ])

  const shopifyByHandle = new Map(
    shopifyCollections
      .filter((c) => !HIDDEN_COLLECTION_HANDLES.has(c.handle))
      .map((c) => [c.handle, c])
  )

  const items: NavCollectionItem[] = []
  const seenHandles = new Set<string>()

  for (const def of getNavCategories()) {
    const shopify = shopifyByHandle.get(def.slug)
    const productCount = catalogCounts.get(def.slug) ?? 0

    if (productCount === 0 && !shopify) continue
    if (def.slug === 'ostatne' && productCount === 0) continue

    seenHandles.add(def.slug)
    items.push({
      handle: def.slug,
      title: shopify?.title ?? def.title,
      description: shopify?.description ?? def.description ?? null,
      href: getCollectionUrl(def.slug),
      productCount,
      icon: def.icon,
      menuLabel: def.menuLabel,
      source: shopify ? 'shopify' : 'catalog',
    })
  }

  for (const shopify of shopifyCollections) {
    if (HIDDEN_COLLECTION_HANDLES.has(shopify.handle) || seenHandles.has(shopify.handle)) {
      continue
    }

    const withProducts = await getCollectionByHandle(shopify.handle, 1)
    const productCount = withProducts?.products?.edges?.length ?? 0
    if (productCount === 0) continue

    items.push({
      handle: shopify.handle,
      title: shopify.title,
      description: shopify.description ?? null,
      href: getCollectionUrl(shopify.handle),
      productCount,
      menuLabel: shopify.title.toUpperCase(),
      source: 'shopify',
    })
  }

  return items.sort((a, b) => a.title.localeCompare(b.title, 'sk'))
}

export async function getCollectionViewByHandle(
  handle: string,
  page = 1
): Promise<CollectionView | null> {
  if (HIDDEN_COLLECTION_HANDLES.has(handle)) {
    return null
  }

  const slug = normalizeCategorySlug(handle)
  if (!slug) return null

  const def = getCategoryDefinition(slug)
  if (slug === 'ostatne') {
    // Fallback category: products not matched by search — skip for now in listing
    return null
  }

  const shopifyCollection = await getCollectionByHandle(slug, PAGE_SIZE)
  if (shopifyCollection) {
    const products = shopifyCollection.products?.edges?.map((e) => e.node) ?? []
    if (products.length > 0) {
      return {
        handle: slug,
        title: shopifyCollection.title,
        description: shopifyCollection.description ?? def.description ?? null,
        products,
        source: 'shopify',
        page: 1,
        hasNextPage: false,
        hasPreviousPage: false,
        totalOnPage: products.length,
      }
    }
  }

  const query = buildCategorySearchQuery(slug)
  if (!query) return null

  const safePage = Math.max(1, page)
  let after: string | undefined
  for (let i = 1; i < safePage; i++) {
    const skip = await getProducts({ first: PAGE_SIZE, after, query, sortKey: 'BEST_SELLING' })
    if (!skip.pageInfo.hasNextPage) {
      return null
    }
    after = skip.pageInfo.endCursor ?? undefined
  }

  const result = await getProducts({
    first: PAGE_SIZE,
    after,
    query,
    sortKey: 'BEST_SELLING',
  })

  const products = result.edges.map((e) => e.node)
  if (products.length === 0 && safePage === 1) return null
  if (products.length === 0) return null

  return {
    handle: slug,
    title: def.title,
    description: def.description ?? null,
    products,
    source: 'catalog',
    page: safePage,
    hasNextPage: result.pageInfo.hasNextPage,
    hasPreviousPage: safePage > 1,
    totalOnPage: products.length,
  }
}

/** Top products for mega menu sidebar (cached via shopifyFetch). */
export async function getCategoryFeaturedProducts(
  handle: string,
  count = 3
): Promise<ProductListItem[]> {
  const slug = normalizeCategorySlug(handle)
  if (!slug || slug === 'ostatne') return []

  const shopifyCollection = await getCollectionByHandle(slug, count)
  const fromCollection =
    shopifyCollection?.products?.edges?.map((e) => e.node).slice(0, count) ?? []
  if (fromCollection.length > 0) return fromCollection

  const query = buildCategorySearchQuery(slug)
  if (!query) return []

  const result = await getProducts({
    first: count,
    query,
    sortKey: 'BEST_SELLING',
  })

  return result.edges.map((e) => e.node)
}
