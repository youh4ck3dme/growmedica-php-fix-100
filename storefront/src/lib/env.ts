import { z } from 'zod'

const envSchema = z.object({
  NEXT_PUBLIC_SHOPIFY_STORE_DOMAIN: z
    .string()
    .min(1, 'NEXT_PUBLIC_SHOPIFY_STORE_DOMAIN is required')
    .refine((v) => v.endsWith('.myshopify.com'), {
      message: 'NEXT_PUBLIC_SHOPIFY_STORE_DOMAIN must end with .myshopify.com',
    }),
  NEXT_PUBLIC_SHOPIFY_STOREFRONT_ACCESS_TOKEN: z
    .string()
    .min(1, 'NEXT_PUBLIC_SHOPIFY_STOREFRONT_ACCESS_TOKEN is required'),
  SHOPIFY_REVALIDATION_SECRET: z
    .string()
    .min(16, 'SHOPIFY_REVALIDATION_SECRET must be at least 16 characters'),
})

type Env = z.infer<typeof envSchema>

function validateEnv(): Env {
  const result = envSchema.safeParse({
    NEXT_PUBLIC_SHOPIFY_STORE_DOMAIN: process.env.NEXT_PUBLIC_SHOPIFY_STORE_DOMAIN,
    NEXT_PUBLIC_SHOPIFY_STOREFRONT_ACCESS_TOKEN:
      process.env.NEXT_PUBLIC_SHOPIFY_STOREFRONT_ACCESS_TOKEN,
    SHOPIFY_REVALIDATION_SECRET: process.env.SHOPIFY_REVALIDATION_SECRET,
  })

  if (!result.success) {
    console.error('❌ Invalid environment variables:')
    result.error.errors.forEach((err) => {
      console.error(`  → ${err.path.join('.')}: ${err.message}`)
    })
    throw new Error('Invalid environment variables. Check .env.example for required variables.')
  }

  return result.data
}

export const env = validateEnv()
