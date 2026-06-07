'use client'

import { useState } from 'react'
import { cn } from '@/lib/utils'
import { sanitizeHtml } from '@/lib/utils'
import { SHIPPING_TAB_CONTENT } from '@/lib/brand'

interface ProductTabsProps {
  descriptionHtml: string | null
  compositionHtml: string | null
}

type TabId = 'description' | 'composition' | 'shipping'

const TABS: { id: TabId; label: string }[] = [
  { id: 'description', label: 'Popis' },
  { id: 'composition', label: 'Zloženie' },
  { id: 'shipping', label: 'Doprava & vrátenie' },
]

export default function ProductTabs({ descriptionHtml, compositionHtml }: ProductTabsProps) {
  const availableTabs = TABS.filter((tab) => {
    if (tab.id === 'description') return Boolean(descriptionHtml)
    if (tab.id === 'composition') return Boolean(compositionHtml)
    return true
  })

  const [active, setActive] = useState<TabId>(availableTabs[0]?.id ?? 'shipping')

  if (availableTabs.length === 0) return null

  return (
    <section className="mt-12" aria-label="Detailné informácie o produkte">
      <div className="border-b border-(--color-border)">
        <div className="flex flex-wrap gap-1" role="tablist">
          {availableTabs.map((tab) => (
            <button
              key={tab.id}
              type="button"
              role="tab"
              id={`product-tab-${tab.id}`}
              aria-selected={active === tab.id}
              aria-controls={`product-panel-${tab.id}`}
              onClick={() => setActive(tab.id)}
              className={cn(
                'px-4 py-3 text-sm font-semibold border-b-2 transition-colors -mb-px',
                active === tab.id
                  ? 'border-(--color-primary) text-(--color-primary-dark)'
                  : 'border-transparent text-(--color-text-muted) hover:text-(--color-text)',
              )}
            >
              {tab.label}
            </button>
          ))}
        </div>
      </div>

      {availableTabs.map((tab) => (
        <div
          key={tab.id}
          id={`product-panel-${tab.id}`}
          role="tabpanel"
          aria-labelledby={`product-tab-${tab.id}`}
          hidden={active !== tab.id}
          className="py-6"
        >
          {tab.id === 'description' && descriptionHtml && (
            <div
              className="product-description max-w-3xl"
              dangerouslySetInnerHTML={{ __html: sanitizeHtml(descriptionHtml) }}
            />
          )}
          {tab.id === 'composition' && compositionHtml && (
            <div
              className="product-description max-w-3xl"
              dangerouslySetInnerHTML={{ __html: sanitizeHtml(compositionHtml) }}
            />
          )}
          {tab.id === 'shipping' && (
            <div className="product-description max-w-3xl space-y-3 text-(--color-text-muted)">
              {SHIPPING_TAB_CONTENT.map((paragraph) => (
                <p key={paragraph}>{paragraph}</p>
              ))}
            </div>
          )}
        </div>
      ))}
    </section>
  )
}
