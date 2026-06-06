'use client'

import Link from 'next/link'
import { useState, useEffect } from 'react'
import { Container } from '@/components/ui/Container'
import Logo from '@/components/ui/Logo'
import MobileNav from './MobileNav'

const NAV_LINKS = [
  { href: '/produkty', label: 'Produkty' },
  { href: '/kolekcie', label: 'Kolekcie' },
  { href: '/vyhladavanie', label: 'Vyhľadávanie' },
  { href: '/o-nas', label: 'O nás' },
]

export default function Header() {
  const [mobileOpen, setMobileOpen] = useState(false)
  const [cartCount, setCartCount] = useState(0)
  const [scrolled, setScrolled] = useState(false)

  useEffect(() => {
    async function fetchCartCount() {
      try {
        const res = await fetch('/api/cart')
        if (res.ok) {
          const data = await res.json() as { count?: number }
          if (data.count !== undefined) setCartCount(data.count)
        }
      } catch { /* silent */ }
    }
    fetchCartCount()

    function handleCartCountUpdate(e: Event) {
      setCartCount((e as CustomEvent<number>).detail)
    }
    window.addEventListener('cart-count-updated', handleCartCountUpdate)

    function handleScroll() {
      setScrolled(window.scrollY > 8)
    }
    window.addEventListener('scroll', handleScroll, { passive: true })

    return () => {
      window.removeEventListener('cart-count-updated', handleCartCountUpdate)
      window.removeEventListener('scroll', handleScroll)
    }
  }, [])

  return (
    <>
      <header
        className="sticky top-0 z-30 w-full bg-white transition-shadow duration-200"
        style={{
          boxShadow: scrolled ? '0 1px 12px rgba(16, 22, 21, 0.08)' : '0 1px 0 var(--color-border)',
        }}
      >
        <Container>
          <div className="flex h-[60px] items-center justify-between gap-4">
            <button
              id="mobile-nav-toggle"
              className="p-2 lg:hidden text-(--color-text) hover:text-(--color-primary) transition-colors"
              onClick={() => setMobileOpen(true)}
              aria-label="Otvoriť menu"
              aria-expanded={mobileOpen}
            >
              <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </button>

            <Link
              href="/"
              id="site-logo"
              className="shrink-0"
              aria-label="GrowMedica.sk — domov"
            >
              <Logo iconSize={32} />
            </Link>

            <nav className="hidden lg:flex items-center gap-0" aria-label="Hlavná navigácia">
              {NAV_LINKS.map((link) => (
                <Link
                  key={link.href}
                  href={link.href}
                  className="px-4 py-2 text-sm font-semibold text-(--color-text-muted) hover:text-(--color-primary) transition-colors uppercase tracking-wider relative group"
                  style={{ fontFamily: 'Montserrat, sans-serif', letterSpacing: '0.06em', fontSize: '0.78rem' }}
                >
                  {link.label}
                  <span className="absolute bottom-0 left-4 right-4 h-0.5 bg-(--color-primary) scale-x-0 group-hover:scale-x-100 transition-transform origin-left" />
                </Link>
              ))}
            </nav>

            <div className="flex items-center gap-1">
              <Link
                href="/vyhladavanie"
                id="search-button"
                className="p-2 text-(--color-text-muted) hover:text-(--color-primary) transition-colors rounded-lg"
                aria-label="Vyhľadávanie"
              >
                <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </Link>

              <Link
                href="/kosik"
                id="cart-button"
                className="p-2 text-(--color-text-muted) hover:text-(--color-primary) transition-colors rounded-lg relative"
                aria-label={`Košík${cartCount > 0 ? `, ${cartCount} položiek` : ''}`}
              >
                <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                {cartCount > 0 && (
                  <span
                    className="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full text-[10px] font-bold text-white bg-(--color-primary)"
                    aria-hidden="true"
                  >
                    {cartCount > 9 ? '9+' : cartCount}
                  </span>
                )}
              </Link>
            </div>
          </div>
        </Container>
      </header>

      <MobileNav
        isOpen={mobileOpen}
        onClose={() => setMobileOpen(false)}
        links={NAV_LINKS}
      />
    </>
  )
}
