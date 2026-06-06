import type { Metadata } from 'next'
import { Container } from '@/components/ui/Container'

export const metadata: Metadata = {
  title: 'Veľkoobchod | Grow Medical',
}

export default function Velkoobchod() {
  return (
    <div className="py-12 lg:py-20 bg-(--color-bg) min-h-screen">
      <Container>
        <div className="max-w-4xl mx-auto bg-white p-8 md:p-12 rounded-2xl shadow-(--shadow-card) border border-(--color-border)">
          <h1 className="text-3xl font-bold text-(--color-primary) mb-8 font-montserrat">Kontakt a Veľkoobchodná spolupráca</h1>
          <div className="prose prose-lg text-(--color-text-muted) space-y-6">
            <p className="text-lg">Ste lekáreň, fitness centrum, bio obchod alebo terapeut a radi by ste zaradili prémiové produkty Grow Medical do svojej ponuky?</p>
            <p>Hľadáme stabilných partnerov, ktorým záleží na kvalite a zdraví ich zákazníkov tak ako nám.</p>
            
            <h2 className="text-xl font-bold text-(--color-text) mt-8 mb-4">Čo ponúkame B2B partnerom?</h2>
            <ul className="list-disc pl-5 space-y-2">
              <li>Výhodné veľkoobchodné zľavy a marže</li>
              <li>Prémiovú a garantovanú kvalitu produktov</li>
              <li>Marketingovú a informačnú podporu</li>
              <li>Rýchle dodanie zo slovenského skladu</li>
            </ul>

            <h2 className="text-xl font-bold text-(--color-text) mt-8 mb-4">Ako nadviazať spoluprácu?</h2>
            <p>Kontaktujte nás priamo e-mailom so žiadosťou o veľkoobchodný cenník. Nezabudnite uviesť vaše IČO a krátky popis vašej činnosti.</p>
            
            <div className="bg-gray-50 p-6 rounded-xl mt-8 border-l-4 border-(--color-accent-green)">
              <strong className="block text-lg mb-2 text-(--color-text)">B2B Kontakt:</strong>
              E-mail: <a href="mailto:velkoobchod@growmedica.sk" className="text-(--color-primary) font-bold">velkoobchod@growmedica.sk</a><br/>
              Telefón: <a href="tel:+421900000000" className="text-(--color-primary) font-bold">+421 900 000 000</a>
            </div>
          </div>
        </div>
      </Container>
    </div>
  )
}
