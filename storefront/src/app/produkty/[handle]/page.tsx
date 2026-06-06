import { notFound } from 'next/navigation'
import type { Metadata } from 'next'
import Image from 'next/image'
import Link from 'next/link'
import { Container } from '@/components/ui/Container'
import { Price } from '@/components/ui/Price'
import { Badge } from '@/components/ui/Badge'
import { getProductByHandle } from '@/lib/shopify/products'
import { getProductMetadata, getProductJsonLd, getBreadcrumbJsonLd } from '@/lib/seo'
import { sanitizeHtml } from '@/lib/utils'
import AddToCartButton from '@/components/product/AddToCartButton'
import VariantSelector from '@/components/product/VariantSelector'

export const revalidate = 3600

interface ProductPageProps {
  params: Promise<{ handle: string }>
}

export async function generateMetadata({ params }: ProductPageProps): Promise<Metadata> {
  const { handle } = await params
  const product = await getProductByHandle(handle)
  if (!product) return { title: 'Produkt nenájdený' }
  return getProductMetadata(product)
}

export default async function ProductDetailPage({ params }: ProductPageProps) {
  const { handle } = await params
  const product = await getProductByHandle(handle)

  if (!product) notFound()

  const p = product!

  const images = p.images.edges.map((e) => e.node)
  const mainImage = p.featuredImage ?? images[0]
  const firstVariant = p.variants.edges[0]?.node
  const siteUrl = process.env.NEXT_PUBLIC_SITE_URL ?? 'https://growmedica.nexify-studio.tech'

  const productJsonLd = getProductJsonLd(p)
  const breadcrumbJsonLd = getBreadcrumbJsonLd([
    { name: 'Domov', item: siteUrl },
    { name: 'Produkty', item: `${siteUrl}/produkty` },
    { name: p.title, item: `${siteUrl}/produkty/${p.handle}` },
  ])

  return (
    <>
      {/* Structured Data */}
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(productJsonLd) }}
      />
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(breadcrumbJsonLd) }}
      />

      <div className="py-8 lg:py-12">
        <Container>
          {/* Breadcrumb */}
          <nav aria-label="Breadcrumb" className="mb-6">
            <ol className="flex items-center gap-2 text-sm text-(--color-text-muted)">
              <li>
                <Link href="/" className="hover:text-(--color-primary) transition-colors">
                  Domov
                </Link>
              </li>
              <li aria-hidden="true" className="text-(--color-text-light)">/</li>
              <li>
                <Link href="/produkty" className="hover:text-(--color-primary) transition-colors">
                  Produkty
                </Link>
              </li>
              <li aria-hidden="true" className="text-(--color-text-light)">/</li>
              <li
                className="text-(--color-text) font-medium truncate max-w-48"
                aria-current="page"
              >
                {p.title}
              </li>
            </ol>
          </nav>

          <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-16">
            {/* ─── Gallery ─── */}
            <div className="space-y-3">
              <div className="relative aspect-square rounded-xl overflow-hidden bg-(--color-surface-2)">
                {mainImage ? (
                  <Image
                    src={mainImage.url}
                    alt={mainImage.altText ?? p.title}
                    fill
                    sizes="(max-width: 1024px) 100vw, 50vw"
                    className="object-cover"
                    priority
                  />
                ) : (
                  <div className="absolute inset-0 flex items-center justify-center">
                    <svg
                      className="h-20 w-20 text-(--color-text-light)"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                      aria-hidden="true"
                    >
                      <path
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeWidth={1}
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                      />
                    </svg>
                  </div>
                )}
              </div>

              {images.length > 1 && (
                <div className="grid grid-cols-4 gap-2" role="list" aria-label="Ďalšie fotky produktu">
                  {images.slice(0, 8).map((img, i) => (
                    <div
                      key={img.id ?? i}
                      role="listitem"
                      className="relative aspect-square rounded-lg overflow-hidden bg-(--color-surface-2) border border-(--color-border) cursor-pointer hover:border-(--color-primary) transition-colors"
                    >
                      <Image
                        src={img.url}
                        alt={img.altText ?? `${p.title} - fotka ${i + 1}`}
                        fill
                        sizes="80px"
                        className="object-cover"
                      />
                    </div>
                  ))}
                </div>
              )}
            </div>

            {/* ─── Product Info ─── */}
            <div className="space-y-6">
              <div>
                {p.vendor && (
                  <p className="text-sm font-medium text-(--color-primary) uppercase tracking-wide mb-2">
                    {p.vendor}
                  </p>
                )}
                <h1 className="text-2xl lg:text-3xl font-bold text-(--color-text) leading-tight text-balance mb-3">
                  {p.title}
                </h1>
                <div className="flex items-center gap-2 flex-wrap">
                  {p.availableForSale ? (
                    <Badge variant="success">Skladom</Badge>
                  ) : (
                    <Badge variant="error">Vypredané</Badge>
                  )}
                  {p.productType && (
                    <Badge variant="muted">{p.productType}</Badge>
                  )}
                </div>
              </div>

              <Price
                price={firstVariant?.price ?? p.priceRange.minVariantPrice}
                compareAtPrice={
                  firstVariant?.compareAtPrice ?? p.compareAtPriceRange.minVariantPrice
                }
                size="lg"
              />

              <VariantSelector product={p} />

              <AddToCartButton
                variants={p.variants.edges.map((e) => e.node)}
                availableForSale={p.availableForSale}
              />

              <ul className="space-y-2">
                {[
                  '✓ Overená kvalita a transparentné zloženie',
                  '✓ Doručenie do 24 hodín',
                  '✓ 30-dňová záruka vrátenia',
                ].map((benefit) => (
                  <li key={benefit} className="text-sm text-(--color-text-muted)">
                    {benefit}
                  </li>
                ))}
              </ul>

              {p.tags.length > 0 && (
                <div className="flex flex-wrap gap-2 pt-2 border-t border-(--color-border)">
                  {p.tags.map((tag) => (
                    <span key={tag} className="badge badge-muted text-xs">
                      {tag}
                    </span>
                  ))}
                </div>
              )}
            </div>
          </div>

          {p.descriptionHtml && (
            <section className="mt-12 max-w-2xl" aria-label="Popis produktu">
              <h2 className="text-xl font-bold text-(--color-text) mb-4">Popis produktu</h2>
              <div
                className="product-description"
                dangerouslySetInnerHTML={{ __html: sanitizeHtml(p.descriptionHtml) }}
              />
            </section>
          )}
        </Container>
      </div>
    </>
  )
}
