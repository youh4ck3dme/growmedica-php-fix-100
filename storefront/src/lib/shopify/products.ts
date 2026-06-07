/**
 * Shopify product fetching functions
 */

import { shopifyFetch } from './client'
import {
  GET_PRODUCTS_QUERY,
  GET_PRODUCT_BY_HANDLE_QUERY,
  GET_FEATURED_PRODUCTS_QUERY,
  GET_ALL_PRODUCTS_FOR_SITEMAP,
} from './queries'
import { buildCategorySearchQuery, type MainCategory } from '@/lib/category-map'
import type { Product, ProductListItem, Connection } from './types'

// ─── Types ────────────────────────────────────────────────────────────────────

interface GetProductsOptions {
  first?: number
  after?: string
  query?: string
  sortKey?: 'TITLE' | 'PRICE' | 'BEST_SELLING' | 'CREATED_AT' | 'UPDATED_AT' | 'RELEVANCE'
  reverse?: boolean
}

// ─── Product Fetching ─────────────────────────────────────────────────────────

export async function getProducts(options: GetProductsOptions = {}) {
  const { first = 24, after, query, sortKey = 'BEST_SELLING', reverse = false } = options

  const data = await shopifyFetch<{
    products: Connection<ProductListItem>
  }>({
    query: GET_PRODUCTS_QUERY,
    variables: { first, after, query, sortKey, reverse },
    tags: ['products'],
    revalidate: 3600,
  })

  return data.products
}

export async function getProductByHandle(handle: string) {
  const data = await shopifyFetch<{ product: Product | null }>({
    query: GET_PRODUCT_BY_HANDLE_QUERY,
    variables: { handle },
    tags: [`product-${handle}`, 'products'],
    revalidate: 3600,
  })

  return data.product
}

export async function getFeaturedProducts(count = 8) {
  const data = await shopifyFetch<{
    products: Connection<ProductListItem>
  }>({
    query: GET_FEATURED_PRODUCTS_QUERY,
    variables: { first: count },
    tags: ['products', 'featured'],
    revalidate: 3600,
  })

  return data.products.edges.map((e) => e.node)
}

export async function getAllProductHandlesForSitemap(): Promise<
  Array<{ handle: string; updatedAt: string }>
> {
  const handles: Array<{ handle: string; updatedAt: string }> = []
  let hasNextPage = true
  let after: string | undefined

  while (hasNextPage) {
    const data = await shopifyFetch<{
      products: {
        edges: Array<{ node: { handle: string; updatedAt: string }; cursor: string }>
        pageInfo: { hasNextPage: boolean; endCursor: string | null }
      }
    }>({
      query: GET_ALL_PRODUCTS_FOR_SITEMAP,
      variables: { first: 250, after },
      revalidate: 86400,
    })

    data.products.edges.forEach((e) => handles.push(e.node))
    hasNextPage = data.products.pageInfo.hasNextPage
    after = data.products.pageInfo.endCursor ?? undefined
  }

  return handles
}

export async function getRelatedProducts(
  categorySlug: MainCategory,
  excludeHandle: string,
  count = 4,
): Promise<ProductListItem[]> {
  const query = buildCategorySearchQuery(categorySlug)
  if (!query || categorySlug === 'ostatne') return []

  const result = await getProducts({
    first: count + 8,
    query,
    sortKey: 'BEST_SELLING',
  })

  return result.edges
    .map((e) => e.node)
    .filter((p) => p.handle !== excludeHandle)
    .slice(0, count)
}

export function getProductCompositionHtml(product: Product): string | null {
  const fields = product.metafields?.filter(Boolean) ?? []
  const composition = fields.find(
    (f) => f && (f.key === 'composition' || f.key === 'zlozenie'),
  )
  if (!composition?.value) return null
  if (composition.type === 'multi_line_text_field' || composition.value.includes('<')) {
    return composition.value
  }
  return `<p>${composition.value}</p>`
}
