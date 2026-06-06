import type { Metadata } from 'next'
import { Container } from '@/components/ui/Container'

export const metadata: Metadata = {
  title: 'Reklamačný poriadok | Grow Medical',
}

export default function ReklamacnyPoriadok() {
  return (
    <div className="py-12 lg:py-20 bg-(--color-bg) min-h-screen">
      <Container>
        <div className="max-w-4xl mx-auto bg-white p-8 md:p-12 rounded-2xl shadow-(--shadow-card) border border-(--color-border)">
          <h1 className="text-3xl font-bold text-(--color-primary) mb-8 font-montserrat">Reklamačný poriadok</h1>
          <div className="prose prose-lg text-(--color-text-muted) space-y-6">
            <p className="italic bg-gray-50 p-4 rounded-lg">Poznámka: Tu vložte vaše presné znenie reklamačného poriadku a pokyny k vráteniu tovaru.</p>
            <h2 className="text-xl font-bold text-(--color-text) mt-8 mb-4">Postup pri reklamácii</h2>
            <p>V prípade, že ste s tovarom nespokojní, alebo bol poškodený pri preprave, kontaktujte nás na info@growmedica.sk.</p>
            <h2 className="text-xl font-bold text-(--color-text) mt-8 mb-4">Vrátenie tovaru (Odstúpenie od zmluvy)</h2>
            <p>Zákazník má právo na vrátenie nepoškodeného tovaru do 14 dní bez udania dôvodu...</p>
          </div>
        </div>
      </Container>
    </div>
  )
}
