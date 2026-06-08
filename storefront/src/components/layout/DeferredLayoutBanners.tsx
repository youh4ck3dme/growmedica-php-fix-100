'use client'

import dynamic from 'next/dynamic'

const CookieBanner = dynamic(() => import('@/components/ui/CookieBanner'), {
  ssr: false,
})

const PwaInstallBanner = dynamic(() => import('@/components/layout/PwaInstallBanner'), {
  ssr: false,
})

export function DeferredLayoutBanners() {
  return (
    <>
      <CookieBanner />
      <PwaInstallBanner />
    </>
  )
}
