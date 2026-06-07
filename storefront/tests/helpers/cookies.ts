import { expect, type Page } from '@playwright/test'

export async function acceptCookies(page: Page) {
  const cookieButton = page.getByRole('button', { name: 'Prijať všetky' })
  try {
    if (await cookieButton.isVisible({ timeout: 2000 })) {
      await cookieButton.click()
      await expect(cookieButton).toBeHidden()
    }
  } catch {
    // Banner already dismissed or not shown
  }
}
