import type { Metadata, Viewport } from 'next'
import { cookies } from 'next/headers'
import Script from 'next/script'
import { Montserrat, Inter, Playfair_Display } from 'next/font/google'
import '@/styles/globals.css'
import { DEFAULT_METADATA, getOrganizationJsonLd } from '@/lib/seo'
import { BRAND_COPY } from '@/lib/brand'
import AnnouncementBar from '@/components/layout/AnnouncementBar'
import HeaderShell from '@/components/layout/HeaderShell'
import TrustStrip from '@/components/layout/TrustStrip'
import Footer from '@/components/layout/Footer'
import CookieBanner from '@/components/ui/CookieBanner'
import PwaInstallBanner from '@/components/layout/PwaInstallBanner'
import { StorefrontThemeProvider } from '@/components/theme/StorefrontThemeProvider'
import { NoorThemeChrome } from '@/components/theme/NoorThemeChrome'
import { NoorUiProviders } from '@/components/noor/providers/NoorUiProviders'
import { getThemeBootstrapScript, isStorefrontTheme, resolveInitialTheme, STORAGE_KEY } from '@/lib/theme/storefront-theme'

const montserrat = Montserrat({
  variable: '--font-montserrat',
  subsets: ['latin'],
  display: 'swap',
  weight: ['400', '500', '600', '700', '800'],
})

const inter = Inter({
  variable: '--font-inter',
  subsets: ['latin'],
  display: 'swap',
})

const playfair = Playfair_Display({
  variable: '--font-playfair',
  subsets: ['latin'],
  display: 'swap',
  weight: ['400', '500', '600', '700'],
  style: ['normal', 'italic'],
})

export const metadata: Metadata = {
  ...DEFAULT_METADATA,
  applicationName: BRAND_COPY.siteName,
  manifest: '/manifest.webmanifest',
  appleWebApp: {
    capable: true,
    statusBarStyle: 'default',
    title: BRAND_COPY.siteName,
  },
  formatDetection: {
    telephone: false,
  },
  icons: {
    icon: [
      { url: '/favicon.ico', sizes: 'any' },
      { url: '/favicon-16x16.png', sizes: '16x16', type: 'image/png' },
      { url: '/favicon-32x32.png', sizes: '32x32', type: 'image/png' },
    ],
    apple: [{ url: '/apple-touch-icon.png', sizes: '180x180', type: 'image/png' }],
  },
}

export const viewport: Viewport = {
  width: 'device-width',
  initialScale: 1,
  themeColor: BRAND_COPY.themeColor,
}

export default async function RootLayout({
  children,
}: {
  children: React.ReactNode
}) {
  const cookieStore = await cookies()
  const cookieTheme = cookieStore.get(STORAGE_KEY)?.value
  const ssrTheme = resolveInitialTheme(
    isStorefrontTheme(cookieTheme) ? cookieTheme : null,
  )

  return (
    <html
      lang="sk"
      suppressHydrationWarning
      data-scroll-behavior="smooth"
      data-storefront-theme={ssrTheme}
      className={`${montserrat.variable} ${inter.variable} ${playfair.variable}`}
    >
      <body className="font-(--font-inter) antialiased">
        <Script
          id="storefront-theme-bootstrap"
          strategy="beforeInteractive"
          dangerouslySetInnerHTML={{
            __html: getThemeBootstrapScript(),
          }}
        />
        <script
          type="application/ld+json"
          dangerouslySetInnerHTML={{ __html: JSON.stringify(getOrganizationJsonLd()) }}
        />
        <StorefrontThemeProvider>
          <NoorUiProviders>
            <NoorThemeChrome />
            <div className="flex min-h-dvh flex-col">
              <AnnouncementBar />
              <HeaderShell />
              <TrustStrip />
              <main className="flex-1">{children}</main>
              <Footer />
              <CookieBanner />
              <PwaInstallBanner />
            </div>
          </NoorUiProviders>
        </StorefrontThemeProvider>
      </body>
    </html>
  )
}
