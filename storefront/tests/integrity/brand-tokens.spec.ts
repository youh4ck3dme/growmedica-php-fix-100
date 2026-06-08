import { test, expect } from '@playwright/test'
import { BRAND_COLORS, LEGACY_COLORS, REQUIRED_CSS_VARS } from '../fixtures/brand'
import { getCssVarFromSource, readGlobalsCss } from '../helpers/globals-css'

test.describe('Brand tokens — CSS source (globals.css)', () => {
  let css: string

  test.beforeAll(() => {
    css = readGlobalsCss()
  })

  for (const varName of REQUIRED_CSS_VARS) {
    test(`globals.css definuje ${varName}`, () => {
      const value = getCssVarFromSource(css, varName)
      expect(value).toBeTruthy()
    })
  }

  test('--color-primary je AA-safe green #166534', () => {
    const value = getCssVarFromSource(css, '--color-primary')
    expect(value?.toUpperCase()).toBe(BRAND_COLORS.primary)
  })

  test('--color-primary-bright je brand teal #35C79A', () => {
    const value = getCssVarFromSource(css, '--color-primary-bright')
    expect(value?.toUpperCase()).toBe(BRAND_COLORS.primaryBright)
  })

  test('--color-footer-bg je charcoal #101615', () => {
    const value = getCssVarFromSource(css, '--color-footer-bg')
    expect(value?.toUpperCase()).toBe(BRAND_COLORS.footerBg)
  })

  test('globals.css neobsahuje legacy navy paletu', () => {
    const lower = css.toLowerCase()
    expect(lower).not.toContain(LEGACY_COLORS.navy.toLowerCase())
    expect(lower).not.toContain(LEGACY_COLORS.navyDark.toLowerCase())
  })
})

test.describe('Brand tokens — rendered HTML', () => {
  test('homepage neobsahuje legacy navy v HTML', async ({ request }) => {
    const response = await request.get('/')
    expect(response.status()).toBe(200)
    const html = (await response.text()).toLowerCase()
    expect(html).not.toContain(LEGACY_COLORS.navy.toLowerCase())
    expect(html).not.toContain(LEGACY_COLORS.navyDark.toLowerCase())
  })
})
