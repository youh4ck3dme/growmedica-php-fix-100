import type { Metadata } from 'next'
import Link from 'next/link'
import { Container } from '@/components/ui/Container'
import { getCollections } from '@/lib/shopify/collections'

export const revalidate = 3600

export const metadata: Metadata = {
  title: 'Kolekcie — Grow Medical',
  description: 'Prehliadajte naše kolekcie produktov. Vyberte si prémiové doplnky výživy podľa vašich potrieb.',
}

export default async function KolekciePage() {
  let collections: Awaited<ReturnType<typeof getCollections>> = []
  try {
    collections = await getCollections(100)
  } catch {
    // Shopify nie je nakonfigurovaný
  }

  return (
    <div className="py-8 lg:py-12 bg-[var(--color-surface-2)] min-h-[60vh]">
      <Container>
        {/* Breadcrumb */}
        <nav aria-label="Breadcrumb" className="mb-6">
          <ol className="flex items-center gap-2 text-sm text-[var(--color-text-muted)]">
            <li>
              <Link href="/" className="hover:text-[var(--color-primary)] transition-colors">
                Domov
              </Link>
            </li>
            <li aria-hidden="true">/</li>
            <li className="text-[var(--color-text)] font-medium" aria-current="page">
              Kolekcie
            </li>
          </ol>
        </nav>

        {/* Header */}
        <header className="mb-10 text-center max-w-2xl mx-auto">
          <h1 className="text-3xl lg:text-4xl font-bold text-[var(--color-text)] mb-3">
            Kolekcie produktov
          </h1>
          <p className="text-[var(--color-text-muted)]">
            Objavte našu ponuku organizovanú do prehľadných kategórií pre vaše zdravie a kondíciu.
          </p>
        </header>

        {/* Collections Grid */}
        {collections.length === 0 ? (
          <div className="text-center py-12 bg-white rounded-xl border border-[var(--color-border)]">
            <p className="text-[var(--color-text-muted)]">Nenašli sa žiadne kolekcie.</p>
          </div>
        ) : (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {collections.map((collection) => (
              <Link
                key={collection.id}
                href={`/kolekcie/${collection.handle}`}
                className="group flex flex-col justify-between bg-white rounded-xl p-6 border border-[var(--color-border)] hover:border-[var(--color-primary-light)] hover:shadow-md transition-all h-full"
              >
                <div>
                  <div className="flex items-center justify-between mb-4">
                    <span className="badge badge-brand text-xs uppercase tracking-wide">Kolekcia</span>
                    <span className="text-[var(--color-primary)] opacity-0 group-hover:opacity-100 transition-opacity font-semibold">
                      Zobraziť →
                    </span>
                  </div>
                  <h2 className="text-xl font-bold text-[var(--color-text)] group-hover:text-[var(--color-primary)] transition-colors mb-2">
                    {collection.title}
                  </h2>
                  {collection.description && (
                    <p className="text-sm text-[var(--color-text-muted)] line-clamp-3 leading-relaxed">
                      {collection.description}
                    </p>
                  )}
                </div>
              </Link>
            ))}
          </div>
        )}
      </Container>
    </div>
  )
}
