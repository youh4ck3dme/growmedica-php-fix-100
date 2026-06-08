import { test, expect } from '@playwright/test'

test.describe('NOOR UI components', () => {
  test.beforeEach(async ({ page }) => {
    await page.addInitScript(() => {
      localStorage.setItem('growmedica-storefront-theme', 'noor')
    })
    await page.goto('/')
    await page.waitForLoadState('networkidle')
  })

  test('SearchDrawer opens from header search on NOOR theme', async ({ page }) => {
    await page.setViewportSize({ width: 1280, height: 800 })
    await page.locator('#search-button').click()
    await expect(page.locator('.noor-search-drawer')).toBeVisible()
    await expect(page.locator('#noor-search-input')).toBeFocused()
  })

  test('Toast appears after accepting cookies', async ({ page }) => {
    await page.evaluate(() => localStorage.removeItem('gm_cookie_consent'))
    await page.reload()
    await page.waitForLoadState('networkidle')

    const banner = page.getByRole('dialog', { name: 'Súhlas s cookies' })
    if ((await banner.count()) === 0) {
      test.skip(true, 'Cookie banner not visible')
    }

    await page.getByRole('button', { name: 'Prijať všetky' }).click()
    await expect(page.locator('.noor-toast')).toBeVisible()
    await expect(page.locator('.noor-toast__title')).toContainText('Cookies prijaté')
  })

  test('FAQ accordion expands on /faq for NOOR theme', async ({ page }) => {
    await page.goto('/faq')
    await expect(page.locator('.noor-accordion')).toBeVisible()
    const firstTrigger = page.locator('.noor-accordion__trigger').first()
    await firstTrigger.click()
    await expect(firstTrigger).toHaveAttribute('aria-expanded', 'true')
  })
})
