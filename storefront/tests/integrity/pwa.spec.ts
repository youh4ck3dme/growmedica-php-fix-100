import { test, expect } from '@playwright/test'

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
})
