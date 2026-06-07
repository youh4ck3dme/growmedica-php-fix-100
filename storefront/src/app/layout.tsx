import type { Metadata, Viewport } from 'next'
import { Montserrat, Inter } from 'next/font/google'
import '@/styles/globals.css'
import { DEFAULT_METADATA, getOrganizationJsonLd } from '@/lib/seo'
import { BRAND_COPY } from '@/lib/brand'
import AnnouncementBar from '@/components/layout/AnnouncementBar'
import HeaderShell from '@/components/layout/HeaderShell'
import TrustStrip from '@/components/layout/TrustStrip'
import Footer from '@/components/layout/Footer'
import CookieBanner from '@/components/ui/CookieBanner'

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

export const metadata: Metadata = {
  ...DEFAULT_METADATA,
  manifest: '/manifest.webmanifest',
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
  return (
    <html lang="sk" className={`${montserrat.variable} ${inter.variable}`}>
      <body className="font-(--font-inter) antialiased">
        <script
          type="application/ld+json"
          dangerouslySetInnerHTML={{ __html: JSON.stringify(getOrganizationJsonLd()) }}
        />
        <div className="flex min-h-dvh flex-col">
          <AnnouncementBar />
          <HeaderShell />
          <TrustStrip />
          <main className="flex-1">{children}</main>
          <Footer />
          <CookieBanner />
        </div>
      </body>
    </html>
  )
}
