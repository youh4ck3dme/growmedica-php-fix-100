import Link from 'next/link'
import type { Metadata } from 'next'
import { Container } from '@/components/ui/Container'
import { ProductGrid } from '@/components/product/ProductGrid'
import { getFeaturedProducts } from '@/lib/shopify/products'

export const revalidate = 3600

export const metadata: Metadata = {
  title: 'Growmedica — Doplnky výživy pre zdravie, šport a krásu',
  description:
    'Prémiové doplnky výživy, proteíny a zdravotné produkty. Odborná výživa pre aktívnych ľudí. Overená kvalita. Rýchle doručenie na Slovensku.',
}

const USP_ITEMS = [
  {
    icon: (
      <svg className="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
      </svg>
    ),
    text: 'Doprava zdarma nad 60 €',
  },
  {
    icon: (
      <svg className="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
      </svg>
    ),
    text: '1000+ produktov skladom',
  },
  {
    icon: (
      <svg className="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
    ),
    text: 'Doručenie do 24 hodín',
  },
  {
    icon: (
      <svg className="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
      </svg>
    ),
    text: '30-dňová záruka vrátenia',
  },
]

const CATEGORIES = [
  {
    title: 'Balíčky zdravia',
    desc: 'Kompletné balíky pre zdravie',
    href: '/kolekcie/balicky-zdravia',
    icon: '💊',
  },
  {
    title: 'Proteíny',
    desc: 'Whey, Plant-based, Kazeinát',
    href: '/kolekcie/proteiny',
    icon: '💪',
  },
  {
    title: 'Aminokyseliny',
    desc: 'BCAA, EAA, Glutamín',
    href: '/kolekcie/aminokyseliny',
    icon: '⚡',
  },
  {
    title: 'Vitamíny',
    desc: 'Komplex vitamínov a minerálov',
    href: '/kolekcie/vitaminy',
    icon: '🌿',
  },
  {
    title: 'Mykologické produkty',
    desc: 'Liečivé huby a extrakty',
    href: '/kolekcie/mykologicke-produkty',
    icon: '🍄',
  },
  {
    title: 'Zdravotné riešenia',
    desc: 'Kĺby, srdce, imunita',
    href: '/kolekcie/zdravotne-riesenia',
    icon: '❤️',
  },
  {
    title: 'Kosmetika',
    desc: 'Prírodná starostlivosť',
    href: '/kolekcie/kosmetika',
    icon: '✨',
  },
  {
    title: 'Pre zvieratá',
    desc: 'Zdravie pre vašich miláčikov',
    href: '/kolekcie/pre-zvierata',
    icon: '🐾',
  },
]

export default async function HomePage() {
  let featuredProducts: Awaited<ReturnType<typeof getFeaturedProducts>> = []
  try {
    featuredProducts = await getFeaturedProducts(8)
  } catch {
    // Shopify nie je nakonfigurovaný — zobrazíme prázdny stav
  }

  return (
    <div>
      {/* ─── USP Bar ───────────────────────────────────────────────────── */}
      <div className="usp-bar" aria-label="Benefity">
        <Container>
          <div className="flex items-center justify-center gap-6 lg:gap-12 flex-wrap">
            {USP_ITEMS.map((item) => (
              <div key={item.text} className="flex items-center gap-2">
                {item.icon}
                <span>{item.text}</span>
              </div>
            ))}
          </div>
        </Container>
      </div>

      {/* ─── Hero ──────────────────────────────────────────────────────── */}
      <section
        className="relative overflow-hidden py-20 lg:py-28"
        style={{
          background: 'linear-gradient(135deg, #1E3A5F 0%, #0F2240 60%, #1a3352 100%)',
        }}
        aria-labelledby="hero-heading"
      >
        {/* Decorative blobs */}
        <div
          className="pointer-events-none absolute -right-24 -top-24 h-80 w-80 rounded-full opacity-10 blur-3xl"
          style={{ background: '#6BAE2E' }}
          aria-hidden="true"
        />
        <div
          className="pointer-events-none absolute -left-16 bottom-0 h-64 w-64 rounded-full opacity-10 blur-3xl"
          style={{ background: '#D4920A' }}
          aria-hidden="true"
        />

        <Container>
          <div className="max-w-2xl">
            <p
              className="text-sm font-bold uppercase tracking-widest mb-4"
              style={{ color: '#9CE159', fontFamily: 'Montserrat, sans-serif' }}
            >
              Growmedica — výživa s výsledkami
            </p>
            <h1
              id="hero-heading"
              className="text-4xl lg:text-5xl xl:text-6xl font-extrabold leading-tight text-balance mb-6"
              style={{ fontFamily: 'Montserrat, sans-serif', color: 'white' }}
            >
              Prémiová výživa pre{' '}
              <span style={{ color: '#D4920A' }}>zdravie</span>,{' '}
              <span style={{ color: '#6BAE2E' }}>šport</span> a{' '}
              <span style={{ color: '#D4920A' }}>krásu</span>
            </h1>
            <p className="text-lg leading-relaxed mb-8 max-w-xl" style={{ color: '#CBD5E0' }}>
              U nás v Growmedica nájdete širokú ponuku produktov pre vaše zdravie,
              životný štýl a aktívny pohyb. Overená kvalita, transparentné zloženie.
            </p>
            <div className="flex flex-wrap gap-3">
              <Link
                href="/produkty"
                id="hero-cta-primary"
                className="btn btn-lg"
                style={{
                  background: '#6BAE2E',
                  color: 'white',
                  borderColor: '#6BAE2E',
                }}
              >
                Zobraziť produkty
              </Link>
              <Link
                href="/kolekcie"
                id="hero-cta-secondary"
                className="btn btn-lg"
                style={{
                  background: 'transparent',
                  color: 'white',
                  borderColor: 'rgba(255,255,255,0.4)',
                }}
              >
                Prehliadať kolekcie
              </Link>
            </div>
          </div>
        </Container>
      </section>

      {/* ─── Kategórie ─────────────────────────────────────────────────── */}
      <section
        className="py-12 lg:py-16"
        style={{ background: 'var(--color-bg)' }}
        aria-labelledby="categories-heading"
      >
        <Container>
          <div className="mb-8">
            <p className="section-label">Nakupujte podľa kategórie</p>
            <h2
              id="categories-heading"
              className="section-heading"
            >
              Čo hľadáte?
            </h2>
          </div>

          <div className="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3">
            {CATEGORIES.map((cat) => (
              <Link
                key={cat.href}
                href={cat.href}
                className="group flex flex-col items-center text-center p-4 bg-white rounded-lg border border-[var(--color-border)] hover:border-[var(--color-primary)] hover:-translate-y-0.5 hover:shadow-md transition-all"
              >
                <span className="text-2xl mb-2" aria-hidden="true">{cat.icon}</span>
                <h3
                  className="text-xs font-bold leading-tight"
                  style={{ fontFamily: 'Montserrat, sans-serif', color: 'var(--color-primary)' }}
                >
                  {cat.title}
                </h3>
              </Link>
            ))}
          </div>
        </Container>
      </section>

      {/* ─── Featured Products ──────────────────────────────────────────── */}
      <section
        className="py-12 lg:py-16"
        style={{ background: 'var(--color-surface-2)' }}
        aria-labelledby="featured-heading"
      >
        <Container>
          <div className="flex items-end justify-between mb-8">
            <div>
              <p className="section-label">Najpredávanejšie</p>
              <h2 id="featured-heading" className="section-heading">
                Obľúbené produkty
              </h2>
            </div>
            <Link
              href="/produkty"
              className="text-sm font-semibold hidden sm:block transition-colors"
              style={{ color: 'var(--color-primary)', fontFamily: 'Montserrat, sans-serif' }}
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

      {/* ─── About / SEO ───────────────────────────────────────────────── */}
      <section className="py-12 lg:py-16 bg-white" aria-label="O Growmedica">
        <Container>
          <div className="max-w-3xl mx-auto text-center">
            <p className="section-label" style={{ textAlign: 'center' }}>Prečo Growmedica</p>
            <h2 className="section-heading mb-4" style={{ textAlign: 'center' }}>
              Cesta za zdravím, vitalitou a kvalitným životom
            </h2>
            <p className="leading-relaxed" style={{ color: 'var(--color-text-muted)' }}>
              U nás v Growmedica nájdete širokú ponuku produktov pre vaše zdravie, životný štýl
              a aktívny pohyb. Naša ponuka je premyslene usporiadaná do kategórií, aby ste rýchlo
              a jednoducho našli to, čo potrebujete — či sú to vitamíny, minerály, proteíny,
              probiotiká a produkty pre vaše zdravie.
            </p>
          </div>
        </Container>
      </section>
    </div>
  )
}
