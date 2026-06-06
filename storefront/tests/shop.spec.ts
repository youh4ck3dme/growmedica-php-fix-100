import { test, expect, Page } from '@playwright/test';

// Pomocná funkcia na prijatie cookies
async function acceptCookies(page: Page) {
  const cookieButton = page.getByRole('button', { name: 'Prijať všetky' });
  try {
    if (await cookieButton.isVisible({ timeout: 2000 })) {
      await cookieButton.click();
      await expect(cookieButton).toBeHidden();
    }
  } catch {
    // Ak sa nezobrazí, pokračujeme
  }
}

test.describe('1. Domovská stránka (Homepage)', () => {
  test('1. Mal by načítať domovskú stránku a overiť hlavný nadpis v Hero sekcii', async ({ page }) => {
    await page.goto('/');
    const heroHeading = page.locator('h1');
    await expect(heroHeading).toBeVisible();
    await expect(heroHeading).toContainText('Prémiová výživa pre');
  });

  test('2. Mal by zobraziť logo a názov obchodu growmedica v hlavičke', async ({ page }) => {
    await page.goto('/');
    const logo = page.locator('#site-logo');
    await expect(logo).toBeVisible();
    await expect(logo).toContainText('growmedica');
  });

  test('3. Mal by zobraziť USP panel s benefitmi', async ({ page }) => {
    await page.goto('/');
    const uspBar = page.locator('.usp-bar');
    await expect(uspBar).toBeVisible();
    await expect(uspBar).toContainText('Doručenie do 24 hodín');
  });

  test('4. Mal by obsahovať hlavné navigačné odkazy na produkty a kolekcie', async ({ page }) => {
    await page.goto('/');
    const isMobile = await page.locator('#mobile-nav-toggle').isVisible();
    if (isMobile) {
      await expect(page.locator('#mobile-nav-toggle')).toBeVisible();
    } else {
      const nav = page.locator('nav[aria-label="Hlavná navigácia"]');
      await expect(nav).toBeVisible();
      await expect(nav.locator('a[href="/produkty"]')).toBeVisible();
      await expect(nav.locator('a[href="/kolekcie"]')).toBeVisible();
    }
  });

  test('5. Mal by obsahovať sekciu "Nakupujte podľa kategórie"', async ({ page }) => {
    await page.goto('/');
    const categoriesSection = page.locator('section[aria-labelledby="categories-heading"]');
    await expect(categoriesSection).toBeVisible();
    await expect(categoriesSection.locator('a[href="/kolekcie/balicky-zdravia"]')).toBeVisible();
  });

  test('6. Mal by obsahovať sekciu "Obľúbené produkty"', async ({ page }) => {
    await page.goto('/');
    const featuredHeading = page.locator('#featured-heading');
    await expect(featuredHeading).toBeVisible();
    await expect(featuredHeading).toContainText('Obľúbené produkty');
  });

  test('7. Mal by obsahovať sekciu "Prečo Growmedica" so SEO popisom', async ({ page }) => {
    await page.goto('/');
    const aboutSection = page.locator('section[aria-label="O Growmedica"]');
    await expect(aboutSection).toBeVisible();
    await expect(aboutSection.locator('h2')).toContainText('Cesta za zdravím');
  });

  test('8. Mal by obsahovať pätičku (Footer) s logami platobných možností', async ({ page }) => {
    await page.goto('/');
    const footer = page.locator('footer');
    await expect(footer).toBeVisible();
    await expect(footer.locator('text=VISA')).toBeVisible();
  });
});

test.describe('2. Navigácia a Statické Podstránky', () => {
  test('9. Mal by úspešne načítať podstránku "O nás"', async ({ page }) => {
    await page.goto('/o-nas');
    const heading = page.locator('h1');
    await expect(heading).toBeVisible();
    await expect(heading).toContainText('O spoločnosti Grow Medical');
  });

  test('10. Mal by úspešne načítať podstránku "Doprava a platba"', async ({ page }) => {
    await page.goto('/doprava-a-platba');
    const heading = page.locator('h1');
    await expect(heading).toBeVisible();
    await expect(heading).toContainText('Doprava a platba');
  });

  test('11. Mal by úspešne načítať podstránku "Veľkoobchod"', async ({ page }) => {
    await page.goto('/velkoobchod');
    const heading = page.locator('h1');
    await expect(heading).toBeVisible();
    await expect(heading).toContainText('Veľkoobchod');
  });

  test('12. Mal by úspešne načítať podstránku "Často kladené otázky (FAQ)"', async ({ page }) => {
    await page.goto('/faq');
    const heading = page.locator('h1');
    await expect(heading).toBeVisible();
    await expect(heading).toContainText('Často kladené otázky');
  });

  test('13. Mal by úspešne načítať Obchodné podmienky', async ({ page }) => {
    await page.goto('/obchodne-podmienky');
    const heading = page.locator('h1');
    await expect(heading).toBeVisible();
    await expect(heading).toContainText('Obchodné podmienky');
  });

  test('14. Mal by úspešne načítať Ochranu osobných údajov', async ({ page }) => {
    await page.goto('/ochrana-osobnych-udajov');
    const heading = page.locator('h1');
    await expect(heading).toBeVisible();
    await expect(heading).toContainText('Ochrana osobných údajov');
  });

  test('15. Mal by úspešne načítať Reklamačný poriadok', async ({ page }) => {
    await page.goto('/reklamacny-poriadok');
    const heading = page.locator('h1');
    await expect(heading).toBeVisible();
    await expect(heading).toContainText('Reklamačný poriadok');
  });

  test('16. Mal by načítať kontaktnú stránku a overiť jej hlavný nadpis a kontaktné údaje', async ({ page }) => {
    await page.goto('/kontakt');
    const heading = page.locator('h1');
    await expect(heading).toBeVisible();
    await expect(heading).toContainText('Kontakt');
    await expect(page.locator('main').locator('text=info@growmedica.sk').first()).toBeVisible();
  });

  test('17. Mal by odoslať kontaktný formulár a zobraziť potvrdzujúcu hlášku (alert)', async ({ page }) => {
    await page.goto('/kontakt');
    await acceptCookies(page);

    await page.getByPlaceholder('Jozef Novák').fill('Testovací Používateľ');
    await page.getByPlaceholder('jozef@email.sk').fill('test@email.sk');
    await page.getByPlaceholder('Dobrý deň, chcel by som sa opýtať...').fill('Ahoj, toto je testovacia správa.');

    page.once('dialog', async dialog => {
      expect(dialog.message()).toContain('Ďakujeme za vašu správu');
      await dialog.accept();
    });

    await page.getByRole('button', { name: 'Odoslať správu' }).click();
  });
});

test.describe('3. Produkty a Kolekcie', () => {
  test('18. Mal by načítať celkový zoznam produktov (/produkty) a zobraziť aspoň jeden produkt', async ({ page }) => {
    await page.goto('/produkty');
    const heading = page.locator('h1');
    await expect(heading).toBeVisible();
    await expect(heading).toContainText('Všetky produkty');
    
    const productCard = page.locator('article.product-card').first();
    await expect(productCard).toBeVisible({ timeout: 10000 });
  });

  test('19. Mal by načítať zoznam kolekcií (/kolekcie)', async ({ page }) => {
    await page.goto('/kolekcie');
    const heading = page.locator('h1');
    await expect(heading).toBeVisible();
    await expect(heading).toContainText('Kolekcie produktov');
  });

  test('20. Mal by úspešne načítať konkrétnu kolekciu (frontpage)', async ({ page }) => {
    await page.goto('/kolekcie/frontpage');
    const heading = page.locator('h1');
    await expect(heading).toBeVisible();
    await expect(heading).toContainText('Domovská stránka');
  });

  test('21. Mal by pri neexistujúcej alebo prázdnej kolekcii zobraziť prázdny stav (EmptyState)', async ({ page }) => {
    await page.goto('/kolekcie/neexistujuca-kolekcia');
    const emptyState = page.locator('text=Stránka nebola nájdená');
    await expect(emptyState).toBeVisible();
  });

  test('22. Mal by načítať detail konkrétneho produktu a zobraziť jeho názov, cenu a popis', async ({ page }) => {
    await page.goto('/produkty');
    await acceptCookies(page);
    
    const firstProduct = page.locator('article.product-card').first();
    const productTitle = await firstProduct.locator('h3').innerText();
    
    await firstProduct.locator('a.btn-primary').click();
    await expect(page).toHaveURL(/\/produkty\/.+/);
    
    const detailHeading = page.locator('h1');
    await expect(detailHeading).toBeVisible();
    await expect(detailHeading).toContainText(productTitle.substring(0, 10)); // Overenie zhody časti názvu
  });

  test('23. Mal by zobraziť stav zásob a výrobcu na detaile produktu', async ({ page }) => {
    await page.goto('/produkty');
    await acceptCookies(page);
    await page.locator('article.product-card').first().locator('a.btn-primary').click();
    
    const detailContainer = page.locator('div.space-y-6');
    await expect(detailContainer).toBeVisible();
    
    const badge = detailContainer.locator('.badge-success, .badge-error').first();
    await expect(badge).toBeVisible();
    await expect(badge).toHaveText(/(Skladom|Vypredané)/);
  });
});

test.describe('4. Vyhľadávanie', () => {
  test('24. Mal by načítať vyhľadávaciu stránku (/vyhladavanie) s formulárom', async ({ page }) => {
    await page.goto('/vyhladavanie');
    const heading = page.locator('h2');
    await expect(heading).toBeVisible();
    await expect(heading).toContainText('Zadajte hľadaný výraz');
    await expect(page.locator('input[type="search"]')).toBeVisible();
  });

  test('25. Mal by vyhľadať reálny produkt (napr. "BIO" alebo "Coriolus") a zobraziť výsledok', async ({ page }) => {
    await page.goto('/vyhladavanie');
    await acceptCookies(page);
    
    const searchInput = page.locator('input[type="search"]');
    await searchInput.fill('BIO');
    await searchInput.press('Enter');
    
    await expect(page).toHaveURL(/\/vyhladavanie\?q=BIO/);
    const productCard = page.locator('article.product-card').first();
    await expect(productCard).toBeVisible({ timeout: 10000 });
  });

  test('26. Mal by zobraziť správny prázdny stav pri vyhľadaní neexistujúceho výrazu', async ({ page }) => {
    await page.goto('/vyhladavanie');
    await acceptCookies(page);
    
    const searchInput = page.locator('input[type="search"]');
    await searchInput.fill('neexistujuci-vyraz-xyz');
    await searchInput.press('Enter');
    
    const emptyState = page.locator('text=Nič sme nenašli pre');
    await expect(emptyState).toBeVisible();
  });
});

test.describe('5. Košík a Nákupný Proces', () => {
  test('27. Mal by načítať prázdny košík a zobraziť informáciu, že je prázdny', async ({ page }) => {
    await page.goto('/kosik');
    const heading = page.locator('h2');
    await expect(heading).toBeVisible();
    await expect(heading).toContainText('Váš košík je prázdny');
  });

  test('28. Mal by na detaile produktu zobraziť funkčné tlačidlo "Pridať do košíka"', async ({ page }) => {
    await page.goto('/produkty');
    await acceptCookies(page);
    await page.locator('article.product-card').first().locator('a.btn-primary').click();
    
    const addToCartBtn = page.locator('#add-to-cart-btn');
    await expect(addToCartBtn).toBeVisible();
  });

  test('29. Mal by po kliknutí na "Pridať do košíka" aktualizovať počítadlo košíka v hlavičke', async ({ page }) => {
    await page.goto('/produkty');
    await acceptCookies(page);
    await page.locator('article.product-card').first().locator('a.btn-primary').click();
    
    const addToCartBtn = page.locator('#add-to-cart-btn');
    if (await addToCartBtn.isEnabled()) {
      await addToCartBtn.click();
      const cartIcon = page.locator('#cart-button');
      await expect(cartIcon).toContainText('1');
    }
  });

  test('30. Mal by pridať produkt do košíka, prejsť do košíka a zobraziť pridanú položku', async ({ page }) => {
    await page.goto('/produkty');
    await acceptCookies(page);
    
    const firstProduct = page.locator('article.product-card').first();
    const productTitle = await firstProduct.locator('h3').innerText();
    await firstProduct.locator('a.btn-primary').click();
    
    const addToCartBtn = page.locator('#add-to-cart-btn');
    if (await addToCartBtn.isEnabled()) {
      await addToCartBtn.click();
      
      // Počkáme, kým sa zmení stav v košíku v hlavičke
      const cartIcon = page.locator('#cart-button');
      await expect(cartIcon).toContainText('1');
      
      await page.goto('/kosik');
      
      const cartItemTitle = page.locator('a[href^="/produkty/"]').first();
      await expect(cartItemTitle).toBeVisible();
      await expect(cartItemTitle).toContainText(productTitle.substring(0, 10));
    }
  });

  test('31. Mal by v košíku zobraziť súhrn objednávky a tlačidlo pre prechod k pokladni (checkout)', async ({ page }) => {
    await page.goto('/produkty');
    await acceptCookies(page);
    await page.locator('article.product-card').first().locator('a.btn-primary').click();
    
    const addToCartBtn = page.locator('#add-to-cart-btn');
    if (await addToCartBtn.isEnabled()) {
      await addToCartBtn.click();
      
      // Počkáme, kým sa zmení stav v košíku v hlavičke
      const cartIcon = page.locator('#cart-button');
      await expect(cartIcon).toContainText('1');
      
      await page.goto('/kosik');
      
      const summaryHeading = page.locator('h2', { hasText: 'Súhrn objednávky' });
      await expect(summaryHeading).toBeVisible();
      
      const checkoutBtn = page.locator('#checkout-btn');
      await expect(checkoutBtn).toBeVisible();
    }
  });
});
