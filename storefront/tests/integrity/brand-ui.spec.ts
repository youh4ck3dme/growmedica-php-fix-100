import { test, expect } from '@playwright/test'
import { BRAND_COPY } from '../fixtures/brand'
import { cssUsesPrimaryToken, readGlobalsCss } from '../helpers/globals-css'
import { extractHtmlLang, extractMetaContent } from '../helpers/html'

test.describe('Brand UI — layout markup (SSR HTML)', () => {
  let html: string

  test.beforeAll(async ({ request }) => {
    const response = await request.get('/')
    expect(response.status()).toBe(200)
    html = await response.text()
  })

  test('header má biele pozadie (bg-white, nie navy)', () => {
    expect(html).toMatch(/<header[^>]*class="[^"]*bg-white[^"]*"/)
    expect(html).not.toMatch(/#1[Ee]3[Aa]5[Ff]/)
  })

  test('footer používa brand footer token', () => {
    expect(html).toMatch(/class="[^"]*\bsite-footer\b/)
  })

  test('logo a navigácia majú stabilné selektory', () => {
    expect(html).toContain('id="site-logo"')
    expect(html).toContain('id="cart-button"')
    expect(html).toContain('id="search-button"')
    expect(html).toMatch(/<footer[^>]*role="contentinfo"/)
  })

  test('logo wordmark GrowMedica.sk je v HTML', () => {
    for (const part of BRAND_COPY.logoParts) {
      expect(html).toContain(part)
    }
  })
})

test.describe('Brand UI — homepage copy & structure', () => {
  let html: string

  test.beforeAll(async ({ request }) => {
    const response = await request.get('/')
    html = await response.text()
  })

  test('hero nadpis a CTA zodpovedajú brand boardu', () => {
    expect(html).toContain(`id="hero-heading"`)
    expect(html).toContain(BRAND_COPY.heroTitle)
    expect(html).toContain(BRAND_COPY.heroSubtitle)
    expect(html).toContain(`id="hero-cta-primary"`)
    expect(html).toContain(BRAND_COPY.heroCta)
  })

  test('USP panel obsahuje všetky 4 value props', () => {
    expect(html).toMatch(/class="[^"]*\busp-bar\b/)
    for (const label of BRAND_COPY.valueProps) {
      expect(html).toContain(label)
    }
  })

  test('featured sekcia má správny nadpis', () => {
    expect(html).toContain('id="featured-heading"')
    expect(html).toContain(BRAND_COPY.featuredHeading)
  })
})

test.describe('Brand UI — CSS components', () => {
  test('.btn-primary používa teal token (nie hardcoded navy)', () => {
    const css = readGlobalsCss()
    expect(cssUsesPrimaryToken(css, '.btn-primary')).toBe(true)
    expect(css).not.toMatch(/\.btn-primary[\s\S]*?#1[Ee]3[Aa]5[Ff]/)
  })
})

test.describe('Brand UI — meta & accessibility', () => {
  test('theme-color meta je teal', async ({ request }) => {
    const html = await (await request.get('/')).text()
    const themeColor = extractMetaContent(html, 'theme-color')
    expect(themeColor?.toUpperCase()).toBe(BRAND_COPY.themeColor)
  })

  test('html lang je sk', async ({ request }) => {
    const html = await (await request.get('/')).text()
    expect(extractHtmlLang(html)).toBe('sk')
  })
})
