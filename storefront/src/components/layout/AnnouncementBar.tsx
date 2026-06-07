'use client'

import Link from 'next/link'
import { useEffect, useState } from 'react'
import { ANNOUNCEMENT_BAR } from '@/lib/brand'

const STORAGE_KEY = 'growmedica-announcement-dismissed'

export default function AnnouncementBar() {
  const [visible, setVisible] = useState(false)

  useEffect(() => {
    if (!ANNOUNCEMENT_BAR.enabled) return
    const dismissed = sessionStorage.getItem(STORAGE_KEY)
    setVisible(!dismissed)
  }, [])

  if (!ANNOUNCEMENT_BAR.enabled || !visible) return null

  function dismiss() {
    sessionStorage.setItem(STORAGE_KEY, '1')
    setVisible(false)
  }

  return (
    <div
      className="announcement-bar"
      role="region"
      aria-label="Aktuálna ponuka"
    >
      <div className="mx-auto flex max-w-7xl items-center justify-center gap-3 px-4 py-2 text-center text-xs font-semibold sm:text-sm">
        <span className="text-white/95">{ANNOUNCEMENT_BAR.message}</span>
        {ANNOUNCEMENT_BAR.href && (
          <Link
            href={ANNOUNCEMENT_BAR.href}
            className="shrink-0 underline underline-offset-2 hover:text-white"
          >
            {ANNOUNCEMENT_BAR.linkLabel}
          </Link>
        )}
        <button
          type="button"
          onClick={dismiss}
          className="ml-auto shrink-0 rounded p-1 text-white/80 hover:text-white"
          aria-label="Zavrieť oznámenie"
        >
          <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>
  )
}
