import { test, expect } from '@playwright/test'
import {
  isLockedNoorDemo,
  resolveInitialTheme,
} from '../../src/lib/theme/storefront-theme'

const isNoorDemoTest = process.env.NOOR_DEMO_TEST === '1'

test.describe('NOOR demo — theme resolution', () => {
  test.skip(!isNoorDemoTest, 'Run via yarn test:noor-demo')

  test('isLockedNoorDemo is true with demo env', () => {
    expect(isLockedNoorDemo()).toBe(true)
  })

  test('resolveInitialTheme ignores stored classic preference', () => {
    expect(resolveInitialTheme('classic')).toBe('noor')
    expect(resolveInitialTheme('noor')).toBe('noor')
    expect(resolveInitialTheme(null)).toBe('noor')
  })
})

test.describe('NOOR demo — storefront smoke', () => {
  test.skip(!isNoorDemoTest, 'Run via yarn test:noor-demo')

  test('SSR html exposes noor theme attribute', async ({ request }) => {
    const html = await (await request.get('/')).text()
    expect(html).toMatch(/data-storefront-theme="noor"/)
  })

  test('ignores classic localStorage and keeps NOOR skin', async ({ page }) => {
    await page.addInitScript(() => {
      localStorage.setItem('growmedica-storefront-theme', 'classic')
    })
    await page.goto('/')
    await expect(page.locator('html')).toHaveAttribute('data-storefront-theme', 'noor')
  })

  test('theme switcher is hidden on locked demo', async ({ page }) => {
    await page.goto('/')
    await expect(page.locator('.noor-theme-switch')).toHaveCount(0)
  })

  test('NOOR chrome renders after hydration', async ({ page }) => {
    await page.goto('/')
    await expect(page.locator('.noor-scroll-progress')).toHaveCount(1)
  })
})
