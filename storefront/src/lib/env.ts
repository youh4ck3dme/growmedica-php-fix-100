import { z } from 'zod'

function readShopifyStoreDomain(): string | undefined {
  return process.env.SHOPIFY_STORE_DOMAIN ?? process.env.NEXT_PUBLIC_SHOPIFY_STORE_DOMAIN
}

function readShopifyStorefrontToken(): string | undefined {
  return (
    process.env.SHOPIFY_STOREFRONT_ACCESS_TOKEN ??
    process.env.NEXT_PUBLIC_SHOPIFY_STOREFRONT_ACCESS_TOKEN
  )
}

const shopifyEnvSchema = z.object({
  SHOPIFY_STORE_DOMAIN: z
    .string()
    .min(1, 'SHOPIFY_STORE_DOMAIN is required')
    .refine((v) => v.endsWith('.myshopify.com'), {
      message: 'SHOPIFY_STORE_DOMAIN must end with .myshopify.com',
    }),
  SHOPIFY_STOREFRONT_ACCESS_TOKEN: z
    .string()
    .min(1, 'SHOPIFY_STOREFRONT_ACCESS_TOKEN is required'),
  SHOPIFY_API_VERSION: z
    .string()
    .regex(/^\d{4}-\d{2}$/, 'SHOPIFY_API_VERSION must match YYYY-MM'),
})

const revalidationSecretSchema = z
  .string()
  .min(16, 'SHOPIFY_REVALIDATION_SECRET must be at least 16 characters')
  .refine((v) => !v.startsWith('shpat_'), {
    message:
      'SHOPIFY_REVALIDATION_SECRET must be a custom webhook secret, not a Shopify Admin API token',
  })

type ShopifyEnv = z.infer<typeof shopifyEnvSchema>

function validateShopifyEnv(): ShopifyEnv {
  const result = shopifyEnvSchema.safeParse({
    SHOPIFY_STORE_DOMAIN: readShopifyStoreDomain(),
    SHOPIFY_STOREFRONT_ACCESS_TOKEN: readShopifyStorefrontToken(),
    SHOPIFY_API_VERSION: process.env.SHOPIFY_API_VERSION ?? '2025-01',
  })

  if (!result.success) {
    console.error('Invalid environment variables:')
    result.error.errors.forEach((err) => {
      console.error(`  → ${err.path.join('.')}: ${err.message}`)
    })
    throw new Error('Invalid environment variables. Copy .env.example to .env.local and fill in values.')
  }

  return result.data
}

/** Server-side Shopify Storefront API config (validated at import). */
export const env = validateShopifyEnv()

/** Webhook revalidation secret — validated when the revalidate route runs. */
export function getRevalidationSecret(): string {
  const result = revalidationSecretSchema.safeParse(process.env.SHOPIFY_REVALIDATION_SECRET)
  if (!result.success) {
    const message = result.error.errors.map((e) => `${e.path.join('.')}: ${e.message}`).join('; ')
    throw new Error(`Invalid SHOPIFY_REVALIDATION_SECRET: ${message}`)
  }
  return result.data
}
