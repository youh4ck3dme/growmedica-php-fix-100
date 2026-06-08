'use client'

import Link from 'next/link'
import { useState, useEffect } from 'react'
import { Container } from '@/components/ui/Container'
import Logo from '@/components/ui/Logo'
import MobileNav from './MobileNav'
import HeaderMegaMenu, { type MegaMenuCategory } from './HeaderMegaMenu'
import { StorefrontThemeSwitcher } from '@/components/theme/StorefrontThemeSwitcher'
import { shouldHideThemeSwitcher } from '@/lib/theme/storefront-theme'

const BASE_NAV_LINKS = [
  { href: '/produkty', label: 'Produkty' },
  { href: '/o-nas', label: 'O nás' },
]

const MOBILE_EXTRA_LINKS = [{ href: '/vyhladavanie', label: 'Vyhľadávanie' }]

export interface NavLinkItem {
  href: string
  label: string
}

interface HeaderProps {
  megaMenuCategories?: MegaMenuCategory[]
}

export default function Header({ megaMenuCategories = [] }: HeaderProps) {
  const [mobileOpen, setMobileOpen] = useState(false)
  const [cartCount, setCartCount] = useState(0)
  const [scrolled, setScrolled] = useState(false)

  const mobileLinks: NavLinkItem[] = [
    ...megaMenuCategories.map((c) => ({ href: c.href, label: c.menuLabel })),
    { href: '/kolekcie', label: 'Všetky kategórie' },
    ...BASE_NAV_LINKS,
    ...MOBILE_EXTRA_LINKS,
  ]

  useEffect(() => {
    async function fetchCartCount() {
      try {
        const res = await fetch('/api/cart')
        if (res.ok) {
          const data = (await res.json()) as { count?: number }
          if (data.count !== undefined) setCartCount(data.count)
        }
      } catch {
        /* silent */
      }
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

  const navLinkClass =
    'px-3 py-2 text-sm font-semibold text-(--color-text-muted) hover:text-(--color-primary) transition-colors uppercase tracking-wider relative group whitespace-nowrap'

  const showThemeSwitcher = !shouldHideThemeSwitcher()

  return (
    <>
      <header
        data-site-header
        className="site-header sticky top-0 z-30 w-full bg-(--color-surface) transition-shadow duration-200 noor-header-shell"
        style={{
          boxShadow: scrolled ? 'var(--header-shadow-scrolled, 0 1px 12px rgba(16, 22, 21, 0.08))' : 'var(--header-shadow, 0 1px 0 var(--color-border))',
        }}
      >
        <Container>
          <div className="noor-header-grid flex h-[60px] items-center justify-between gap-4">
            <div className="noor-header-left flex items-center min-w-0">
              <button
                id="mobile-nav-toggle"
                className="p-2 lg:hidden text-(--color-text) hover:text-(--color-primary) transition-colors"
                onClick={() => setMobileOpen(true)}
                aria-label="Otvoriť menu"
                aria-expanded={mobileOpen}
              >
                <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M4 7h16M4 12h16M4 17h16" />
                </svg>
              </button>
            </div>

            <Link
              href="/"
              id="site-logo"
              className="noor-header-center shrink-0 site-logo-mark"
              aria-label="GrowMedica.sk — domov"
            >
              <Logo iconSize={32} />
            </Link>

            <nav className="noor-header-nav hidden lg:flex items-center gap-0 min-w-0 flex-1 justify-center" aria-label="Hlavná navigácia">
              <HeaderMegaMenu categories={megaMenuCategories} />

              {BASE_NAV_LINKS.map((link) => (
                <Link
                  key={link.href}
                  href={link.href}
                  className={navLinkClass}
                  style={{ fontFamily: 'Montserrat, sans-serif', letterSpacing: '0.06em', fontSize: '0.72rem' }}
                >
                  {link.label}
                  <span className="absolute bottom-0 left-3 right-3 h-0.5 bg-(--color-primary) scale-x-0 group-hover:scale-x-100 transition-transform origin-left" />
                </Link>
              ))}
            </nav>

            <div className="noor-header-right flex items-center gap-1 shrink-0">
              {showThemeSwitcher && <StorefrontThemeSwitcher />}
              <Link
                href="/vyhladavanie"
                id="search-button"
                className="p-2 text-(--color-text-muted) hover:text-(--color-primary) transition-colors rounded-lg"
                aria-label="Vyhľadávanie"
              >
                <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </Link>

              <Link
                href="/kosik"
                id="cart-button"
                className="p-2 text-(--color-text-muted) hover:text-(--color-primary) transition-colors rounded-lg relative"
                aria-label={`Košík${cartCount > 0 ? `, ${cartCount} položiek` : ''}`}
              >
                <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M16 11V7a4 4 0 00-8 0v4M5 9h14l-1 12H6L5 9z" />
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

      <MobileNav isOpen={mobileOpen} onClose={() => setMobileOpen(false)} links={mobileLinks} />
    </>
  )
}
