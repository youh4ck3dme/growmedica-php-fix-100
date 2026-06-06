import type { MetadataRoute } from 'next'
import { getAllProductHandlesForSitemap } from '@/lib/shopify/products'
import { getAllCollectionHandlesForSitemap } from '@/lib/shopify/collections'

const BASE_URL = process.env.NEXT_PUBLIC_SITE_URL ?? 'https://growmedical.sk'

export default async function sitemap(): Promise<MetadataRoute.Sitemap> {
  // Static pages
  const staticPages: MetadataRoute.Sitemap = [
    { url: BASE_URL, lastModified: new Date(), changeFrequency: 'daily', priority: 1.0 },
    { url: `${BASE_URL}/produkty`, lastModified: new Date(), changeFrequency: 'daily', priority: 0.9 },
    { url: `${BASE_URL}/kolekcie`, lastModified: new Date(), changeFrequency: 'weekly', priority: 0.8 },
    { url: `${BASE_URL}/vyhladavanie`, lastModified: new Date(), changeFrequency: 'monthly', priority: 0.5 },
  ]

  // Dynamic product pages
  let productPages: MetadataRoute.Sitemap = []
  let collectionPages: MetadataRoute.Sitemap = []

  try {
    const products = await getAllProductHandlesForSitemap()
    productPages = products.map(({ handle, updatedAt }) => ({
      url: `${BASE_URL}/produkty/${handle}`,
      lastModified: new Date(updatedAt),
      changeFrequency: 'weekly' as const,
      priority: 0.8,
    }))
  } catch (error) {
    console.error('[Sitemap] Failed to fetch products:', error)
  }

  try {
    const collections = await getAllCollectionHandlesForSitemap()
    collectionPages = collections.map(({ handle, updatedAt }) => ({
      url: `${BASE_URL}/kolekcie/${handle}`,
      lastModified: new Date(updatedAt),
      changeFrequency: 'weekly' as const,
      priority: 0.7,
    }))
  } catch (error) {
    console.error('[Sitemap] Failed to fetch collections:', error)
  }

  return [...staticPages, ...productPages, ...collectionPages]
}
