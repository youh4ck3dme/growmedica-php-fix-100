import { test, expect } from '@playwright/test'
import { BRAND_ASSETS } from '../fixtures/brand'

test.describe('Brand assets — static files', () => {
  for (const assetPath of BRAND_ASSETS) {
    test(`${assetPath} je dostupný (HTTP 200)`, async ({ request }) => {
      const response = await request.get(assetPath)
      expect(response.status()).toBe(200)
    })
  }

  test('manifest.webmanifest obsahuje theme_color teal', async ({ request }) => {
    const response = await request.get('/manifest.webmanifest')
    expect(response.status()).toBe(200)
    const body = await response.json()
    expect(body.theme_color?.toUpperCase()).toBe('#35C79A')
    expect(body.name).toContain('GrowMedica')
  })
})
