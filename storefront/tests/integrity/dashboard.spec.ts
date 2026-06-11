import { test, expect } from '@playwright/test'

const dashboardUrl = process.env.NEXT_PUBLIC_DASHBOARD_URL?.trim()
const isDashboardConfigured = Boolean(dashboardUrl)

test.describe('Dashboard route — smoke', () => {
  test('returns 200', async ({ request }) => {
    const response = await request.get('/dashboard')
    expect(response.status()).toBe(200)
  })

  test('skips shop chrome and deferred overlays', async ({ page }) => {
    await page.goto('/dashboard')
    await page.waitForTimeout(1_000)

    await expect(page.locator('#site-logo')).toHaveCount(0)
    await expect(page.locator('footer')).toHaveCount(0)
    await expect(page.locator('.usp-bar')).toHaveCount(0)
    await expect(page.getByRole('dialog', { name: 'Súhlas s cookies' })).toHaveCount(0)
    await expect(page.getByTestId('assistant-fab-trigger')).toHaveCount(0)
  })

  test('sends frame-src CSP for the dashboard iframe target', async ({ request }) => {
    const response = await request.get('/dashboard')
    expect(response.status()).toBe(200)

    const csp = response.headers()['content-security-policy'] ?? ''
    expect(csp).toContain("frame-src 'self'")

    if (dashboardUrl) {
      expect(csp).toContain(new URL(dashboardUrl).origin)
    }
  })

  test('robots.txt disallows /dashboard', async ({ request }) => {
    const response = await request.get('/robots.txt')
    expect(response.status()).toBe(200)
    const body = await response.text()
    expect(body).toContain('Disallow: /dashboard')
  })

  test('shows fallback when NEXT_PUBLIC_DASHBOARD_URL is missing', async ({ page }) => {
    test.skip(isDashboardConfigured, 'NEXT_PUBLIC_DASHBOARD_URL is set — fallback not expected')

    await page.goto('/dashboard')
    await expect(page.getByRole('heading', { level: 1 })).toContainText(
      'Dashboard nie je nakonfigurovaný',
    )
    await expect(page.locator('iframe[title="GrowMedica Dashboard"]')).toHaveCount(0)
  })

  test('renders iframe when NEXT_PUBLIC_DASHBOARD_URL is set', async ({ page }) => {
    test.skip(!isDashboardConfigured, 'Set NEXT_PUBLIC_DASHBOARD_URL to run iframe smoke')

    await page.goto('/dashboard')
    const iframe = page.locator('iframe[title="GrowMedica Dashboard"]')
    await expect(iframe).toBeVisible()
    await expect(iframe).toHaveAttribute('src', dashboardUrl!)
  })
})
