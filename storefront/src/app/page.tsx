import Link from 'next/link'
import type { Metadata } from 'next'
import { Container } from '@/components/ui/Container'
import { ProductGrid } from '@/components/product/ProductGrid'
import { SupplementFinder } from '@/components/ai/SupplementFinder'
import { getNavCollectionItems } from '@/lib/shopify/collection-nav'
import { getFeaturedProducts } from '@/lib/shopify/products'
import { getHomepageCategories } from '@/lib/category-map'
import { BRAND_COPY } from '@/lib/brand'

export const revalidate = 3600

export const metadata: Metadata = {
  title: BRAND_COPY.siteTitle,
  description: BRAND_COPY.siteDescription,
}

const VALUE_PROPS = [
  {
    title: 'DÔVERYHODNOSŤ',
    subtitle: 'Bezpečný nákup',
    icon: (
      <svg className="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2} aria-hidden="true">
        <path strokeLinecap="round" strokeLinejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
      </svg>
    ),
  },
  {
    title: 'KVALITA',
    subtitle: 'Overené produkty',
    icon: (
      <svg className="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2} aria-hidden="true">
        <path strokeLinecap="round" strokeLinejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
      </svg>
    ),
  },
  {
    title: 'RAST',
    subtitle: 'Rastúca značka v regióne',
    icon: (
      <svg className="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2} aria-hidden="true">
        <path strokeLinecap="round" strokeLinejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
      </svg>
    ),
  },
  {
    title: 'PODPORA',
    subtitle: 'Sme tu pre vás',
    icon: (
      <svg className="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2} aria-hidden="true">
        <path strokeLinecap="round" strokeLinejoin="round" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
      </svg>
    ),
  },
]


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

      {/* Hero */}
      <section
        className="theme-transition noor-reveal noor-hero-section noor-premium-bg relative overflow-hidden py-8 lg:py-16 bg-(--color-surface)"
        aria-labelledby="hero-heading"
      >
        <div
          className="noor-hero-glow pointer-events-none absolute -right-20 top-0 h-72 w-72 rounded-full opacity-30 blur-3xl"
          style={{ background: 'var(--color-primary-light)' }}
          aria-hidden="true"
        />

        <Container>
          <div className="noor-hero-stack grid lg:grid-cols-2 gap-8 lg:gap-10 items-center">
            <div className="noor-hero-copy order-2 lg:order-1">
              <p className="section-label mb-3">{BRAND_COPY.heroEyebrow}</p>
              <h1
                id="hero-heading"
                className="noor-display-heading text-3xl lg:text-4xl xl:text-5xl font-extrabold leading-tight text-balance mb-4 text-(--color-text)"
              >
                {BRAND_COPY.heroTitle}
              </h1>
              <p className="text-base lg:text-lg leading-relaxed mb-8 text-(--color-text-muted) max-w-xl">
                <span className="sm:hidden">{BRAND_COPY.heroSubtitleShort}</span>
                <span className="hidden sm:inline">{BRAND_COPY.heroSubtitle}</span>
              </p>
              <Link href="/produkty" id="hero-cta-primary" className="btn btn-primary btn-lg noor-pill-cta w-full sm:w-auto">
                {BRAND_COPY.heroCta}
              </Link>
            </div>

            {/* Hero visual placeholder */}
            <div
              className="noor-hero-visual relative order-1 lg:order-2 flex items-center justify-center rounded-3xl overflow-hidden aspect-4/3 lg:aspect-square"
              style={{
                background:
                  'linear-gradient(135deg, var(--color-primary-light) 0%, var(--color-surface) 60%)',
              }}
              aria-hidden="true"
            >
              <div className="flex gap-4 items-end p-8">
                {[0, 1, 2].map((i) => (
                  <div
                    key={i}
                    className="noor-card theme-transition w-16 lg:w-20 flex flex-col items-center justify-end pb-3 pt-6"
                    style={{ height: `${120 + i * 24}px` }}
                  >
                    <div className="w-8 h-8 rounded-full mb-2" style={{ background: 'var(--color-primary-light)' }} />
                    <div className="w-10 h-1 rounded bg-(--color-primary) opacity-40" />
                  </div>
                ))}
              </div>
            </div>
          </div>
        </Container>
      </section>

      {/* Value props */}
      <section className="usp-bar theme-transition" aria-label="Benefity">
        <Container>
          <div className="noor-stagger grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4">
            {VALUE_PROPS.map((item) => (
              <div key={item.title} className="usp-card noor-card theme-transition">
                <div className="text-(--color-primary)">{item.icon}</div>
                <p className="font-bold text-xs tracking-wide text-(--color-text)" style={{ fontFamily: 'Montserrat, sans-serif' }}>
                  {item.title}
                </p>
                <p className="text-xs text-(--color-text-muted)">{item.subtitle}</p>
              </div>
            ))}
          </div>
        </Container>
      </section>

      {/* Categories */}
      <section
        className="noor-reveal theme-transition py-12 lg:py-16 bg-(--color-bg)"
        aria-labelledby="categories-heading"
      >
        <Container>
          <div className="mb-8">
            <p className="section-label">Nakupujte podľa kategórie</p>
            <h2 id="categories-heading" className="section-heading">
              Čo hľadáte?
            </h2>
          </div>

          <div className="noor-categories-grid grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3">
            {categories.map((cat) => (
              <Link
                key={cat.handle}
                href={cat.href}
                className="noor-card theme-transition group flex flex-col items-center text-center p-4 bg-(--color-surface) rounded-xl border border-(--color-border) hover:border-(--color-primary) hover:bg-(--color-primary-light) hover:-translate-y-0.5 hover:shadow-md transition-all"
              >
                {cat.icon && (
                  <span className="text-2xl mb-2" aria-hidden="true">{cat.icon}</span>
                )}
                <h3
                  className="text-xs font-bold leading-tight text-(--color-text) group-hover:text-(--color-primary-dark)"
                  style={{ fontFamily: 'Montserrat, sans-serif' }}
                >
                  {cat.title}
                </h3>
              </Link>
            ))}
          </div>

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
      </section>

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
