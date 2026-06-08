/** Shared hero image tuning — used by HeroSlider and server-side LCP preload. */

/** Full-bleed hero with a mobile width cap to avoid oversized Shopify CDN requests. */
export const HERO_IMAGE_SIZES =
  '(max-width: 768px) min(100vw, 640px), (max-width: 1200px) 960px, 1200px'

export const HERO_LCP_QUALITY = 65
export const HERO_SLIDE_QUALITY = 70
