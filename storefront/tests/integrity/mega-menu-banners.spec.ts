import { test, expect } from '@playwright/test'
import fs from 'node:fs'
import path from 'node:path'
import { MEGA_MENU_BANNER_HANDLES } from '../../src/lib/mega-menu-banners'

const BANNERS_DIR = path.join(process.cwd(), 'public', 'images', 'mega-menu')

test.describe('Mega menu banners — static assets', () => {
  test('každý mapped handle má WebP súbor v public/images/mega-menu/', () => {
    for (const handle of MEGA_MENU_BANNER_HANDLES) {
      const filePath = path.join(BANNERS_DIR, `${handle}.webp`)
      expect(fs.existsSync(filePath), `${handle}.webp missing`).toBe(true)
    }
  })

  test('WebP bannery sú dostupné cez HTTP 200', async ({ request }) => {
    for (const handle of MEGA_MENU_BANNER_HANDLES) {
      const response = await request.get(`/images/mega-menu/${handle}.webp`)
      expect(response.status(), handle).toBe(200)
      expect(response.headers()['content-type']).toContain('image')
    }
  })
})

test.describe('Mega menu banners — UI', () => {
  test('mega menu: kategória s bannerom zobrazí hero WebP', async ({ page }) => {
    await page.setViewportSize({ width: 1280, height: 800 })
    await page.goto('/')

    await page.locator('#category-mega-menu-trigger').hover()
    await page.waitForTimeout(400)

    const panel = page.locator('#category-mega-menu-panel')
    await expect(panel).toBeVisible()

    await panel.getByRole('link', { name: /Vitamíny a minerály/i }).hover()
    await page.waitForTimeout(200)

    await expect(panel.locator('.mega-hero-banner--has-image')).toBeVisible()
    await expect(panel.locator('.mega-hero-banner-image')).toBeVisible()

    const src = await panel.locator('.mega-hero-banner-image').getAttribute('src')
    expect(decodeURIComponent(src ?? '')).toContain('/images/mega-menu/vitaminy-mineraly.webp')
  })

  test('mega menu: kategória bez banneru ostáva na gradient fallback', async ({ page }) => {
    await page.setViewportSize({ width: 1280, height: 800 })
    await page.goto('/')

    await page.locator('#category-mega-menu-trigger').hover()
    await page.waitForTimeout(400)

    const panel = page.locator('#category-mega-menu-panel')
    const spanokLink = panel.getByRole('link', { name: /Spánok a stres/i })
    await spanokLink.hover()
    await page.waitForTimeout(200)

    await expect(panel.locator('.mega-hero-banner--has-image')).toHaveCount(0)
    await expect(panel.locator('.mega-hero-title')).toContainText('Spánok a stres')
  })
})

test.describe('Console audit — extension noise', () => {
  test('homepage nemá app console errors mimo extension noise', async ({ page }) => {
    const messages: string[] = []
    page.on('console', (msg) => {
      if (msg.type() === 'error' || msg.type() === 'warning') {
        messages.push(msg.text())
      }
    })

    await page.setViewportSize({ width: 1280, height: 800 })
    await page.goto('/')
    await page.locator('#category-mega-menu-trigger').hover()
    await page.waitForTimeout(600)

    const appMessages = messages.filter(
      (text) =>
        !text.includes('contentscript.js') &&
        !text.includes('ObjectMultiplex') &&
        !text.includes('app-init-liveness') &&
        !text.includes('background-liveness') &&
        !text.includes('MaxListenersExceededWarning'),
    )

    expect(appMessages, `Unexpected app console noise: ${appMessages.join(' | ')}`).toEqual(
      [],
    )
  })
})
