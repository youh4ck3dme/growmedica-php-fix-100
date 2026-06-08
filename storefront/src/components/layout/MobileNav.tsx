'use client'

import Link from 'next/link'
import { useEffect } from 'react'
import Logo from '@/components/ui/Logo'
import { StorefrontThemeSwitcher } from '@/components/theme/StorefrontThemeSwitcher'

interface MobileNavProps {
  isOpen: boolean
  onClose: () => void
  links: Array<{ href: string; label: string }>
}

export default function MobileNav({ isOpen, onClose, links }: MobileNavProps) {
  useEffect(() => {
    if (isOpen) {
      document.body.style.overflow = 'hidden'
    } else {
      document.body.style.overflow = ''
    }
    return () => { document.body.style.overflow = '' }
  }, [isOpen])

  if (!isOpen) return null

  return (
    <>
      <div
        className="fixed inset-0 z-40 bg-[#101615]/40 backdrop-blur-sm"
        onClick={onClose}
        aria-hidden="true"
      />

      <nav
        id="mobile-nav"
        className="fixed inset-y-0 left-0 z-50 flex w-72 flex-col bg-(--color-surface) shadow-xl"
        aria-label="Mobilná navigácia"
        role="dialog"
        aria-modal="true"
      >
        <div className="flex items-center justify-between border-b border-(--color-border) p-4">
          <Link href="/" onClick={onClose} aria-label="GrowMedica.sk — domov">
            <Logo iconSize={28} />
          </Link>

          <button
            onClick={onClose}
            className="btn btn-ghost p-2"
            aria-label="Zatvoriť menu"
          >
            <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <ul className="flex-1 overflow-y-auto p-4 space-y-1">
          {links.map((link) => (
            <li key={link.href}>
              <Link
                href={link.href}
                onClick={onClose}
                className="flex items-center px-3 py-3 text-base font-medium text-(--color-text) rounded-lg hover:bg-(--color-primary-light) hover:text-(--color-primary-dark) transition-colors"
                style={{ fontFamily: 'Montserrat, sans-serif' }}
              >
                {link.label}
              </Link>
            </li>
          ))}
        </ul>

        <div className="border-t border-(--color-border) p-4 space-y-3">
          <StorefrontThemeSwitcher compact />
          <p className="text-xs text-(--color-text-light)">© {new Date().getFullYear()} GrowMedica.sk</p>
        </div>
      </nav>
    </>
  )
}
