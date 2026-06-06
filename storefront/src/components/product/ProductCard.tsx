import Image from 'next/image'
import Link from 'next/link'
import type { ProductListItem } from '@/lib/shopify/types'
import { getProductUrl } from '@/lib/utils'
import { Price } from '@/components/ui/Price'

interface ProductCardProps {
  product: ProductListItem
  priority?: boolean
}

export function ProductCard({ product, priority = false }: ProductCardProps) {
  const image = product.featuredImage
  const firstVariant = product.variants.edges[0]?.node
  const price = firstVariant?.price ?? product.priceRange.minVariantPrice
  const compareAtPrice = firstVariant?.compareAtPrice ?? product.compareAtPriceRange.minVariantPrice

  const hasDiscount =
    compareAtPrice &&
    parseFloat(compareAtPrice.amount) > parseFloat(price.amount)

  const discountPct = hasDiscount
    ? Math.round((1 - parseFloat(price.amount) / parseFloat(compareAtPrice!.amount)) * 100)
    : 0

  return (
    <article className="product-card group" aria-label={product.title}>
      {/* Image area */}
      <Link
        href={getProductUrl(product.handle)}
        className="block relative"
        tabIndex={-1}
        aria-hidden="true"
      >
        <div className="relative aspect-square overflow-hidden bg-white">
          {image ? (
            <Image
              src={image.url}
              alt={image.altText ?? product.title}
              fill
              sizes="(max-width: 640px) 50vw, (max-width: 1024px) 33vw, 280px"
              className="object-contain p-3 transition-transform duration-300 group-hover:scale-105"
              priority={priority}
            />
          ) : (
            <div className="absolute inset-0 flex items-center justify-center bg-[var(--color-surface-2)]">
              <svg className="h-12 w-12 text-[var(--color-text-light)]" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1} d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
              </svg>
            </div>
          )}
        </div>

        {/* Badges overlay — top-left */}
        <div className="absolute top-2 left-2 flex flex-col gap-1">
          {!product.availableForSale && (
            <span className="badge badge-error">Vypredané</span>
          )}
          {hasDiscount && product.availableForSale && (
            <span className="badge badge-sale">Zľava {discountPct}%</span>
          )}
        </div>
      </Link>

      {/* Info */}
      <div className="p-3 flex flex-col flex-1 gap-1.5">
        {/* Vendor */}
        {product.vendor && (
          <p className="text-[0.7rem] text-[var(--color-text-muted)] font-semibold uppercase tracking-wider leading-none" style={{ fontFamily: 'Montserrat, sans-serif' }}>
            {product.vendor}
          </p>
        )}

        {/* Title */}
        <h3 className="text-sm font-semibold text-[var(--color-text)] leading-snug line-clamp-2 flex-1" style={{ fontFamily: 'Montserrat, sans-serif' }}>
          <Link
            href={getProductUrl(product.handle)}
            className="hover:text-[var(--color-primary)] transition-colors"
          >
            {product.title}
          </Link>
        </h3>

        {/* Stock status */}
        <p className="text-xs font-medium" style={{ color: product.availableForSale ? 'var(--color-accent-green-dark)' : 'var(--color-error)' }}>
          {product.availableForSale ? '✓ Skladom' : '✗ Vypredané'}
        </p>

        {/* Price */}
        <Price
          price={price}
          compareAtPrice={hasDiscount ? compareAtPrice : null}
          size="sm"
        />

        {/* CTA Button — navy, full width, matches old shop */}
        <Link
          href={getProductUrl(product.handle)}
          id={`product-cta-${product.handle}`}
          className="btn btn-primary btn-sm btn-full mt-1"
          aria-label={`Detail produktu: ${product.title}`}
        >
          Detail
        </Link>
      </div>
    </article>
  )
}
