import Link from 'next/link'
import type { Metadata } from 'next'
import { Container } from '@/components/ui/Container'
import { ProductGrid } from '@/components/product/ProductGrid'
import { getNavCollectionItems } from '@/lib/shopify/collection-nav'
import { getFeaturedProducts } from '@/lib/shopify/products'
import { getHomepageCategories } from '@/lib/category-map'

export const revalidate = 3600

export const metadata: Metadata = {
  title: 'GrowMedica.sk — Prémiový medical e-shop',
  description:
    'Prémiové produkty pre zdravie a pohodu. Moderná, dôveryhodná značka pre doplnky výživy a zdravotné produkty na Slovensku.',
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
    title: 'RÝCHLOSŤ',
    subtitle: 'Rýchle doručenie',
    icon: (
      <svg className="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2} aria-hidden="true">
        <path strokeLinecap="round" strokeLinejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
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
      <div className="border-b border-(--color-border) bg-white py-3 lg:hidden">
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
        className="relative overflow-hidden py-12 lg:py-20 bg-white"
        aria-labelledby="hero-heading"
      >
        <div
          className="pointer-events-none absolute -right-20 top-0 h-72 w-72 rounded-full opacity-30 blur-3xl"
          style={{ background: 'var(--color-primary-light)' }}
          aria-hidden="true"
        />

        <Container>
          <div className="grid lg:grid-cols-2 gap-10 items-center">
            <div>
              <p className="section-label mb-3">Premium Medical E-shop</p>
              <h1
                id="hero-heading"
                className="text-3xl lg:text-4xl xl:text-5xl font-extrabold leading-tight text-balance mb-4 text-(--color-text)"
                style={{ fontFamily: 'Montserrat, sans-serif' }}
              >
                Starostlivosť o vaše zdravie
              </h1>
              <p className="text-base lg:text-lg leading-relaxed mb-8 text-(--color-text-muted) max-w-lg">
                Prémiové produkty pre zdravie a pohodu
              </p>
              <Link href="/produkty" id="hero-cta-primary" className="btn btn-primary btn-lg w-full sm:w-auto">
                Nakupovať
              </Link>
            </div>

            {/* Hero visual placeholder */}
            <div
              className="relative hidden sm:flex items-center justify-center rounded-2xl overflow-hidden aspect-4/3 lg:aspect-square"
              style={{ background: 'linear-gradient(135deg, #E7F8F2 0%, #FFFFFF 60%)' }}
              aria-hidden="true"
            >
              <div className="flex gap-4 items-end p-8">
                {[0, 1, 2].map((i) => (
                  <div
                    key={i}
                    className="w-16 lg:w-20 rounded-xl bg-white shadow-md flex flex-col items-center justify-end pb-3 pt-6 border border-(--color-border)"
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
      <section className="usp-bar" aria-label="Benefity">
        <Container>
          <div className="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4">
            {VALUE_PROPS.map((item) => (
              <div key={item.title} className="usp-card">
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
        className="py-12 lg:py-16 bg-(--color-bg)"
        aria-labelledby="categories-heading"
      >
        <Container>
          <div className="mb-8">
            <p className="section-label">Nakupujte podľa kategórie</p>
            <h2 id="categories-heading" className="section-heading">
              Čo hľadáte?
            </h2>
          </div>

          <div className="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3">
            {categories.map((cat) => (
              <Link
                key={cat.handle}
                href={cat.href}
                className="group flex flex-col items-center text-center p-4 bg-white rounded-xl border border-(--color-border) hover:border-(--color-primary) hover:bg-(--color-primary-light) hover:-translate-y-0.5 hover:shadow-md transition-all"
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
      <section
        className="py-12 lg:py-16 bg-(--color-surface-2)"
        aria-labelledby="featured-heading"
      >
        <Container>
          <div className="flex items-end justify-between mb-8">
            <div>
              <p className="section-label">Obľúbené produkty</p>
              <h2 id="featured-heading" className="section-heading">
                Najpredávanejšie produkty
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

          <ProductGrid products={featuredProducts} />

          <div className="mt-8 text-center sm:hidden">
            <Link href="/produkty" className="btn btn-primary">
              Zobraziť všetky produkty
            </Link>
          </div>
        </Container>
      </section>

      {/* About / SEO */}
      <section className="py-12 lg:py-16 bg-white" aria-label="O Growmedica">
        <Container>
          <div className="max-w-3xl mx-auto text-center">
            <p className="section-label" style={{ textAlign: 'center' }}>Prečo Growmedica</p>
            <h2 className="section-heading mb-4" style={{ textAlign: 'center' }}>
              Cesta za zdravím, vitalitou a kvalitným životom
            </h2>
            <p className="leading-relaxed text-(--color-text-muted)">
              Moderná, prémiová a dôveryhodná značka pre e-shop zameraný na zdravie, doplnky výživy
              a zdravotné produkty. Logo symbolizuje starostlivosť, rast a vitalitu. Značka spája
              lekársku presnosť s prírodnou rovnováhou.
            </p>
          </div>
        </Container>
      </section>
    </div>
  )
}
