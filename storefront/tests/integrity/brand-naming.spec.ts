import { test, expect } from '@playwright/test'

const PAGES_WITHOUT_LEGACY_NAME = ['/', '/o-nas', '/blog', '/faq', '/produkty', '/kontakt']

test.describe('Brand naming — GrowMedica.sk', () => {
  for (const path of PAGES_WITHOUT_LEGACY_NAME) {
    test(`${path} neobsahuje legacy názov Grow Medical`, async ({ request }) => {
      const response = await request.get(path)
      expect(response.status()).toBe(200)
      const html = await response.text()
      expect(html).not.toContain('Grow Medical')
    })
  }

  test('homepage obsahuje wordmark Medica', async ({ request }) => {
    const html = await (await request.get('/')).text()
    expect(html).toContain('Medica')
  })

  test('/o-nas obsahuje aktualizovaný H1', async ({ request }) => {
    const html = await (await request.get('/o-nas')).text()
    expect(html).toContain('O spoločnosti GrowMedica.sk')
  })

  test('homepage nemá trust strip', async ({ request }) => {
    const html = await (await request.get('/')).text()
    expect(html).not.toContain('class="trust-strip"')
  })

  test('/faq má trust strip', async ({ request }) => {
    const html = await (await request.get('/faq')).text()
    expect(html).toContain('class="trust-strip"')
    expect(html).toContain('Biomedicínske supplementy · Stredná Európa')
  })
})
