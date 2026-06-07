import { notFound } from 'next/navigation'
import type { Metadata } from 'next'
import { Container } from '@/components/ui/Container'
import { ProductGrid } from '@/components/product/ProductGrid'
import { getCollectionViewByHandle } from '@/lib/shopify/collection-nav'
import { getCollectionMetadata } from '@/lib/seo'
import Link from 'next/link'

export const revalidate = 3600

interface CollectionPageProps {
  params: Promise<{ handle: string }>
  searchParams: Promise<{ page?: string }>
}

export async function generateMetadata({ params }: CollectionPageProps): Promise<Metadata> {
  const { handle } = await params
  const view = await getCollectionViewByHandle(handle)
  if (!view) return { title: 'Kolekcia nenájdená' }

  return getCollectionMetadata({
    handle: view.handle,
    title: view.title,
    description: view.description ?? '',
    descriptionHtml: view.description ?? '',
    seo: { title: view.title, description: view.description ?? '' },
    updatedAt: new Date().toISOString(),
    image: null,
    id: view.handle,
  })
}

export default async function CollectionPage({ params, searchParams }: CollectionPageProps) {
  const { handle } = await params
  const { page: pageParam } = await searchParams
  const page = Math.max(1, parseInt(pageParam ?? '1', 10) || 1)

  let view: Awaited<ReturnType<typeof getCollectionViewByHandle>> = null

  try {
    view = await getCollectionViewByHandle(handle, page)
  } catch {
    notFound()
  }

  if (!view) notFound()

  return (
    <div className="py-8 lg:py-12">
      <Container>
        <nav aria-label="Breadcrumb" className="mb-6">
          <ol className="flex items-center gap-2 text-sm text-(--color-text-muted)">
            <li><Link href="/" className="hover:text-(--color-primary) transition-colors">Domov</Link></li>
            <li aria-hidden="true">/</li>
            <li><Link href="/kolekcie" className="hover:text-(--color-primary) transition-colors">Kolekcie</Link></li>
            <li aria-hidden="true">/</li>
            <li className="text-(--color-text) font-medium" aria-current="page">{view.title}</li>
          </ol>
        </nav>

        <header className="mb-8">
          <h1 className="text-3xl font-bold text-(--color-text) mb-2">{view.title}</h1>
          {view.description && (
            <p className="text-(--color-text-muted) max-w-2xl">{view.description}</p>
          )}
        </header>

        <ProductGrid
          products={view.products}
          emptyTitle="Táto kolekcia je prázdna"
          emptyDescription="Zatiaľ tu nie sú žiadne produkty."
        />

        {(view.hasPreviousPage || view.hasNextPage) && (
          <nav
            className="mt-10 flex items-center justify-center gap-4"
            aria-label="Stránkovanie kategórie"
          >
            {view.hasPreviousPage ? (
              <Link
                href={page > 2 ? `/kolekcie/${view.handle}?page=${page - 1}` : `/kolekcie/${view.handle}`}
                className="btn btn-secondary"
              >
                ← Predchádzajúca
              </Link>
            ) : (
              <span className="btn btn-secondary opacity-40 pointer-events-none" aria-hidden="true">
                ← Predchádzajúca
              </span>
            )}
            <span className="text-sm text-(--color-text-muted)">Strana {view.page}</span>
            {view.hasNextPage ? (
              <Link
                href={`/kolekcie/${view.handle}?page=${page + 1}`}
                className="btn btn-secondary"
              >
                Ďalšia →
              </Link>
            ) : (
              <span className="btn btn-secondary opacity-40 pointer-events-none" aria-hidden="true">
                Ďalšia →
              </span>
            )}
          </nav>
        )}
      </Container>
    </div>
  )
}
