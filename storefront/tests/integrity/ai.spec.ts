import { test, expect } from '@playwright/test'

test.describe.configure({ mode: 'serial' })

const delay = (ms: number) => new Promise((resolve) => setTimeout(resolve, ms))

test.describe('AI Integration — API Endpoints', () => {
  test('1. /api/ai/compliance-check by mal schváliť compliant text', async ({ request }) => {
    const response = await request.post('/api/ai/compliance-check', {
      data: { text: 'Tento produkt obsahuje bylinné extrakty a je určený na bežné doplnenie stravy.' },
    })
    expect(response.status()).toBe(200)
    const json = await response.json()
    expect(json).toHaveProperty('approved')
    expect(json).toHaveProperty('riskLevel')
    expect(json.approved).toBe(true)
    expect(json.riskLevel).toBe('low')
  })

  test('2. /api/ai/compliance-check by mal zablokovať non-compliant text', async ({ request }) => {
    await delay(4000)
    const response = await request.post('/api/ai/compliance-check', {
      data: { text: 'Tento produkt 100% vylieči vašu rakovinu a nahradí akéhokoľvek lekára.' },
    })
    expect(response.status()).toBe(200)
    const json = await response.json()
    expect(json.approved).toBe(false)
    expect(json.riskLevel).not.toBe('low')
    expect(json.blockedClaims.length).toBeGreaterThan(0)
  })

  test('3. /api/ai/recommend by mal odmietnuť zablokované slovo s 422', async ({ request }) => {
    const response = await request.post('/api/ai/recommend', {
      data: { userInput: 'Hľadám liek ktorý ma úplne vylieči' },
    })
    expect(response.status()).toBe(422)
    const json = await response.json()
    expect(json.error).toContain('obsahuje zakázané tvrdenia')
  })

  test('4. /api/ai/recommend by mal odmietnuť krátky vstup s 400', async ({ request }) => {
    const response = await request.post('/api/ai/recommend', {
      data: { userInput: 'krátke' },
    })
    expect(response.status()).toBe(400)
  })

  test('5. /api/ai/product-fit by mal overiť vhodnosť produktu s 200', async ({ request }) => {
    await delay(4000)
    const response = await request.post('/api/ai/product-fit', {
      data: {
        handle: 'mycomedica-cordyceps-50-90-rastlinnych-kapsul',
        userContext: 'Som bežec a hľadám energiu',
      },
    })
    expect(response.status()).toBe(200)
    const json = await response.json()
    expect(json).toHaveProperty('fit')
    expect(json).toHaveProperty('shortAnswer')
    expect(json).toHaveProperty('bestFor')
    expect(json).toHaveProperty('safeDisclaimer')
  })

  test('6. /api/ai/product-fit by mal vrátiť 404 pre neexistujúci produkt', async ({ request }) => {
    const response = await request.post('/api/ai/product-fit', {
      data: {
        handle: 'neexistujuci-produkt-xyz',
        userContext: 'Som bežec a hľadám energiu',
      },
    })
    expect(response.status()).toBe(404)
  })
})

test.describe('AI Integration — Premium Frontend & Animations', () => {
  test('1. SupplementFinder by mal obsahovať prémiovú animáciu a inline formulár', async ({ page }) => {
    await page.goto('/')

    // Skontrolujeme, či je nadpis vyhľadávača viditeľný
    const heading = page.locator('h2', { hasText: 'Nájdite vhodný doplnok' })
    await expect(heading).toBeVisible()

    // Skontrolujeme prítomnosť inline inputu a vyhľadávacieho tlačidla vo vnútri formy
    const form = page.locator('form', { has: page.locator('input[placeholder*="Popíšte svoje potreby"]') })
    await expect(form).toBeVisible()

    const input = form.locator('input[type="text"]')
    await expect(input).toBeVisible()

    const button = form.locator('button[type="submit"]')
    await expect(button).toBeVisible()
    await expect(button).toContainText('Nájsť doplnky')

    // ŠPECIÁLNY TEST NA ANIMÁCIU ("Rainbow Snake Border"):
    // Overíme prítomnosť CSS štýlov pre rotáciu na stránke pomocou evaluate, čo funguje spoľahlivo aj s React 19 stylesheet hoistingom
    const styleContent = await page.evaluate(() => {
      return Array.from(document.querySelectorAll('style'))
        .map((style) => style.textContent || '')
        .join('\n')
    })
    expect(styleContent).toContain('spin-gradient')
    expect(styleContent).toContain('.animate-spin-gradient')
    expect(styleContent).toContain('6s linear infinite normal')

    // Overíme, že v DOM-e existuje element s priradenou triedou pre animáciu rotácie
    const animator = page.locator('.animate-spin-gradient')
    await expect(animator).toBeAttached()

    // Overíme neon beam s blur efektom na cestujúcom gradiente
    const beam = page.locator('.animate-spin-gradient.blur-sm')
    await expect(beam).toBeAttached()
  })

  test('2. Detail produktu by mal správne renderovať ProductFitBox', async ({ page }) => {
    await page.goto('/produkty/mycomedica-cordyceps-50-90-rastlinnych-kapsul')

    // Skontrolujeme nadpis bloku ProductFitBox
    const heading = page.locator('h3', {
      hasText: 'Hodí sa vám produkt MycoMedica Cordyceps 50% 90 rastlinných kapsúl?',
    })
    await expect(heading).toBeVisible()

    // Skontrolujeme, či sa zobrazuje textarea pre popis cieľov/obáv
    const textarea = page.locator('textarea[placeholder*="Popíšte svoje ciele alebo obavy"]')
    await expect(textarea).toBeVisible()

    // Skontrolujeme overovacie tlačidlo
    const checkButton = page.locator('button', { hasText: 'Overiť vhodnosť pre mňa' })
    await expect(checkButton).toBeVisible()
    await expect(checkButton).toBeDisabled() // Tlačidlo by malo byť vypnuté pokiaľ je vstup prázdny
  })
})
