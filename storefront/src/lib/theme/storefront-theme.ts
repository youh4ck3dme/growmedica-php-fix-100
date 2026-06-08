export type StorefrontTheme = 'classic' | 'noor'

export const STORAGE_KEY = 'growmedica-storefront-theme'
export const DEFAULT_THEME: StorefrontTheme = 'classic'
export const THEME_CHANGED_EVENT = 'storefront-theme-changed'

export function isStorefrontTheme(value: unknown): value is StorefrontTheme {
  return value === 'classic' || value === 'noor'
}

export function readStoredTheme(): StorefrontTheme | null {
  if (typeof window === 'undefined') return null

  try {
    const stored = localStorage.getItem(STORAGE_KEY)
    return isStorefrontTheme(stored) ? stored : null
  } catch {
    return null
  }
}

export function getDocumentTheme(): StorefrontTheme {
  if (typeof document === 'undefined') return DEFAULT_THEME

  const attr = document.documentElement.getAttribute('data-storefront-theme')
  return isStorefrontTheme(attr) ? attr : DEFAULT_THEME
}

export function applyThemeToDocument(theme: StorefrontTheme): void {
  if (typeof document === 'undefined') return
  document.documentElement.setAttribute('data-storefront-theme', theme)
}
