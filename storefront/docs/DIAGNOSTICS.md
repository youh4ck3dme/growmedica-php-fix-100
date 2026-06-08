# GrowMedica Storefront Diagnostics

Last updated: 2026-06-08

## Current storefront status

| Area | Status |
|------|--------|
| 14 mega menu WebP banners | PASS |
| Collection index WebP banners | PASS |
| Collection detail hero banners | PASS |
| Collection descriptions | PASS on PR branch |
| TypeScript | PASS |
| Integrity tests | PASS |
| Smoke test | PASS on PR branch |
| Vercel preview check | PASS |
| GitGuardian check | PASS |

## Live URLs

| Environment | URL | Notes |
|-------------|-----|-------|
| Production | https://growmedicanextjs.vercel.app | Main Vercel production app |
| Domain alias used in diagnostics | https://growmedica.nexify-studio.tech | Smoke-tested production-facing host |
| PR preview | Vercel-generated preview URL | May be protected by Vercel SSO for anonymous browser tests |

## Category / collection banner coverage

All 14 navigable categories map to `.webp` files in:

```text
storefront/public/images/mega-menu/
```

Expected files:

```text
aminokyseliny.webp
detox-pecen.webp
imunita.webp
klby-pohyb.webp
krasa-pokozka.webp
proteiny.webp
regeneracia.webp
spanok-stres.webp
specialna-vyziva.webp
sportova-vyziva.webp
srdce-cievy.webp
travenie.webp
vitaminy-mineraly.webp
zdrave-potraviny.webp
```

The route mapping is centralized in:

```text
storefront/src/lib/mega-menu-banners.ts
```

The category source of truth, including required titles and descriptions, is:

```text
storefront/src/lib/category-map.ts
```

## Validation commands

Run from `storefront/`:

```bash
yarn type-check
yarn test:integrity
SHOPIFY_MOCK_MODE=1 \
SHOPIFY_STORE_DOMAIN=mock-store.myshopify.com \
SHOPIFY_STOREFRONT_ACCESS_TOKEN=mock-storefront-token \
yarn build
```

Latest validated results:

```text
yarn type-check: PASS
yarn test:integrity: PASS — 67/67
SHOPIFY_MOCK_MODE=1 ... yarn build: PASS
```

## Shopify mock mode for tests

`yarn test:integrity` does not require real Shopify secrets.

Playwright starts the Next.js webServer with:

```env
SHOPIFY_MOCK_MODE=1
SHOPIFY_STORE_DOMAIN=mock-store.myshopify.com
SHOPIFY_STOREFRONT_ACCESS_TOKEN=mock-storefront-token
SHOPIFY_API_VERSION=2025-01
```

When `SHOPIFY_MOCK_MODE=1`, `src/lib/shopify/client.ts` returns deterministic
fixture responses from:

```text
storefront/src/lib/shopify/mock.ts
```

This covers:

- collection navigation
- collection detail pages
- product grids
- featured products
- mega menu featured products
- sitemap-related product/collection handles

Production behavior is unchanged unless `SHOPIFY_MOCK_MODE=1` is explicitly set.

## Smoke test summary

Smoke test coverage:

- `/`
- `/kolekcie`
- `/kolekcie/vitaminy-mineraly`
- `/produkty`
- `/vyhladavanie?q=vitamin`
- `/kontakt`
- homepage hero
- header and footer
- mega menu open state
- 14 category links in mega menu
- 14 collection cards
- visible `.webp` collection banners
- collection descriptions
- collection detail hero banner dimensions
- product grid presence
- browser page errors

Latest PR branch smoke result:

```text
18/18 PASS
```

Measured collection detail hero banner on desktop:

```text
~1216 x 322 px
```

Measured collection cards on desktop:

```text
~387 x 160 px banner area
```

## Production caveat from latest diagnostics

Before PR #14 was merged/deployed, production smoke against
`https://growmedica.nexify-studio.tech` returned:

```text
17/18 PASS
```

The failing check was collection card descriptions, because production still had
the pre-PR state. The current PR branch passed the same check locally:

```text
/kolekcie cards include descriptions: PASS
```

After PR #14 is merged and deployed to production, rerun the smoke test against
the production URL and expect:

```text
18/18 PASS
```

## Suggested AI/report response

```text
Smoke test prešiel na aktuálnom PR branchi: 18/18 kontrol úspešných.
Produkcia pred merge/deploy PR #14 ešte neobsahovala posledné popisy kolekcií,
preto tam smoke ukázal 17/18. Po merge/deploy PR #14 majú byť popisy aj WebP
bannery v kolekciách viditeľné správne.
```
