import { notFound } from 'next/navigation'
import type { Metadata } from 'next'
import { Container } from '@/components/ui/Container'
import { ProductGrid } from '@/components/product/ProductGrid'
import { getCollectionByHandle } from '@/lib/shopify/collections'
import { getCollectionMetadata } from '@/lib/seo'
import Link from 'next/link'

export const revalidate = 3600

interface CollectionPageProps {
  params: Promise<{ handle: string }>
}

export async function generateMetadata({ params }: CollectionPageProps): Promise<Metadata> {
  const { handle } = await params
  const collection = await getCollectionByHandle(handle)
  if (!collection) return { title: 'Kolekcia nenájdená' }
  return getCollectionMetadata(collection)
}

export default async function CollectionPage({ params }: CollectionPageProps) {
  const { handle } = await params
  const collection = await getCollectionByHandle(handle)

  if (!collection) notFound()

  const c = collection!
  const products = c.products.edges.map((e) => e.node)

  return (
    <div className="py-8 lg:py-12">
      <Container>
        {/* Breadcrumb */}
        <nav aria-label="Breadcrumb" className="mb-6">
          <ol className="flex items-center gap-2 text-sm text-[var(--color-text-muted)]">
            <li><Link href="/" className="hover:text-[var(--color-primary)] transition-colors">Domov</Link></li>
            <li aria-hidden="true">/</li>
            <li><Link href="/kolekcie" className="hover:text-[var(--color-primary)] transition-colors">Kolekcie</Link></li>
            <li aria-hidden="true">/</li>
            <li className="text-[var(--color-text)] font-medium" aria-current="page">{c.title}</li>
          </ol>
        </nav>

        {/* Header */}
        <header className="mb-8">
          <h1 className="text-3xl font-bold text-[var(--color-text)] mb-2">{c.title}</h1>
          {c.description && (
            <p className="text-[var(--color-text-muted)] max-w-2xl">{c.description}</p>
          )}
        </header>

        <ProductGrid
          products={products}
          emptyTitle="Táto kolekcia je prázdna"
          emptyDescription="Zatiaľ tu nie sú žiadne produkty."
        />
      </Container>
    </div>
  )
}
