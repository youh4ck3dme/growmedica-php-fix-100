import Link from 'next/link'
import type { Metadata } from 'next'
import { Container } from '@/components/ui/Container'
import { ProductGrid } from '@/components/product/ProductGrid'
import { getProducts } from '@/lib/shopify/products'
import { BRAND_COPY } from '@/lib/brand'
import { buildPageMetadata } from '@/lib/seo'

export const revalidate = 3600

export const metadata: Metadata = buildPageMetadata(
  'Produkty',
  BRAND_COPY.pageDescriptions.products,
)

interface SearchParams {
  q?: string
  typ?: string
  zoradenie?: string
}

interface ProductsPageProps {
  searchParams: Promise<SearchParams>
}

const SORT_OPTIONS = [
  { value: 'BEST_SELLING', label: 'Najpredávanejšie', reverse: false },
  { value: 'PRICE', label: 'Cena: od najnižšej', reverse: false },
  { value: 'PRICE', label: 'Cena: od najvyššej', reverse: true },
  { value: 'CREATED_AT', label: 'Najnovšie', reverse: true },
  { value: 'TITLE', label: 'Abecedne A–Z', reverse: false },
] as const

export default async function ProduktyPage({ searchParams }: ProductsPageProps) {
  const params = await searchParams
  const query = params.q
  const sortValue = params.zoradenie ?? 'BEST_SELLING'
  const sortOption =
    SORT_OPTIONS.find((o) => o.value === sortValue) ?? SORT_OPTIONS[0]

  type ProductEdges = Awaited<ReturnType<typeof getProducts>>
  let productData: ProductEdges = { edges: [], pageInfo: { hasNextPage: false, hasPreviousPage: false, startCursor: null, endCursor: null } }
  try {
    productData = await getProducts({
      first: 48,
      query: query ?? undefined,
      sortKey: sortOption.value,
      reverse: sortOption.reverse,
    })
  } catch {
    // Shopify nie je nakonfigurovaný
  }

  const products = productData.edges.map((e) => e.node)

  return (
    <div className="py-8 lg:py-12">
      <Container>
        {/* Header */}
        <div className="mb-8">
          <h1 className="text-3xl font-bold text-(--color-text) mb-2">
            {query ? `Výsledky pre: "${query}"` : 'Všetky produkty'}
          </h1>
          <p className="text-(--color-text-muted)">
            {products.length} {products.length === 1 ? 'produkt' : products.length < 5 ? 'produkty' : 'produktov'}
          </p>
        </div>

        {/* Sorting (simple, no JS required for initial load) */}
        <div className="flex items-center gap-3 mb-6 flex-wrap">
          <span className="text-sm text-(--color-text-muted)">Zoradiť:</span>
          {SORT_OPTIONS.map((option, i) => (
            <Link
              key={i}
              href={`/produkty?zoradenie=${option.value}${option.reverse ? '&rev=1' : ''}${query ? `&q=${encodeURIComponent(query)}` : ''}`}
              className={`text-sm px-3 py-1.5 rounded-full border transition-colors ${
                sortValue === option.value
                  ? 'border-(--color-primary) bg-(--color-primary) text-white'
                  : 'border-(--color-border) text-(--color-text-muted) hover:border-(--color-primary) hover:text-(--color-primary)'
              }`}
            >
              {option.label}
            </Link>
          ))}
        </div>

        {/* Product Grid */}
        <ProductGrid
          products={products}
          emptyTitle="Žiadne produkty sa nenašli"
          emptyDescription={
            query
              ? `Pre hľadaný výraz "${query}" sme nenašli žiadne produkty.`
              : 'Momentálne tu nie sú žiadne produkty.'
          }
        />

        {/* Load more (TODO: cursor-based pagination) */}
        {productData.pageInfo.hasNextPage && (
          <div className="mt-12 text-center">
            <p className="text-sm text-(--color-text-muted) mb-4">
              Zobrazených {products.length} produktov
            </p>
            <Link href="/produkty?dalej=1" className="btn btn-secondary">
              Načítať ďalšie produkty
            </Link>
          </div>
        )}
      </Container>
    </div>
  )
}
