import Image from 'next/image'
import { getMegaMenuBannerSrc } from '@/lib/mega-menu-banners'

interface CollectionHeroProps {
  handle: string
  title: string
  description: string | null
  productCount: number
}

export default function CollectionHero({
  handle,
  title,
  description,
  productCount,
}: CollectionHeroProps) {
  const bannerSrc = getMegaMenuBannerSrc(handle)
  const countLabel =
    productCount === 1
      ? '1 produkt'
      : productCount < 5
        ? `${productCount} produkty`
        : `${productCount} produktov`

  return (
    <header className="collection-hero mb-8 overflow-hidden rounded-xl border border-(--color-border)">
      <div className="relative min-h-[140px] lg:min-h-[180px]">
        {bannerSrc ? (
          <>
            <Image
              src={bannerSrc}
              alt=""
              fill
              priority
              sizes="(min-width: 1024px) 896px, 100vw"
              className="collection-hero-image object-cover object-center"
            />
            <div className="collection-hero-overlay absolute inset-0" aria-hidden="true" />
          </>
        ) : (
          <div
            className="absolute inset-0"
            style={{
              background:
                'linear-gradient(135deg, var(--color-primary-light) 0%, #C8EFE0 100%)',
            }}
            aria-hidden="true"
          />
        )}
        <div className="relative z-10 flex h-full min-h-[140px] flex-col justify-center p-6 lg:min-h-[180px] lg:p-8">
          <p className="mb-1 text-xs font-bold uppercase tracking-wider text-(--color-primary-dark)">
            {countLabel}
          </p>
          <h1 className="text-2xl font-bold text-(--color-text) lg:text-3xl">{title}</h1>
          {description && (
            <p className="mt-2 max-w-2xl text-sm leading-relaxed text-(--color-text-muted) lg:text-base">
              {description}
            </p>
          )}
        </div>
      </div>
    </header>
  )
}
