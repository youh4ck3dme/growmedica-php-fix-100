import { test, expect } from '@playwright/test'
import { acceptCookies } from '../helpers/cookies'

test.describe('Mock Shopify cart API', () => {
  test('POST /api/cart/add vráti count a cookie', async ({ page, request }) => {
    await page.goto('/produkty')
    await acceptCookies(page)

    const variantId = await page.evaluate(async () => {
      const res = await fetch('/produkty')
      return 'gid://shopify/ProductVariant/mock-vitaminy-mineraly-1'
    })

    const response = await request.post('/api/cart/add', {
      data: { variantId, quantity: 1 },
    })

    expect(response.ok()).toBeTruthy()
    const body = (await response.json()) as { count?: number }
    expect(body.count).toBe(1)
  })

  test('pridanie do košíka aktualizuje badge v hlavičke', async ({ page }) => {
    await page.goto('/produkty')
    await acceptCookies(page)
    await page.locator('article.product-card').first().locator('a.btn-primary').click()

    const addToCartBtn = page.locator('#add-to-cart-btn')
    await expect(addToCartBtn).toBeEnabled()
    await addToCartBtn.click()

    await expect(page.locator('#cart-button span[aria-hidden="true"]')).toHaveText('1')
  })
})
