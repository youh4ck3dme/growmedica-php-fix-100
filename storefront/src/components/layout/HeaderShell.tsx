import Header from '@/components/layout/Header'
import { getNavCollectionItems } from '@/lib/shopify/collection-nav'
import { getHeaderCategories, type MainCategory } from '@/lib/category-map'

export default async function HeaderShell() {
  let collections: Awaited<ReturnType<typeof getNavCollectionItems>> = []
  try {
    collections = await getNavCollectionItems()
  } catch {
    // Shopify not configured
  }

  const headerSlugs = new Set(getHeaderCategories().map((c) => c.slug as MainCategory))
  const withProducts = collections.filter((c) => c.productCount > 0)

  const headerCategories = withProducts
    .filter((c) => headerSlugs.has(c.handle as MainCategory))
    .map((c) => ({ href: c.href, label: c.menuLabel }))

  const moreCategories = withProducts
    .filter((c) => !headerSlugs.has(c.handle as MainCategory))
    .map((c) => ({ href: c.href, label: c.menuLabel }))

  return (
    <Header
      headerCategories={headerCategories}
      moreCategories={moreCategories}
    />
  )
}
