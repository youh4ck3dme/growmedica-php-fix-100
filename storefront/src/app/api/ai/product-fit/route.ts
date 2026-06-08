import { type NextRequest, NextResponse } from 'next/server'
import { z } from 'zod'
import { callMistral } from '@/lib/ai/client'
import { SAFE_DISCLAIMER } from '@/lib/ai/compliance'
import { AiError } from '@/lib/ai/errors'
import { getClientIp } from '@/lib/ai/request'
import { productFitSchema } from '@/lib/ai/schemas'
import { getProductByHandle } from '@/lib/shopify/products'

const productFitInputSchema = z.object({
  handle: z.string().min(1),
  userContext: z.string().min(5).max(500),
})

const PRODUCT_FIT_PROMPT_SCHEMA = `
Vráť IBA JSON objekt s nasledujúcou štruktúrou:
{
  "fit": "good" | "maybe" | "not_recommended",
  "shortAnswer": "Krátka odpoveď (1-2 vety)",
  "bestFor": ["Skupina 1"],
  "notIdealFor": ["Skupina A"],
  "howToUse": "Stručný návod (max 2 vety)",
  "safeDisclaimer": "Disclaimer text"
}
PRAVIDLÁ:
- Nikdy netvrd, že produkt lieči alebo diagnostikuje.
- safeDisclaimer musí obsahovať: "${SAFE_DISCLAIMER}"
`

export async function POST(request: NextRequest) {
  try {
    const ip = getClientIp(request)
    const body = await request.json()
    const { handle, userContext } = productFitInputSchema.parse(body)

    const product = await getProductByHandle(handle)
    if (!product) {
      return NextResponse.json({ error: 'Produkt neexistuje.' }, { status: 404 })
    }

    const prompt = `
Si pomocník pre e-shop so zdravotnými doplnkami GrowMedica.sk (SK trh).
${PRODUCT_FIT_PROMPT_SCHEMA}

Produkt: ${JSON.stringify({
      handle: product.handle,
      title: product.title,
      vendor: product.vendor,
      productType: product.productType,
      tags: product.tags.slice(0, 8),
      description: product.description.slice(0, 500),
    })}
Používateľov kontext: ${JSON.stringify(userContext)}
`

    const output = await callMistral(prompt, productFitSchema, { ip, userInput: userContext })
    return NextResponse.json(output)
  } catch (error) {
    console.error('[AI Product-Fit] Error:', error)
    const message =
      error instanceof Error ? error.message : 'Nepodarilo sa posúdiť vhodnosť produktu.'
    const status =
      error instanceof AiError ? error.status : error instanceof z.ZodError ? 400 : 500
    return NextResponse.json({ error: message }, { status })
  }
}
