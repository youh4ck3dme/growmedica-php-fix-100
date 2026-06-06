import type { MetadataRoute } from 'next'

export default function robots(): MetadataRoute.Robots {
  const BASE_URL = process.env.NEXT_PUBLIC_SITE_URL ?? 'https://growmedical.sk'

  return {
    rules: [
      {
        userAgent: '*',
        allow: '/',
        disallow: ['/api/', '/kosik', '/checkout'],
      },
    ],
    sitemap: `${BASE_URL}/sitemap.xml`,
  }
}
