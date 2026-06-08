import type { Metadata, Viewport } from 'next'
import Script from 'next/script'
import { Montserrat, Inter, Playfair_Display } from 'next/font/google'
import '@/styles/globals.css'
import { DEFAULT_METADATA, getOrganizationJsonLd } from '@/lib/seo'
import { BRAND_COPY } from '@/lib/brand'
import AnnouncementBar from '@/components/layout/AnnouncementBar'
import HeaderShell from '@/components/layout/HeaderShell'
import TrustStrip from '@/components/layout/TrustStrip'
import Footer from '@/components/layout/Footer'
import { DeferredLayoutBanners } from '@/components/layout/DeferredLayoutBanners'
import { MotionProvider } from '@/components/motion/MotionProvider'
import { StorefrontThemeProvider } from '@/components/theme/StorefrontThemeProvider'
import { NoorThemeChrome } from '@/components/theme/NoorThemeChrome'
import { getDefaultTheme, getThemeBootstrapScript } from '@/lib/theme/storefront-theme'

const montserrat = Montserrat({
  variable: '--font-montserrat',
  subsets: ['latin'],
  display: 'swap',
  weight: ['500', '600', '700', '800'],
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
  weight: ['600', '700'],
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

export default function RootLayout({
  children,
}: {
  children: React.ReactNode
}) {
  const defaultTheme = getDefaultTheme()

  return (
    <html
      lang="sk"
      suppressHydrationWarning
      data-scroll-behavior="smooth"
      data-storefront-theme={defaultTheme}
      className={`${montserrat.variable} ${inter.variable} ${playfair.variable}`}
    >
      <body className="font-(--font-inter) antialiased" suppressHydrationWarning>
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
          <MotionProvider>
            <NoorThemeChrome />
            <div className="flex min-h-dvh flex-col">
              <AnnouncementBar />
              <HeaderShell />
              <TrustStrip />
              <main className="flex-1">{children}</main>
              <Footer />
              <DeferredLayoutBanners />
            </div>
          </MotionProvider>
        </StorefrontThemeProvider>
      </body>
    </html>
  )
}
