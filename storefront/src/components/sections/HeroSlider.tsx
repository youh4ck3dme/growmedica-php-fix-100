'use client'

import Image from 'next/image'
import Link from 'next/link'
import { useCallback, useEffect, useState } from 'react'
import { AnimatePresence, m, useReducedMotion } from 'framer-motion'
import { ChevronLeft, ChevronRight } from 'lucide-react'
import { Container } from '@/components/ui/Container'
import { BRAND_COPY } from '@/lib/brand'
import {
  HERO_IMAGE_SIZES,
  HERO_LCP_QUALITY,
  HERO_SLIDE_QUALITY,
} from '@/lib/hero-image'
import { cn } from '@/lib/utils'

export interface HeroSlide {
  id: string
  imageUrl: string
  alt: string
  width: number
  height: number
}

interface HeroSliderProps {
  slides: HeroSlide[]
}

const AUTOPLAY_MS = 6000

const FALLBACK_SLIDES: HeroSlide[] = [
  {
    id: 'fallback-1',
    imageUrl: '',
    alt: '',
    width: 1600,
    height: 900,
  },
]

export function HeroSlider({ slides }: HeroSliderProps) {
  const items = slides.length > 0 ? slides : FALLBACK_SLIDES
  const [index, setIndex] = useState(0)
  const reduceMotion = useReducedMotion()

  const goTo = useCallback(
    (next: number) => {
      setIndex((next + items.length) % items.length)
    },
    [items.length],
  )

  const goNext = useCallback(() => goTo(index + 1), [goTo, index])
  const goPrev = useCallback(() => goTo(index - 1), [goTo, index])

  useEffect(() => {
    if (reduceMotion || items.length <= 1) return

    const timer = window.setInterval(goNext, AUTOPLAY_MS)
    return () => window.clearInterval(timer)
  }, [goNext, items.length, reduceMotion])

  const active = items[index]
  const hasImage = Boolean(active.imageUrl)
  const isLcpSlide = index === 0

  return (
    <section
      className="theme-transition noor-reveal noor-hero-section hero-slider relative overflow-hidden bg-(--color-surface)"
      aria-labelledby="hero-heading"
    >
      <div className="hero-slider__stage relative w-full min-h-[28rem] sm:min-h-[32rem] lg:min-h-[36rem]">
        <AnimatePresence mode="wait" initial={false}>
          <m.div
            key={active.id}
            className="absolute inset-0"
            initial={reduceMotion || isLcpSlide ? false : { opacity: 0, scale: 1.03 }}
            animate={{ opacity: 1, scale: 1 }}
            exit={reduceMotion ? undefined : { opacity: 0, scale: 1.01 }}
            transition={{ duration: reduceMotion ? 0 : 0.65, ease: [0.22, 1, 0.36, 1] }}
          >
            {hasImage ? (
              <Image
                src={active.imageUrl}
                alt={active.alt}
                fill
                priority={isLcpSlide}
                fetchPriority={isLcpSlide ? 'high' : 'auto'}
                loading={isLcpSlide ? 'eager' : 'lazy'}
                sizes={HERO_IMAGE_SIZES}
                className="object-cover object-center"
                quality={isLcpSlide ? HERO_LCP_QUALITY : HERO_SLIDE_QUALITY}
              />
            ) : (
              <div
                className="absolute inset-0"
                style={{
                  background:
                    'linear-gradient(135deg, var(--color-primary-light) 0%, var(--color-surface-2) 45%, var(--color-surface) 100%)',
                }}
                aria-hidden="true"
              />
            )}

            <div className="hero-slider__overlay absolute inset-0" aria-hidden="true" />
          </m.div>
        </AnimatePresence>

        <Container className="relative z-10 flex h-full min-h-[inherit] items-center py-10 lg:py-16">
          <m.div
            className="hero-slider__copy liquid-glass liquid-glass--heavy max-w-xl rounded-3xl p-6 sm:p-8 lg:p-10"
            initial={reduceMotion ? false : { opacity: 0, y: 16 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: reduceMotion ? 0 : 0.5, delay: reduceMotion ? 0 : 0.15 }}
          >
            <p className="section-label mb-3">{BRAND_COPY.heroEyebrow}</p>
            <h1
              id="hero-heading"
              className="noor-display-heading text-3xl lg:text-4xl xl:text-5xl font-extrabold leading-tight text-balance mb-4 text-(--color-text)"
            >
              {BRAND_COPY.heroTitle}
            </h1>
            <p className="text-base lg:text-lg leading-relaxed mb-8 text-(--color-text-muted)">
              <span className="sm:hidden">{BRAND_COPY.heroSubtitleShort}</span>
              <span className="hidden sm:inline">{BRAND_COPY.heroSubtitle}</span>
            </p>
            <Link
              href="/produkty"
              id="hero-cta-primary"
              className="btn btn-primary btn-lg noor-pill-cta w-full sm:w-auto"
            >
              {BRAND_COPY.heroCta}
            </Link>
          </m.div>
        </Container>

        {items.length > 1 && (
          <>
            <div className="hero-slider__controls absolute inset-x-0 bottom-6 z-20 flex items-center justify-center gap-2">
              {items.map((slide, dotIndex) => (
                <button
                  key={slide.id}
                  type="button"
                  className={cn(
                    'hero-slider__dot',
                    dotIndex === index && 'hero-slider__dot--active',
                  )}
                  onClick={() => goTo(dotIndex)}
                  aria-label={`Slide ${dotIndex + 1} z ${items.length}`}
                  aria-current={dotIndex === index ? 'true' : undefined}
                />
              ))}
            </div>

            <button
              type="button"
              className="hero-slider__nav hero-slider__nav--prev"
              onClick={goPrev}
              aria-label="Predchádzajúci slide"
            >
              <ChevronLeft className="h-5 w-5" strokeWidth={1.75} />
            </button>
            <button
              type="button"
              className="hero-slider__nav hero-slider__nav--next"
              onClick={goNext}
              aria-label="Ďalší slide"
            >
              <ChevronRight className="h-5 w-5" strokeWidth={1.75} />
            </button>
          </>
        )}
      </div>
    </section>
  )
}
