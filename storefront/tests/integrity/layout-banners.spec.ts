import { test, expect } from '@playwright/test'

test.describe('Deferred layout banners', () => {
  test('homepage mounts exactly one cookie consent dialog', async ({ page }) => {
    await page.goto('/')

    await expect(page.getByRole('dialog', { name: 'Súhlas s cookies' })).toHaveCount(1, {
      timeout: 2_000,
    })
  })
})
