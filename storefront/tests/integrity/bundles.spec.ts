import { test, expect } from '@playwright/test'
import { acceptCookies } from '../helpers/cookies'
import { HEALTH_BUNDLE_CATALOG, getBundleBySlug, getFeaturedBundles } from '@/lib/bundles/catalog'
import { BRAND_COPY } from '@/lib/brand'

test.describe('Health bundle catalog', () => {
  test('1. catalog contains exactly 63 bundles', () => {
    expect(HEALTH_BUNDLE_CATALOG).toHaveLength(63)
  })

  test('2. all bundle slugs are unique', () => {
    const slugs = HEALTH_BUNDLE_CATALOG.map((b) => b.slug)
    expect(new Set(slugs).size).toBe(slugs.length)
  })

  test('3. all bundle ids are unique and sequential 1–63', () => {
    const ids = HEALTH_BUNDLE_CATALOG.map((b) => b.id).sort((a, b) => a - b)
    expect(ids).toEqual(Array.from({ length: 63 }, (_, i) => i + 1))
  })

  test('4. discount percentages are within 10–20', () => {
    for (const bundle of HEALTH_BUNDLE_CATALOG) {
      expect(bundle.discountPercent).toBeGreaterThanOrEqual(10)
      expect(bundle.discountPercent).toBeLessThanOrEqual(20)
    }
  })

  test('5. getBundleBySlug resolves known bundle', () => {
    const bundle = getBundleBySlug('growmedica-komplet')
    expect(bundle?.name).toBe('GrowMedica Komplet')
    expect(bundle?.discountPercent).toBe(20)
  })

  test('6. getFeaturedBundles returns requested count', () => {
    expect(getFeaturedBundles(6)).toHaveLength(6)
  })

  test('7. brand copy includes about slogan and health lines', () => {
    expect(BRAND_COPY.aboutSlogan).toBeTruthy()
    expect(BRAND_COPY.aboutHealthLines.length).toBeGreaterThanOrEqual(5)
  })

  test('8. /balicky shows price and add-to-cart for Shopify-linked bundles', async ({ page }) => {
    await page.goto('/balicky')
    await acceptCookies(page)
    const linked = page.locator('[data-has-shopify-product="true"]').first()
    await expect(linked).toBeVisible()
    await expect(linked.getByTestId('bundle-price')).toBeVisible()
    await expect(linked.getByTestId('bundle-add-to-cart')).toBeVisible()
  })

  test('9. bundle add-to-cart updates cart badge', async ({ page }) => {
    await page.goto('/balicky')
    await acceptCookies(page)
    await page.getByTestId('bundle-add-to-cart').first().click()
    await expect(page.locator('#cart-button span[aria-hidden="true"]')).toHaveText('1', {
      timeout: 10_000,
    })
  })
})
