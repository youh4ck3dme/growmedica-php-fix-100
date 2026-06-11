import { test, expect } from '@playwright/test'

const revalidationSecret = process.env.SHOPIFY_REVALIDATION_SECRET ?? 'mock-revalidation-secret'

test.describe('Shopify revalidation webhook', () => {
  test('rejects requests without the shared secret', async ({ request }) => {
    const response = await request.post('/api/revalidate', {
      data: { handle: 'growmedica-komplet' },
    })

    expect(response.status()).toBe(401)
    const json = await response.json()
    expect(json.error).toBe('Unauthorized')
  })

  test('accepts the shared secret from the webhook header', async ({ request }) => {
    const response = await request.post('/api/revalidate', {
      headers: {
        'x-revalidation-secret': revalidationSecret,
        'x-shopify-topic': 'products/update',
      },
      data: { handle: 'growmedica-komplet' },
    })

    expect(response.status()).toBe(200)
    const json = await response.json()
    expect(json.revalidated).toBe(true)
    expect(json.at).toEqual(expect.any(String))
  })
})
