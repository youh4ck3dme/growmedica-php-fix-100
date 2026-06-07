# Vercel deploy — GrowMedica Next.js storefront

## Required project setting

The Next.js app lives in `storefront/`, not the repo root.

In **Vercel → Project → Settings → General**:

| Setting | Value |
|---------|--------|
| **Root Directory** | `storefront` |
| **Framework Preset** | Next.js |
| **Package Manager** | Yarn |

Without **Root Directory = `storefront`**, Vercel builds the repo root (legacy PHP), finds no `next` in `package.json`, and fails.

## Environment variables

Set in Vercel (server-side, never commit real values):

```env
SHOPIFY_STORE_DOMAIN=
SHOPIFY_STOREFRONT_ACCESS_TOKEN=
SHOPIFY_REVALIDATION_SECRET=
SHOPIFY_API_VERSION=2025-01
NEXT_PUBLIC_SITE_URL=
```

Legacy `NEXT_PUBLIC_SHOPIFY_*` names are supported during migration but prefer `SHOPIFY_*`.

## Smoke test (after deploy)

Test against the **Vercel deployment URL** or configured Next.js domain — not `growmedica.sk` if that still points to legacy Apache PHP.

```bash
curl -I https://<your-vercel-domain>
curl -i -X POST "https://<your-vercel-domain>/api/revalidate" \
  -H "x-revalidation-secret: <secret>" \
  -H "Content-Type: application/json" \
  -d '{}'
```

Expected: `401` (wrong secret) or `200` (valid secret). A `302` from Apache means the wrong host.
