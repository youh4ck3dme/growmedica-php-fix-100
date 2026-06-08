import type { Metadata } from 'next'
import Link from 'next/link'
import { Container } from '@/components/ui/Container'
import { getNavCollectionItems } from '@/lib/shopify/collection-nav'
import { BRAND_COPY } from '@/lib/brand'
import { buildPageMetadata } from '@/lib/seo'

export const revalidate = 3600

export const metadata: Metadata = buildPageMetadata(
  'Kolekcie',
  BRAND_COPY.pageDescriptions.collections,
)

export default async function KolekciePage() {
  let collections: Awaited<ReturnType<typeof getNavCollectionItems>> = []
  try {
    collections = await getNavCollectionItems()
  } catch {
    // Shopify not configured
  }

  return (
    <div className="py-8 lg:py-12 bg-(--color-surface-2) min-h-[60vh]">
      <Container>
        <nav aria-label="Breadcrumb" className="mb-6">
          <ol className="flex items-center gap-2 text-sm text-(--color-text-muted)">
            <li>
              <Link href="/" className="hover:text-(--color-primary) transition-colors">
                Domov
              </Link>
            </li>
            <li aria-hidden="true">/</li>
            <li className="text-(--color-text) font-medium" aria-current="page">
              Kolekcie
            </li>
          </ol>
        </nav>

        <header className="mb-10 text-center max-w-2xl mx-auto">
          <h1 className="text-3xl lg:text-4xl font-bold text-(--color-text) mb-3">
            Kolekcie produktov
          </h1>
          <p className="text-(--color-text-muted)">
            Objavte našu ponuku organizovanú do prehľadných kategórií pre vaše zdravie a kondíciu.
          </p>
        </header>

        {collections.length === 0 ? (
          <div className="text-center py-12 bg-white rounded-xl border border-(--color-border)">
            <p className="text-(--color-text-muted)">Nenašli sa žiadne kolekcie.</p>
          </div>
        ) : (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {collections.map((collection) => (
              <Link
                key={collection.handle}
                href={collection.href}
                className="group flex h-full flex-col rounded-xl border border-(--color-border) bg-white p-6 shadow-sm transition-all hover:border-(--color-primary-light) hover:shadow-md"
                data-collection-handle={collection.handle}
              >
                <div className="flex flex-1 flex-col justify-between">
                  <div>
                    <span className="mb-4 inline-block badge badge-brand text-xs uppercase tracking-wide">
                      Kolekcia
                    </span>
                    <div className="mb-3 flex items-center justify-between gap-3">
                      <h2 className="text-xl font-bold text-(--color-text) transition-colors group-hover:text-(--color-primary)">
                        {collection.title}
                      </h2>
                      <span className="shrink-0 text-(--color-primary) opacity-0 transition-opacity group-hover:opacity-100 font-semibold">
                        →
                      </span>
                    </div>
                    {collection.description && (
                      <p className="text-sm text-(--color-text-muted) line-clamp-3 leading-relaxed">
                        {collection.description}
                      </p>
                    )}
                  </div>
                  <p className="mt-5 text-xs text-(--color-text-light)">
                    {collection.productCount}{' '}
                    {collection.productCount === 1
                      ? 'produkt'
                      : collection.productCount < 5
                        ? 'produkty'
                        : 'produktov'}
                  </p>
                </div>
              </Link>
            ))}
          </div>
        )}
      </Container>
    </div>
  )
}
