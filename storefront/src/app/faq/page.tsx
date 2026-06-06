import type { Metadata } from 'next'
import { Container } from '@/components/ui/Container'

export const metadata: Metadata = {
  title: 'Často kladené otázky | Grow Medical',
}

const faqs = [
  {
    q: "Aké sú čakacie doby na doručenie?",
    a: "Objednávky expedujeme väčšinou do 24 hodín. Štandardná doba doručenia v rámci Slovenska je 1 až 3 pracovné dni."
  },
  {
    q: "Je možné si tovar vyzdvihnúť aj osobne?",
    a: "Osobný odber v našom sídle momentálne neposkytujeme. Ponúkame však širokú sieť odberných miest Packeta."
  },
  {
    q: "Sú vaše výživové doplnky vhodné aj pre vegánov?",
    a: "Väčšina našich produktov je na rastlinnej báze a vhodná pre vegánov. Presné informácie nájdete vždy v zložení konkrétneho produktu."
  },
  {
    q: "Ako mám reklamovať poškodený tovar?",
    a: "Ak Vám dorazil tovar poškodený, prosím odfoťte ho a pošlite nám fotografie na info@growmedica.sk. Vyriešime to v čo najkratšom čase zaslaním nového kusu."
  }
];

export default function FAQ() {
  return (
    <div className="py-12 lg:py-20 bg-(--color-bg) min-h-screen">
      <Container>
        <div className="max-w-3xl mx-auto bg-white p-8 md:p-12 rounded-2xl shadow-(--shadow-card) border border-(--color-border)">
          <h1 className="text-3xl font-bold text-(--color-primary) mb-8 font-montserrat text-center">Často kladené otázky (FAQ)</h1>
          
          <div className="space-y-6">
            {faqs.map((faq, index) => (
              <div key={index} className="p-6 rounded-xl border border-(--color-border) bg-gray-50 hover:bg-gray-100 transition-colors">
                <h3 className="text-lg font-bold text-(--color-text) mb-3">{faq.q}</h3>
                <p className="text-(--color-text-muted) leading-relaxed">{faq.a}</p>
              </div>
            ))}
          </div>

          <div className="mt-12 text-center pt-8 border-t border-(--color-border)">
            <p className="text-(--color-text-muted) mb-4">Nenašli ste odpoveď na svoju otázku?</p>
            <a href="/kontakt" className="btn btn-primary inline-flex">
              Kontaktujte nás
            </a>
          </div>
        </div>
      </Container>
    </div>
  )
}
