import { test, expect } from '@playwright/test'

test.describe('PageSpeed regression guards', () => {
  test('homepage has no React hydration #418 in console', async ({ page }) => {
    const errors: string[] = []
    page.on('console', (msg) => {
      if (msg.type() === 'error' && msg.text().includes('418')) {
        errors.push(msg.text())
      }
    })

    await page.goto('/')
    await page.waitForLoadState('networkidle')

    expect(errors).toEqual([])
  })

  test('product cards avoid raw Shopify CDN urls', async ({ page }) => {
    await page.goto('/')
    await page.locator('#featured-heading').scrollIntoViewIfNeeded()

    const cards = page.locator('article.product-card')
    const cardCount = await cards.count()
    test.skip(cardCount === 0, 'No featured products in test environment')

    const rawShopifyImages = page.locator('article.product-card img[src*="cdn.shopify.com"]')
    expect(await rawShopifyImages.count()).toBe(0)
  })

  test('announcement bar reserves layout space when enabled', async ({ page }) => {
    await page.goto('/')
    const slot = page.locator('.announcement-bar-slot--reserved')
    if ((await slot.count()) > 0) {
      const box = await slot.first().boundingBox()
      expect(box?.height ?? 0).toBeGreaterThanOrEqual(36)
    }
  })

  test('homepage passes Lighthouse color-contrast audit', async ({ page }) => {
    await page.goto('/')
    await page.waitForLoadState('networkidle')

    const results = await page.evaluate(async () => {
      // @ts-expect-error axe injected below
      if (!window.axe) {
        await new Promise<void>((resolve, reject) => {
          const script = document.createElement('script')
          script.src = 'https://cdnjs.cloudflare.com/ajax/libs/axe-core/4.10.3/axe.min.js'
          script.onload = () => resolve()
          script.onerror = () => reject(new Error('Failed to load axe-core'))
          document.head.appendChild(script)
        })
      }

      // @ts-expect-error axe global
      return window.axe.run(document, {
        runOnly: { type: 'rule', values: ['color-contrast'] },
      })
    })

    const violations = results.violations.filter((v: { id: string }) => v.id === 'color-contrast')
    expect(violations).toEqual([])
  })
})
