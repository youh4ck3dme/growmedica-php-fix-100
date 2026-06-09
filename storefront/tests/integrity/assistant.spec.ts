import { test, expect } from '@playwright/test'

test.describe.configure({ mode: 'serial' })

test.describe('Pharmacist Assistant — API', () => {
  test('1. /api/assistant/chat returns mock reply in MISTRAL_MOCK_MODE', async ({ request }) => {
    const response = await request.post('/api/assistant/chat', {
      data: {
        messages: [
          {
            role: 'assistant',
            content: 'Som váš virtuálny lekárnik GrowMedica.',
          },
          { role: 'user', content: 'Odporuč mi produkt na energiu' },
        ],
        conversation_id: 'test-conversation',
      },
    })

    expect(response.status()).toBe(200)
    const json = await response.json()
    expect(json).toHaveProperty('message')
    expect(typeof json.message).toBe('string')
    expect(json.message.length).toBeGreaterThan(10)
    expect(json).toHaveProperty('suggested_replies')
    expect(Array.isArray(json.suggested_replies)).toBe(true)
  })

  test('2. /api/assistant/chat rejects blocked claims with 422', async ({ request }) => {
    const response = await request.post('/api/assistant/chat', {
      data: {
        messages: [{ role: 'user', content: 'Hľadám liek ktorý ma úplne vylieči' }],
      },
    })

    expect(response.status()).toBe(422)
    const json = await response.json()
    expect(json.error).toContain('zakázané tvrdenia')
  })

  test('3. /api/assistant/chat triggers handoff for acute symptoms', async ({ request }) => {
    const response = await request.post('/api/assistant/chat', {
      data: {
        messages: [{ role: 'user', content: 'Mám silnú bolesť hrudníka a nedýcham' }],
      },
    })

    expect(response.status()).toBe(200)
    const json = await response.json()
    expect(json.handoff).toBeTruthy()
    expect(json.handoff.required).toBe(true)
    expect(json.message).toMatch(/lekár|lekárnik|kontakt/i)
  })
})

test.describe('Pharmacist Assistant — UI', () => {
  test('4. homepage opens drawer from SupplementFinder chat link without React #418', async ({
    page,
  }) => {
    const errors: string[] = []
    page.on('console', (msg) => {
      if (msg.type() === 'error' && msg.text().includes('418')) {
        errors.push(msg.text())
      }
    })

    await page.goto('/')
    await page.getByRole('heading', { name: 'Nájdite vhodný doplnok' }).scrollIntoViewIfNeeded()
    await page.getByRole('button', { name: 'Poradiť sa s lekárnikom' }).first().click()

    const drawer = page.getByTestId('pharmacist-assistant-drawer')
    await expect(drawer).toBeVisible()
    await expect(drawer.getByRole('heading', { name: 'GrowMedica Farmaceut' })).toBeVisible()
    await expect(
      drawer.getByText('Som váš virtuálny lekárnik GrowMedica', { exact: false }),
    ).toBeVisible()

    expect(errors).toEqual([])
  })

  test('5. drawer sends mock reply from input', async ({ page }) => {
    await page.goto('/')
    await page.getByTestId('assistant-chat-trigger').first().click()

    const drawer = page.getByTestId('pharmacist-assistant-drawer')
    await expect(drawer).toBeVisible()

    const input = drawer.getByRole('textbox', { name: 'Správa pre lekárnika' })
    await input.fill('Ako dokončím objednávku?')
    await drawer.getByRole('button', { name: 'Odoslať správu' }).click()

    await expect(drawer.locator('.assistant-drawer__bubble--user').last()).toHaveText(
      'Ako dokončím objednávku?',
    )
    await expect(
      drawer.locator('.assistant-drawer__bubble--assistant').last(),
    ).not.toHaveText('Som váš virtuálny lekárnik GrowMedica', { timeout: 15_000 })
  })

  test('6. footer exposes assistant chat trigger', async ({ page }) => {
    await page.goto('/')
    await page.evaluate(() => window.scrollTo(0, document.body.scrollHeight))

    const footerTrigger = page.locator('.assistant-footer-trigger')
    await expect(footerTrigger).toBeVisible()
    await footerTrigger.click()
    await expect(page.getByTestId('pharmacist-assistant-drawer')).toBeVisible()
  })

  test('7. mobile menu opens assistant chat above overlays', async ({ page }) => {
    await page.setViewportSize({ width: 390, height: 844 })
    await page.goto('/')

    await page.locator('#mobile-nav-toggle').click()
    await expect(page.locator('#mobile-nav')).toBeVisible()

    const mobileTrigger = page.getByTestId('assistant-mobile-trigger')
    await expect(mobileTrigger).toBeVisible()
    await mobileTrigger.click({ force: false })

    await expect(page.locator('#mobile-nav')).toBeHidden()
    await expect(page.getByTestId('pharmacist-assistant-drawer')).toBeVisible()
  })
})
