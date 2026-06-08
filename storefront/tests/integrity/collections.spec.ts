import { test, expect } from '@playwright/test'

test.describe('Collections — catalog navigation', () => {
  test('/kolekcie vracia aspoň 10 kategórií s produktmi', async ({ request }) => {
    const response = await request.get('/kolekcie')
    expect(response.status()).toBe(200)
    const html = await response.text()

    expect(html).toContain('Kolekcie produktov')
    expect(html).not.toContain('/kolekcie/frontpage')
    expect(html).toContain('/kolekcie/vitaminy-mineraly')
    expect(html).toContain('/kolekcie/regeneracia')
    expect(html).toContain('/kolekcie/specialna-vyziva')
  })

  test('/kolekcie nezobrazuje bannerové fotky v kartách', async ({ request }) => {
    const response = await request.get('/kolekcie')
    expect(response.status()).toBe(200)
    const html = await response.text()

    expect(html).not.toContain('collection-card-banner-image')
    expect(html).not.toMatch(/data-banner-src="\/images\/mega-menu\/[^"]+\.webp"/)
    expect(html).toContain('data-collection-handle="vitaminy-mineraly"')
    expect(html).toContain('Kolekcia')
  })

  test('/kolekcie/vitaminy-mineraly zobrazí produkty z katalógu', async ({ request }) => {
    const response = await request.get('/kolekcie/vitaminy-mineraly')
    expect(response.status()).toBe(200)
    const html = await response.text()

    expect(html).toContain('Vitamíny a minerály')
    expect(html).toContain('product-card')
    expect(html).toContain('data-banner-src="/images/mega-menu/vitaminy-mineraly.webp"')
  })

  test('/kolekcie/regeneracia zobrazí produkty', async ({ request }) => {
    const response = await request.get('/kolekcie/regeneracia')
    expect(response.status()).toBe(200)
    const html = await response.text()

    expect(html).toContain('Regeneračné doplnky')
    expect(html).toContain('product-card')
    expect(html).toContain('data-banner-src="/images/mega-menu/regeneracia.webp"')
  })

  test('/kolekcie/frontpage vracia 404', async ({ request }) => {
    const response = await request.get('/kolekcie/frontpage')
    expect(response.status()).toBe(404)
  })

  test('legacy /kolekcia/:slug presmeruje na /kolekcie/:slug', async ({ request }) => {
    const response = await request.get('/kolekcia/vitaminy-mineraly', { maxRedirects: 0 })
    expect(response.status()).toBe(308)
    expect(response.headers()['location']).toBe('/kolekcie/vitaminy-mineraly')
  })

  test('legacy /kolekcie/doplnky-vyzivy presmeruje na vitaminy-mineraly', async ({ request }) => {
    const response = await request.get('/kolekcie/doplnky-vyzivy', { maxRedirects: 0 })
    expect(response.status()).toBe(308)
    expect(response.headers()['location']).toBe('/kolekcie/vitaminy-mineraly')
  })

  test('footer menu obsahuje nové kategórie', async ({ request }) => {
    const response = await request.get('/')
    expect(response.status()).toBe(200)
    const html = await response.text()

    expect(html).toContain('VITAMÍNY A MINERÁLY')
    expect(html).toContain('/kolekcie/vitaminy-mineraly')
    expect(html).not.toContain('/kolekcie/doplnky-vyzivy')
  })

  test('header obsahuje mega menu trigger a kategórie', async ({ request }) => {
    const response = await request.get('/')
    expect(response.status()).toBe(200)
    const html = await response.text()

    expect(html).toContain('/kolekcie/vitaminy-mineraly')
    expect(html).toContain('id="category-mega-menu-trigger"')
    expect(html).not.toContain('id="categories-dropdown-toggle"')
  })

  test('mega menu panel sa otvorí po hover intent', async ({ page }) => {
    await page.setViewportSize({ width: 1280, height: 800 })
    await page.goto('/')

    const trigger = page.locator('#category-mega-menu-trigger')
    await expect(trigger).toBeVisible()

    await trigger.hover()
    await page.waitForTimeout(400)

    const panel = page.locator('#category-mega-menu-panel')
    await expect(panel).toBeVisible()
    await expect(panel.getByRole('link', { name: /Vitamíny a minerály/i })).toBeVisible()
  })

  test('mega menu: každá kategória má ikonku', async ({ page }) => {
    await page.setViewportSize({ width: 1280, height: 800 })
    await page.goto('/')

    const trigger = page.locator('#category-mega-menu-trigger')
    await expect(trigger).toBeVisible()

    await trigger.hover()
    await page.waitForTimeout(400)

    const panel = page.locator('#category-mega-menu-panel')
    await expect(panel).toBeVisible()

    const items = page.locator('.mega-menu-list-item')
    const count = await items.count()
    expect(count).toBeGreaterThanOrEqual(14)

    for (let i = 0; i < count; i++) {
      const item = items.nth(i)
      const icon = item.locator('.mega-menu-list-icon')
      await expect(icon, `item ${i} missing icon`).toBeVisible()
      await expect(icon).not.toBeEmpty()
    }

    await items.first().hover()
    await page.waitForTimeout(200)
    await expect(panel.locator('.mega-hero-title')).toBeVisible()
  })

  test('mega menu: compact desktop panel height', async ({ page }) => {
    await page.setViewportSize({ width: 1280, height: 800 })
    await page.goto('/')

    await page.locator('#category-mega-menu-trigger').hover()
    await page.waitForTimeout(400)

    const panel = page.locator('#category-mega-menu-panel')
    await expect(panel).toBeVisible()

    const box = await panel.boundingBox()
    expect(box?.height).toBeLessThan(420)
  })

  test('mega menu: category list is scrollable on desktop', async ({ page }) => {
    await page.setViewportSize({ width: 1280, height: 800 })
    await page.goto('/')

    await page.locator('#category-mega-menu-trigger').hover()
    await page.waitForTimeout(400)

    const scrollList = page.locator('.mega-menu-list--scroll ul')
    await expect(scrollList).toBeVisible()

    const overflowY = await scrollList.evaluate((el) => getComputedStyle(el).overflowY)
    expect(overflowY).toBe('auto')

    const { scrollHeight, clientHeight } = await scrollList.evaluate((el) => ({
      scrollHeight: el.scrollHeight,
      clientHeight: el.clientHeight,
    }))
    expect(scrollHeight).toBeGreaterThan(clientHeight)
  })
})
