'use client'

import type { LucideIcon } from 'lucide-react'
import { Headphones, ShieldCheck, Sparkles, TrendingUp } from 'lucide-react'
import { motion, useReducedMotion } from 'framer-motion'
import { Container } from '@/components/ui/Container'
import { cn } from '@/lib/utils'

interface TrustBadgeItem {
  title: string
  subtitle: string
  icon: LucideIcon
}

const TRUST_BADGES: TrustBadgeItem[] = [
  {
    title: 'DÔVERYHODNOSŤ',
    subtitle: 'Bezpečný nákup',
    icon: ShieldCheck,
  },
  {
    title: 'KVALITA',
    subtitle: 'Overené produkty',
    icon: Sparkles,
  },
  {
    title: 'RAST',
    subtitle: 'Rastúca značka v regióne',
    icon: TrendingUp,
  },
  {
    title: 'PODPORA',
    subtitle: 'Sme tu pre vás',
    icon: Headphones,
  },
]

export function TrustBadges() {
  const reduceMotion = useReducedMotion()

  return (
    <section className="usp-bar trust-badges theme-transition" aria-label="Benefity">
      <Container>
        <div className="noor-stagger grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4">
          {TRUST_BADGES.map((item, badgeIndex) => {
            const Icon = item.icon

            return (
              <motion.article
                key={item.title}
                className={cn('trust-badge-glass theme-transition')}
                initial={reduceMotion ? false : { opacity: 0, y: 12 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true, amount: 0.35 }}
                transition={{
                  duration: reduceMotion ? 0 : 0.45,
                  delay: reduceMotion ? 0 : badgeIndex * 0.08,
                  ease: [0.22, 1, 0.36, 1],
                }}
                whileHover={reduceMotion ? undefined : { y: -4, scale: 1.02 }}
              >
                <div className="trust-badge-glass__icon" aria-hidden="true">
                  <Icon className="h-7 w-7" strokeWidth={1.5} />
                </div>
                <p
                  className="font-bold text-xs tracking-wide text-(--color-text)"
                  style={{ fontFamily: 'Montserrat, sans-serif' }}
                >
                  {item.title}
                </p>
                <p className="text-xs text-(--color-text-muted)">{item.subtitle}</p>
              </motion.article>
            )
          })}
        </div>
      </Container>
    </section>
  )
}
