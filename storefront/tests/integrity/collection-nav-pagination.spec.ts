import { expect, test } from '@playwright/test'

process.env.SHOPIFY_STORE_DOMAIN = 'mock-store.myshopify.com'
process.env.SHOPIFY_STOREFRONT_ACCESS_TOKEN = 'mock-storefront-token'
process.env.SHOPIFY_API_VERSION = '2025-01'

type ShopifyFetchCall = {
  operation: string
  variables: Record<string, unknown>
}

function pageInfo(hasNextPage: boolean, endCursor: string | null = null) {
  return {
    hasNextPage,
    hasPreviousPage: false,
    startCursor: null,
    endCursor,
  }
}

function productNode(handle: string) {
  return {
    id: `gid://shopify/Product/${handle}`,
    handle,
    title: handle,
    vendor: 'Risk Covered',
    productType: 'DOPLNKY VYZIVY',
    tags: ['Vitaminy'],
    availableForSale: true,
    priceRange: {
      minVariantPrice: { amount: '10.00', currencyCode: 'EUR' },
      maxVariantPrice: { amount: '10.00', currencyCode: 'EUR' },
    },
    compareAtPriceRange: {
      minVariantPrice: { amount: '10.00', currencyCode: 'EUR' },
      maxVariantPrice: { amount: '10.00', currencyCode: 'EUR' },
    },
    featuredImage: null,
    variants: { edges: [] },
  }
}

function productsConnection(edges: Array<{ node: ReturnType<typeof productNode>; cursor: string }>, hasNextPage: boolean) {
  return {
    edges,
    pageInfo: pageInfo(hasNextPage, hasNextPage ? 'cursor-page-1' : null),
  }
}

function installShopifyFetchMock(finalPageEdges: Array<{ node: ReturnType<typeof productNode>; cursor: string }>) {
  const calls: ShopifyFetchCall[] = []
  const originalFetch = globalThis.fetch

  globalThis.fetch = async (_input, init) => {
    const body = JSON.parse(String(init?.body ?? '{}')) as {
      query?: string
      variables?: Record<string, unknown>
    }
    const query = body.query ?? ''
    const variables = body.variables ?? {}

    if (query.includes('query GetCollectionByHandle')) {
      calls.push({ operation: 'GetCollectionByHandle', variables })
      return Response.json({ data: { collection: null } })
    }

    if (query.includes('query GetProducts')) {
      calls.push({ operation: 'GetProducts', variables })

      if (variables.first === 50) {
        return Response.json({
          data: {
            products: productsConnection(
              [{ node: productNode('vendor-sample'), cursor: 'vendor-sample-cursor' }],
              false,
            ),
          },
        })
      }

      if (variables.after === undefined) {
        return Response.json({
          data: {
            products: productsConnection(
              [{ node: productNode('page-1-product'), cursor: 'cursor-page-1' }],
              true,
            ),
          },
        })
      }

      return Response.json({
        data: {
          products: productsConnection(finalPageEdges, false),
        },
      })
    }

    throw new Error(`Unexpected Shopify query in test: ${query}`)
  }

  return {
    calls,
    restore() {
      globalThis.fetch = originalFetch
    },
  }
}

test.describe('collection catalog pagination', () => {
  test.describe.configure({ mode: 'serial' })

  test('returns null for an empty first catalog page', async () => {
    const mock = installShopifyFetchMock([])

    try {
      const { getCollectionViewByHandle } = await import('../../src/lib/shopify/collection-nav')

      const view = await getCollectionViewByHandle('vitaminy-mineraly', { page: 1 })

      expect(view).toBeNull()
      expect(mock.calls.map((call) => call.operation)).toEqual([
        'GetCollectionByHandle',
        'GetProducts',
        'GetProducts',
      ])
    } finally {
      mock.restore()
    }
  })

  test('returns an empty CollectionView for an empty catalog page after page 1', async () => {
    const mock = installShopifyFetchMock([])

    try {
      const { getCollectionViewByHandle } = await import('../../src/lib/shopify/collection-nav')

      const view = await getCollectionViewByHandle('vitaminy-mineraly', { page: 2 })

      expect(view).toMatchObject({
        handle: 'vitaminy-mineraly',
        source: 'catalog',
        products: [],
        page: 2,
        hasNextPage: false,
        hasPreviousPage: true,
        totalOnPage: 0,
      })
      expect(view?.availableVendors).toEqual(['Risk Covered'])
      expect(mock.calls.map((call) => call.operation)).toEqual([
        'GetCollectionByHandle',
        'GetProducts',
        'GetProducts',
        'GetProducts',
      ])
      expect(mock.calls.at(-1)?.variables.after).toBe('cursor-page-1')
    } finally {
      mock.restore()
    }
  })
})
