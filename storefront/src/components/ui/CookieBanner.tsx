'use client'

import { useState, useEffect } from 'react'

const COOKIE_KEY = 'gm_cookie_consent'

export default function CookieBanner() {
  const [visible, setVisible] = useState(false)

  useEffect(() => {
    const consent = localStorage.getItem(COOKIE_KEY)
    if (!consent) {
      // Small delay so it doesn't flash on page load
      const timer = setTimeout(() => setVisible(true), 800)
      return () => clearTimeout(timer)
    }
  }, [])

  function accept() {
    localStorage.setItem(COOKIE_KEY, 'accepted')
    setVisible(false)
  }

  function decline() {
    localStorage.setItem(COOKIE_KEY, 'declined')
    setVisible(false)
  }

  if (!visible) return null

  return (
    <div
      role="dialog"
      aria-label="Súhlas s cookies"
      className="fixed bottom-0 left-0 right-0 z-50 p-4 md:p-6"
      style={{ animation: 'slideUp 0.4s ease-out' }}
    >
      <div
        className="max-w-4xl mx-auto rounded-2xl p-5 md:p-6 flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-6"
        style={{
          background: 'rgba(30, 58, 95, 0.97)',
          backdropFilter: 'blur(12px)',
          boxShadow: '0 -4px 30px rgba(0,0,0,0.2)',
          border: '1px solid rgba(255,255,255,0.1)',
        }}
      >
        <div className="flex-1 text-sm text-white/90 leading-relaxed">
          <p className="font-semibold text-white mb-1" style={{ fontFamily: 'Montserrat, sans-serif' }}>
            🍪 Používame cookies
          </p>
          <p>
            Táto stránka používa cookies na zlepšenie vášho zážitku z prehliadania, analýzu návštevnosti a personalizáciu obsahu.
            Viac informácií nájdete v sekcii{' '}
            <a href="/ochrana-osobnych-udajov" className="underline hover:text-white transition-colors">
              Ochrana osobných údajov
            </a>.
          </p>
        </div>
        <div className="flex gap-3 shrink-0">
          <button
            onClick={decline}
            className="px-4 py-2.5 rounded-lg text-sm font-semibold text-white/80 hover:text-white border border-white/20 hover:border-white/40 transition-colors"
            style={{ fontFamily: 'Montserrat, sans-serif' }}
          >
            Odmietnuť
          </button>
          <button
            onClick={accept}
            className="px-5 py-2.5 rounded-lg text-sm font-bold transition-colors"
            style={{
              fontFamily: 'Montserrat, sans-serif',
              background: '#6BAE2E',
              color: '#152B46',
            }}
          >
            Prijať všetky
          </button>
        </div>
      </div>

      <style jsx>{`
        @keyframes slideUp {
          from { transform: translateY(100%); opacity: 0; }
          to { transform: translateY(0); opacity: 1; }
        }
      `}</style>
    </div>
  )
}
