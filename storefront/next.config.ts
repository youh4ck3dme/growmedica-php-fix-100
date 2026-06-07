import { spawnSync } from 'node:child_process'
import type { NextConfig } from 'next'
import withSerwistInit from '@serwist/next'
import { getLegacyRedirectEntries } from './src/lib/category-map'

const categoryRedirects = getLegacyRedirectEntries().map(({ source, destination }) => ({
  source,
  destination,
  permanent: true,
}))

const revision =
  spawnSync('git', ['rev-parse', 'HEAD'], { encoding: 'utf-8' }).stdout?.trim() ||
  crypto.randomUUID()

const withSerwist = withSerwistInit({
  swSrc: 'src/app/sw.ts',
  swDest: 'public/sw.js',
  disable: process.env.NODE_ENV === 'development',
  cacheOnNavigation: true,
  additionalPrecacheEntries: [
    { url: '/offline', revision },
    { url: '/offline.html', revision },
    { url: '/manifest.webmanifest', revision },
  ],
})

const nextConfig: NextConfig = {
  images: {
    remotePatterns: [
      {
        protocol: 'https',
        hostname: 'cdn.shopify.com',
        pathname: '/s/files/**',
      },
    ],
    formats: ['image/avif', 'image/webp'],
  },

  // Security headers
  async headers() {
    return [
      {
        source: '/(.*)',
        headers: [
          { key: 'X-Content-Type-Options', value: 'nosniff' },
          { key: 'X-Frame-Options', value: 'DENY' },
          { key: 'X-XSS-Protection', value: '1; mode=block' },
          { key: 'Referrer-Policy', value: 'strict-origin-when-cross-origin' },
          { key: 'Permissions-Policy', value: 'camera=(), microphone=(), geolocation=()' },
          {
            key: 'Strict-Transport-Security',
            value: 'max-age=63072000; includeSubDomains; preload',
          },
        ],
      },
    ]
  },

  // Legacy PHP URL redirects
  async redirects() {
    return [
      ...categoryRedirects,
      {
        source: '/produkt/:slug',
        destination: '/produkty/:slug',
        permanent: true,
      },
      {
        source: '/sk/produkt/:slug',
        destination: '/produkty/:slug',
        permanent: true,
      },
      {
        source: '/kategoria/:slug',
        destination: '/kolekcie/:slug',
        permanent: true,
      },
      {
        source: '/kolekcia/:slug',
        destination: '/kolekcie/:slug',
        permanent: true,
      },
      {
        source: '/sk/kolekcia/:slug',
        destination: '/kolekcie/:slug',
        permanent: true,
      },
      {
        source: '/sk/kategoria/:slug',
        destination: '/kolekcie/:slug',
        permanent: true,
      },
      {
        source: '/shop',
        destination: '/produkty',
        permanent: true,
      },
      {
        source: '/sk/shop',
        destination: '/produkty',
        permanent: true,
      },
      {
        source: '/sk',
        destination: '/',
        permanent: true,
      },
    ]
  },

  // Experimental features
  experimental: {
    optimizeCss: false,
  },
}

export default withSerwist(nextConfig)
