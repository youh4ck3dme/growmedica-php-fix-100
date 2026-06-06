/** GrowMedica.sk brand board — single source of truth for integrity tests */

export const BRAND_COLORS = {
  primary: '#35C79A',
  primaryDark: '#2AA882',
  primaryLight: '#E7F8F2',
  text: '#101615',
  footerBg: '#101615',
  white: '#FFFFFF',
} as const

/** Legacy navy palette — must not appear in UI */
export const LEGACY_COLORS = {
  navy: '#1E3A5F',
  navyDark: '#152B46',
  oldGreen: '#6BAE2E',
} as const

export const BRAND_COPY = {
  heroTitle: 'Starostlivosť o vaše zdravie',
  heroSubtitle: 'Prémiové produkty pre zdravie a pohodu',
  heroCta: 'Nakupovať',
  featuredHeading: 'Najpredávanejšie produkty',
  valueProps: ['DÔVERYHODNOSŤ', 'KVALITA', 'RÝCHLOSŤ', 'PODPORA'] as const,
  logoParts: ['Grow', 'Medica', '.sk'] as const,
  themeColor: '#35C79A',
} as const

export const BRAND_ASSETS = [
  '/logo.svg',
  '/logo-icon.svg',
  '/logo-dark.svg',
  '/manifest.webmanifest',
] as const

export const REQUIRED_CSS_VARS = [
  '--color-primary',
  '--color-primary-light',
  '--color-text',
  '--color-footer-bg',
] as const
