'use client'

import Link from 'next/link'
import { useEffect } from 'react'

interface MobileNavProps {
  isOpen: boolean
  onClose: () => void
  links: Array<{ href: string; label: string }>
}

export default function MobileNav({ isOpen, onClose, links }: MobileNavProps) {
  // Lock body scroll when open
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
      {/* Overlay */}
      <div
        className="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-sm"
        onClick={onClose}
        aria-hidden="true"
      />

      {/* Drawer */}
      <nav
        id="mobile-nav"
        className="fixed inset-y-0 left-0 z-50 flex w-72 flex-col bg-white shadow-xl"
        aria-label="Mobilná navigácia"
        role="dialog"
        aria-modal="true"
      >
        {/* Header */}
        <div className="flex items-center justify-between border-b border-[var(--color-border)] p-4">
          <Link
            href="/"
            className="flex items-center gap-2 font-bold text-[var(--color-primary)] text-lg"
            onClick={onClose}
          >
            <div
              className="flex h-7 w-7 items-center justify-center rounded-lg"
              style={{ background: 'var(--color-primary)' }}
            >
              <span className="text-white text-xs font-black">G</span>
            </div>
            Grow Medical
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

        {/* Nav links */}
        <ul className="flex-1 overflow-y-auto p-4 space-y-1">
          {links.map((link) => (
            <li key={link.href}>
              <Link
                href={link.href}
                onClick={onClose}
                className="flex items-center px-3 py-3 text-base font-medium text-[var(--color-text)] rounded-lg hover:bg-[var(--color-surface-2)] transition-colors"
              >
                {link.label}
              </Link>
            </li>
          ))}
        </ul>

        {/* Footer */}
        <div className="border-t border-[var(--color-border)] p-4">
          <p className="text-xs text-[var(--color-text-light)]">© {new Date().getFullYear()} Grow Medical</p>
        </div>
      </nav>
    </>
  )
}
