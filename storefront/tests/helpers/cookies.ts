import { expect, type Page } from '@playwright/test'

export async function acceptCookies(page: Page) {
  const cookieDialog = page.getByRole('dialog', { name: 'Súhlas s cookies' })
  try {
    await expect(cookieDialog).toBeVisible({ timeout: 2500 })
    await cookieDialog.getByRole('button', { name: 'Prijať všetky' }).click()
    await expect(cookieDialog).toBeHidden()
  } catch {
    // Banner already dismissed or not shown
  }
}
