import { readFileSync } from 'node:fs'
import path from 'node:path'
import { test, expect } from '@playwright/test'

const swSourcePath = path.join(process.cwd(), 'src/app/sw.ts')

test.describe('PWA — manifest & offline', () => {
  test('manifest.webmanifest obsahuje id, scope a kategórie', async ({ request }) => {
    const response = await request.get('/manifest.webmanifest')
    expect(response.status()).toBe(200)
    const body = await response.json()
    expect(body.id).toBe('/')
    expect(body.scope).toBe('/')
    expect(body.lang).toBe('sk')
    expect(body.theme_color?.toUpperCase()).toBe('#35C79A')
    expect(body.categories).toContain('shopping')
    expect(body.icons?.length).toBeGreaterThanOrEqual(3)
  })

  test('manifest má oddelené purpose any a maskable ikony', async ({ request }) => {
    const response = await request.get('/manifest.webmanifest')
    const body = await response.json()
    const purposes = (body.icons as { purpose?: string }[]).map((icon) => icon.purpose)
    expect(purposes).toContain('any')
    expect(purposes).toContain('maskable')
  })

  test('/offline route je dostupná', async ({ request }) => {
    const response = await request.get('/offline')
    expect(response.status()).toBe(200)
    const html = await response.text()
    expect(html).toContain('Bez pripojenia')
  })

  test('/offline.html statická záloha je dostupná', async ({ request }) => {
    const response = await request.get('/offline.html')
    expect(response.status()).toBe(200)
    const html = await response.text()
    expect(html).toContain('Bez pripojenia')
  })

  test('HTML dokumenty majú DOCTYPE (žiadny Quirks Mode)', async ({ request }) => {
    for (const path of ['/', '/offline', '/offline.html', '/kosik']) {
      const response = await request.get(path)
      expect(response.status(), `${path} should return 200`).toBe(200)
      const html = await response.text()
      expect(html.startsWith('<!DOCTYPE html>'), `${path} missing DOCTYPE`).toBe(true)
      expect(html.charCodeAt(0), `${path} has UTF-8 BOM before DOCTYPE`).toBe(60) // '<'
    }
  })

  test('sw.js je dostupný po production build', async ({ request }) => {
    const response = await request.get('/sw.js')
    test.skip(
      response.status() === 404,
      'Service worker je v development vypnutý — over cez yarn test:pwa',
    )
    expect(response.status()).toBe(200)
    const contentType = response.headers()['content-type'] ?? ''
    expect(contentType).toMatch(/javascript/)
  })

  test('sw.ts používa NetworkFirst pre navigáciu a vypnutý navigationPreload', () => {
    const source = readFileSync(swSourcePath, 'utf-8')
    expect(source).toContain('navigationPreload: false')
    expect(source).toMatch(
      /matcher: \(\{ request, sameOrigin \}\) => sameOrigin && request\.mode === 'navigate',\s*handler: new NetworkFirst/,
    )
    expect(source).toContain("url: '/offline.html'")
    expect(source).toContain('setCatchHandler')
  })

  test('sw.js build nemá navigationPreload a navigate používa NetworkFirst', async ({
    request,
  }) => {
    const response = await request.get('/sw.js')
    test.skip(
      response.status() === 404,
      'Service worker je v development vypnutý — over cez yarn test:pwa',
    )
    const content = await response.text()
    expect(content).not.toContain('navigationPreload:!0')
    expect(content).toMatch(/"navigate"===\w\.mode\},handler:new X/)
  })
})
