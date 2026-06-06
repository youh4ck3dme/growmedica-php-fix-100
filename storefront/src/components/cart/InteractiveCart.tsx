'use client'

import { useState } from 'react'
import Link from 'next/link'
import { Price } from '@/components/ui/Price'
import type { Cart } from '@/lib/shopify/types'

interface InteractiveCartProps {
  initialCart: Cart
}

export function InteractiveCart({ initialCart }: InteractiveCartProps) {
  const [cart, setCart] = useState<Cart>(initialCart)
  const [updatingLineId, setUpdatingLineId] = useState<string | null>(null)

  const lines = cart.lines.edges.map((e) => e.node) ?? []

  async function handleUpdateQuantity(lineId: string, currentQty: number, delta: number) {
    const newQty = currentQty + delta
    if (newQty < 1) return

    setUpdatingLineId(lineId)
    try {
      const res = await fetch('/api/cart', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ lineId, quantity: newQty }),
      })

      if (res.ok) {
        const data = (await res.json()) as { cart: Cart; count: number }
        setCart(data.cart)
        window.dispatchEvent(new CustomEvent('cart-count-updated', { detail: data.count }))
      }
    } catch (err) {
      console.error('Failed to update quantity', err)
    } finally {
      setUpdatingLineId(null)
    }
  }

  async function handleRemoveItem(lineId: string) {
    setUpdatingLineId(lineId)
    try {
      const res = await fetch(`/api/cart?lineId=${encodeURIComponent(lineId)}`, {
        method: 'DELETE',
      })

      if (res.ok) {
        const data = (await res.json()) as { cart: Cart; count: number }
        setCart(data.cart)
        window.dispatchEvent(new CustomEvent('cart-count-updated', { detail: data.count }))
      }
    } catch (err) {
      console.error('Failed to remove item', err)
    } finally {
      setUpdatingLineId(null)
    }
  }

  if (lines.length === 0) {
    return (
      <div className="flex flex-col items-center justify-center py-20 px-4 text-center">
        <div className="mb-6">
          <svg className="h-16 w-16 text-[var(--color-text-light)]" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1} d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
        </div>
        <h2 className="text-xl font-semibold text-[var(--color-text)] mb-2">Váš košík je prázdny</h2>
        <p className="text-[var(--color-text-muted)] max-w-md mb-6">Pridajte si produkty do košíka a pokračujte v nákupe.</p>
        <Link href="/produkty" className="btn btn-primary">
          Pokračovať v nákupe
        </Link>
      </div>
    )
  }

  return (
    <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
      {/* Cart lines */}
      <div className="lg:col-span-2 space-y-4">
        {lines.map((line) => {
          const isLineUpdating = updatingLineId === line.id
          return (
            <div
              key={line.id}
              className={`flex gap-4 p-4 bg-white rounded-xl border border-[var(--color-border)] transition-opacity duration-200 ${
                isLineUpdating ? 'opacity-50 pointer-events-none' : 'opacity-100'
              }`}
            >
              {/* Product image */}
              <div className="w-20 h-20 rounded-lg overflow-hidden bg-[var(--color-surface-2)] shrink-0 border border-[var(--color-border)]">
                {line.merchandise.product.featuredImage ? (
                  // eslint-disable-next-line @next/next/no-img-element
                  <img
                    src={line.merchandise.product.featuredImage.url}
                    alt={line.merchandise.product.featuredImage.altText ?? line.merchandise.product.title}
                    className="w-full h-full object-cover"
                  />
                ) : (
                  <div className="w-full h-full flex items-center justify-center text-[var(--color-text-muted)] text-xs">
                    Bez obrázku
                  </div>
                )}
              </div>

              {/* Info */}
              <div className="flex-1 min-w-0 flex flex-col justify-between">
                <div>
                  <Link
                    href={`/produkty/${line.merchandise.product.handle}`}
                    className="font-medium text-[var(--color-text)] hover:text-[var(--color-primary)] transition-colors block truncate"
                  >
                    {line.merchandise.product.title}
                  </Link>
                  <p className="text-sm text-[var(--color-text-muted)] mt-0.5">
                    {line.merchandise.selectedOptions
                      .filter((o) => o.name !== 'Title')
                      .map((o) => o.value)
                      .join(' · ')}
                  </p>
                </div>

                {/* Quantity Controls */}
                <div className="flex items-center gap-2 mt-2">
                  <div className="flex items-center border border-[var(--color-border)] rounded-lg bg-[var(--color-surface-2)]">
                    <button
                      type="button"
                      onClick={() => handleUpdateQuantity(line.id, line.quantity, -1)}
                      disabled={line.quantity <= 1}
                      className="px-2.5 py-1 text-sm font-semibold hover:text-[var(--color-primary)] disabled:opacity-30 disabled:hover:text-inherit transition-colors"
                      aria-label="Znížiť množstvo"
                    >
                      –
                    </button>
                    <span className="px-3 py-1 text-sm font-medium tabular-nums min-w-[2.5rem] text-center bg-white border-x border-[var(--color-border)]">
                      {line.quantity}
                    </span>
                    <button
                      type="button"
                      onClick={() => handleUpdateQuantity(line.id, line.quantity, 1)}
                      className="px-2.5 py-1 text-sm font-semibold hover:text-[var(--color-primary)] transition-colors"
                      aria-label="Zvýšiť množstvo"
                    >
                      +
                    </button>
                  </div>

                  <button
                    type="button"
                    onClick={() => handleRemoveItem(line.id)}
                    className="text-xs text-[var(--color-error)] hover:underline ml-3 font-medium"
                    aria-label="Odstrániť položku"
                  >
                    Odstrániť
                  </button>
                </div>
              </div>

              {/* Price */}
              <div className="text-right shrink-0 flex flex-col justify-between items-end">
                <p className="font-semibold text-[var(--color-text)]">
                  {line.cost.totalAmount.amount} {line.cost.totalAmount.currencyCode}
                </p>
              </div>
            </div>
          )
        })}
      </div>

      {/* Order summary */}
      <div className="lg:col-span-1">
        <div className="bg-white rounded-xl border border-[var(--color-border)] p-6 sticky top-24">
          <h2 className="font-semibold text-[var(--color-text)] text-lg mb-4">Súhrn objednávky</h2>

          <div className="space-y-2 mb-4">
            <div className="flex justify-between text-sm">
              <span className="text-[var(--color-text-muted)]">Medzisúčet</span>
              <span className="font-medium">
                {cart.cost.subtotalAmount.amount} {cart.cost.subtotalAmount.currencyCode}
              </span>
            </div>
            <div className="flex justify-between text-sm">
              <span className="text-[var(--color-text-muted)]">Doprava</span>
              <span className="text-[var(--color-success)] font-medium">Vypočíta sa pri pokladni</span>
            </div>
          </div>

          <div className="border-t border-[var(--color-border)] pt-4 mb-6">
            <div className="flex justify-between font-bold text-[var(--color-text)]">
              <span>Spolu</span>
              <span>
                {cart.cost.totalAmount.amount} {cart.cost.totalAmount.currencyCode}
              </span>
            </div>
          </div>

          {/* Checkout redirect */}
          {cart.checkoutUrl && (
            <a
              href={cart.checkoutUrl}
              id="checkout-btn"
              className="btn btn-primary btn-lg btn-full text-center"
              rel="noopener"
            >
              Prejsť k pokladni →
            </a>
          )}

          <Link href="/produkty" className="btn btn-ghost btn-full mt-3 text-center">
            Pokračovať v nákupe
          </Link>
        </div>
      </div>
    </div>
  )
}
