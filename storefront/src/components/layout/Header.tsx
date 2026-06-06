'use client'

import Link from 'next/link'
import { useState, useEffect } from 'react'
import { Container } from '@/components/ui/Container'
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
        className="sticky top-0 z-30 w-full transition-shadow duration-200"
        style={{
          background: '#1E3A5F',
          boxShadow: scrolled ? '0 2px 12px rgba(30,58,95,0.25)' : 'none',
        }}
      >
        <Container>
          <div className="flex h-[60px] items-center justify-between gap-4">
            {/* Mobile: Hamburger */}
            <button
              id="mobile-nav-toggle"
              className="p-2 lg:hidden text-white/80 hover:text-white transition-colors"
              onClick={() => setMobileOpen(true)}
              aria-label="Otvoriť menu"
              aria-expanded={mobileOpen}
            >
              <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </button>

            {/* Logo */}
            <Link
              href="/"
              id="site-logo"
              className="flex items-center gap-2 flex-shrink-0"
              aria-label="Growmedica — domov"
            >
              {/* Leaf SVG + wordmark */}
              <svg width="32" height="32" viewBox="0 0 32 32" fill="none" aria-hidden="true">
                <circle cx="16" cy="16" r="16" fill="#6BAE2E" />
                {/* Leaf shape */}
                <path
                  d="M16 7C16 7 9 11 9 18C9 21.3137 12.134 24 16 24C19.866 24 23 21.3137 23 18C23 11 16 7 16 7Z"
                  fill="white"
                />
                <path
                  d="M16 24V14"
                  stroke="#6BAE2E"
                  strokeWidth="1.5"
                  strokeLinecap="round"
                />
                <path
                  d="M16 18C16 18 13 15 11 13"
                  stroke="#6BAE2E"
                  strokeWidth="1"
                  strokeLinecap="round"
                />
              </svg>
              <span className="font-montserrat font-800 text-lg leading-none" style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 800 }}>
                <span className="text-white">grow</span><span style={{ color: '#CBD5E0' }}>medica</span>
              </span>
            </Link>

            {/* Desktop Nav */}
            <nav className="hidden lg:flex items-center gap-0" aria-label="Hlavná navigácia">
              {NAV_LINKS.map((link) => (
                <Link
                  key={link.href}
                  href={link.href}
                  className="px-4 py-2 text-sm font-semibold text-white/80 hover:text-white hover:bg-white/10 transition-colors uppercase tracking-wider"
                  style={{ fontFamily: 'Montserrat, sans-serif', letterSpacing: '0.06em', fontSize: '0.78rem' }}
                >
                  {link.label}
                </Link>
              ))}
            </nav>

            {/* Right: Search + Cart */}
            <div className="flex items-center gap-1">
              {/* Search */}
              <Link
                href="/vyhladavanie"
                id="search-button"
                className="p-2 text-white/70 hover:text-white transition-colors rounded"
                aria-label="Vyhľadávanie"
              >
                <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </Link>

              {/* Cart */}
              <Link
                href="/kosik"
                id="cart-button"
                className="p-2 text-white/70 hover:text-white transition-colors rounded relative"
                aria-label={`Košík${cartCount > 0 ? `, ${cartCount} položiek` : ''}`}
              >
                <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                {cartCount > 0 && (
                  <span
                    className="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full text-[10px] font-bold text-white"
                    style={{ background: '#C53030' }}
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

      {/* Mobile Nav Drawer */}
      <MobileNav
        isOpen={mobileOpen}
        onClose={() => setMobileOpen(false)}
        links={NAV_LINKS}
      />
    </>
  )
}
