import Link from 'next/link'
import type { Metadata } from 'next'
import { Container } from '@/components/ui/Container'
import { ProductGrid } from '@/components/product/ProductGrid'
import { SupplementFinder } from '@/components/ai/SupplementFinder'
import { HeroSlider, type HeroSlide } from '@/components/sections/HeroSlider'
import { TrustBadges } from '@/components/sections/TrustBadges'
import { ScrollRevealSection } from '@/components/sections/ScrollRevealSection'
import { CategoryGrid } from '@/components/collection/CategoryGrid'
import { getNavCollectionItems } from '@/lib/shopify/collection-nav'
import { getFeaturedProducts } from '@/lib/shopify/products'
import { getHomepageCategories } from '@/lib/category-map'
import { BRAND_COPY } from '@/lib/brand'
import type { ProductListItem } from '@/lib/shopify/types'

export const revalidate = 3600

export const metadata: Metadata = {
  title: BRAND_COPY.siteTitle,
  description: BRAND_COPY.siteDescription,
}

function buildHeroSlides(products: ProductListItem[]): HeroSlide[] {
  return products
    .filter((product) => product.featuredImage?.url)
    .slice(0, 5)
    .map((product) => ({
      id: product.id,
      imageUrl: product.featuredImage!.url,
      alt: product.featuredImage!.altText ?? product.title,
      width: product.featuredImage!.width ?? 1600,
      height: product.featuredImage!.height ?? 900,
    }))
}

export default async function HomePage() {
  let featuredProducts: Awaited<ReturnType<typeof getFeaturedProducts>> = []
  let allCategories: Awaited<ReturnType<typeof getNavCollectionItems>> = []
  try {
    ;[featuredProducts, allCategories] = await Promise.all([
      getFeaturedProducts(8),
      getNavCollectionItems(),
    ])
  } catch {
    // Shopify not configured
  }

  const categoriesByHandle = new Map(allCategories.map((c) => [c.handle, c]))
  const categories = getHomepageCategories()
    .map((def) => categoriesByHandle.get(def.slug))
    .filter((c): c is NonNullable<typeof c> => Boolean(c))

  const heroSlides = buildHeroSlides(featuredProducts)

  return (
    <div>
      {/* Search bar — mobile-first */}
      <div className="theme-transition noor-reveal noor-mobile-search border-b border-(--color-border) bg-(--color-surface) py-3 lg:hidden">
        <Container>
          <Link href="/vyhladavanie" className="search-pill no-underline">
            <svg className="h-5 w-5 shrink-0 text-(--color-primary)" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <span>Hľadať produkty...</span>
          </Link>
        </Container>
      </div>

      {/* Hero slider */}
      <ScrollRevealSection>
        <HeroSlider slides={heroSlides} />
      </ScrollRevealSection>

      <ScrollRevealSection>
        <TrustBadges />
      </ScrollRevealSection>

      {/* Categories */}
      <ScrollRevealSection
        as="section"
        className="theme-transition py-12 lg:py-16 bg-(--color-bg)"
        aria-labelledby="categories-heading"
      >
        <Container>
          <div className="mb-8">
            <p className="section-label">Nakupujte podľa kategórie</p>
            <h2 id="categories-heading" className="section-heading">
              Čo hľadáte?
            </h2>
          </div>

          <CategoryGrid categories={categories} />

          <div className="mt-6 text-center">
            <Link
              href="/kolekcie"
              className="text-sm font-semibold text-(--color-primary) hover:text-(--color-primary-dark) transition-colors"
              style={{ fontFamily: 'Montserrat, sans-serif' }}
            >
              Všetky kategórie →
            </Link>
          </div>
        </Container>
      </ScrollRevealSection>

      {/* AI supplement finder */}
      <div className="noor-reveal noor-glass theme-transition bg-(--color-surface) border-y border-(--color-border)">
        <Container>
          <SupplementFinder />
        </Container>
      </div>

      <section
        className="noor-reveal noor-featured-section theme-transition py-12 lg:py-16 bg-(--color-surface-2)"
        aria-labelledby="featured-heading"
      >
        <Container>
          <div className="flex items-end justify-between mb-8">
            <div>
              <p className="section-label">Obľúbené produkty</p>
              <h2 id="featured-heading" className="section-heading noor-display-heading">
                {BRAND_COPY.featuredHeading}
              </h2>
            </div>
            <Link
              href="/produkty"
              className="text-sm font-semibold hidden sm:block transition-colors text-(--color-primary) hover:text-(--color-primary-dark)"
              style={{ fontFamily: 'Montserrat, sans-serif' }}
            >
              Zobraziť všetky →
            </Link>
          </div>

          <div className="noor-stagger noor-featured-rail">
            <ProductGrid products={featuredProducts} />
          </div>
          <div className="noor-carousel-track mt-4 lg:hidden" aria-hidden="true">
            <div className="noor-carousel-track__fill" />
          </div>

          <div className="mt-8 text-center sm:hidden">
            <Link href="/produkty" className="btn btn-primary">
              Zobraziť všetky produkty
            </Link>
          </div>
        </Container>
      </section>

      {/* About / SEO */}
      <section className="noor-reveal noor-about-block theme-transition py-12 lg:py-16 bg-(--color-surface)" aria-label="O Growmedica">
        <Container>
          <div className="noor-about-inner max-w-3xl mx-auto text-center">
            <p className="section-label" style={{ textAlign: 'center' }}>{BRAND_COPY.aboutLabel}</p>
            <h2 className="section-heading noor-display-heading mb-4" style={{ textAlign: 'center' }}>
              {BRAND_COPY.aboutHeading}
            </h2>
            <p className="leading-relaxed text-(--color-text-muted)">
              {BRAND_COPY.aboutBody}
            </p>
          </div>
        </Container>
      </section>
    </div>
  )
}
