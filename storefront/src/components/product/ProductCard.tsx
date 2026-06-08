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
    <article className="product-card noor-product-card group" aria-label={product.title}>
      <Link
        href={getProductUrl(product.handle)}
        className="block relative"
        tabIndex={-1}
        aria-hidden="true"
      >
        <div className="relative aspect-square noor-product-media overflow-hidden bg-white">
          {image ? (
            // eslint-disable-next-line @next/next/no-img-element
            <img
              src={image.url}
              alt={image.altText ?? product.title}
              loading={priority ? 'eager' : 'lazy'}
              decoding="async"
              className="h-full w-full object-contain p-3 transition-transform duration-300 group-hover:scale-105"
            />
          ) : (
            <div className="absolute inset-0 flex items-center justify-center bg-(--color-surface-2)">
              <svg className="h-12 w-12 text-(--color-text-light)" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1} d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
              </svg>
            </div>
          )}

          {/* Quick-add cart button */}
          <div className="absolute bottom-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
            <span className="btn-icon flex items-center justify-center shadow-md" aria-hidden="true">
              <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
            </span>
          </div>
        </div>

        <div className="absolute top-2 left-2 flex flex-col gap-1">
          {!product.availableForSale && (
            <span className="badge badge-error">Vypredané</span>
          )}
          {hasDiscount && product.availableForSale && (
            <span className="badge badge-sale">Zľava {discountPct}%</span>
          )}
        </div>
      </Link>

      <div className="p-3 flex flex-col flex-1 gap-1.5">
        {product.vendor && (
          <p className="text-[0.7rem] text-(--color-text-muted) font-semibold uppercase tracking-wider leading-none" style={{ fontFamily: 'Montserrat, sans-serif' }}>
            {product.vendor}
          </p>
        )}

        <h3 className="text-sm font-semibold text-(--color-text) leading-snug line-clamp-2 flex-1" style={{ fontFamily: 'Montserrat, sans-serif' }}>
          <Link
            href={getProductUrl(product.handle)}
            className="hover:text-(--color-primary) transition-colors"
          >
            {product.title}
          </Link>
        </h3>

        <p className="text-xs font-medium" style={{ color: product.availableForSale ? 'var(--color-primary-dark)' : 'var(--color-error)' }}>
          {product.availableForSale ? '✓ Skladom' : '✗ Vypredané'}
        </p>

        <Price
          price={price}
          compareAtPrice={hasDiscount ? compareAtPrice : null}
          size="sm"
        />

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
