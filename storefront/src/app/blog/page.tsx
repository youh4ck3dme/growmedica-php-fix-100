import type { Metadata } from 'next'
import Link from 'next/link'
import { Container } from '@/components/ui/Container'

export const metadata: Metadata = {
  title: 'Blog | Grow Medical',
  description: 'Články o zdraví, výžive, liečivých hubách a prírodných riešeniach od tímu Grow Medical.',
}

export default function BlogPage() {
  return (
    <div className="py-12 lg:py-20 bg-(--color-bg) min-h-screen">
      <Container>
        <div className="max-w-4xl mx-auto text-center">
          <span className="section-label">Čítajte a vzdelávajte sa</span>
          <h1 className="section-heading mb-4">Blog Grow Medical</h1>
          <p className="text-lg text-(--color-text-muted) mb-12 max-w-2xl mx-auto">
            Pripravujeme pre vás odborné články o zdraví, výžive, liečivých hubách a prírodných riešeniach. Sledujte nás!
          </p>
          
          <div className="bg-white p-12 rounded-2xl shadow-(--shadow-card) border border-(--color-border)">
            <div className="text-6xl mb-6">📝</div>
            <h2 className="text-2xl font-bold text-(--color-primary) mb-4 font-montserrat">
              Čoskoro tu budú prvé články
            </h2>
            <p className="text-(--color-text-muted) mb-8 max-w-md mx-auto">
              Náš tím odborníkov pracuje na prvých článkoch. Medzitým sa pozrite na naše produkty a ich podrobné popisy.
            </p>
            <Link href="/produkty" className="btn btn-primary btn-lg">
              Prezrieť produkty
            </Link>
          </div>
        </div>
      </Container>
    </div>
  )
}
