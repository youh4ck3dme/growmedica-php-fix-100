import Link from 'next/link'
import { Container } from '@/components/ui/Container'

const FOOTER_LINKS = {
  'Menu': [
    { href: '/kolekcie/balicky-zdravia', label: 'BALÍČKY ZDRAVIA' },
    { href: '/kolekcie/zdravotne-riesenia', label: 'ZDRAVOTNÉ RIEŠENIA' },
    { href: '/kolekcie/mykologicke-produkty', label: 'MYKOLOGICKÉ PRODUKTY' },
    { href: '/kolekcie/doplnky-vyzivy', label: 'DOPLNKY VÝŽIVY' },
    { href: '/kolekcie/zdravie', label: 'ZDRAVIE' },
    { href: '/kolekcie/kozmetika', label: 'KOZMETIKA' },
    { href: '/kolekcie/pre-zvierata', label: 'PRE ZVIERATÁ' },
    { href: '/blog', label: 'BLOG' },
  ],
  'Informácie': [
    { href: '/obchodne-podmienky', label: 'Obchodné podmienky' },
    { href: '/reklamacny-poriadok', label: 'Reklamačný poriadok' },
    { href: '/kontakt', label: 'Kontakt' },
    { href: '/ochrana-osobnych-udajov', label: 'Ochrana osobných údajov' },
    { href: '/doprava-a-platba', label: 'Doprava a platba' },
    { href: '/faq', label: 'Často kladené otázky' },
    { href: '/velkoobchod', label: 'Kontakt a Veľkoobchodná spolupráca' },
  ],
}

export default function Footer() {
  return (
    <footer
      role="contentinfo"
      style={{ background: 'var(--color-footer-bg)' }}
    >
      <Container>
        {/* Main footer grid */}
        <div className="py-12 grid grid-cols-2 md:grid-cols-4 gap-8">
          {/* Brand column */}
          <div className="col-span-2 md:col-span-1">
            <Link
              href="/"
              className="inline-flex items-center gap-2 mb-4"
              aria-label="Growmedica — domov"
            >
              {/* Leaf logo */}
              <svg width="32" height="32" viewBox="0 0 32 32" fill="none" aria-hidden="true">
                <circle cx="16" cy="16" r="16" fill="#6BAE2E" />
                <path d="M16 7C16 7 9 11 9 18C9 21.3137 12.134 24 16 24C19.866 24 23 21.3137 23 18C23 11 16 7 16 7Z" fill="white" />
                <path d="M16 24V14" stroke="#6BAE2E" strokeWidth="1.5" strokeLinecap="round" />
                <path d="M16 18C16 18 13 15 11 13" stroke="#6BAE2E" strokeWidth="1" strokeLinecap="round" />
              </svg>
              <span style={{ fontFamily: 'Montserrat, sans-serif', fontWeight: 800, fontSize: '1.1rem' }}>
                <span className="text-white">grow</span><span style={{ color: '#CBD5E0' }}>medica</span>
              </span>
            </Link>
            <p className="text-sm leading-relaxed max-w-xs mb-4" style={{ color: 'var(--color-footer-text)' }}>
              Prémiové doplnky výživy a zdravotné produkty pre aktívnych ľudí na Slovensku.
            </p>
            {/* Social icons */}
            <div className="flex gap-3 flex-wrap">
              {[
                {
                  label: 'Instagram',
                  href: 'https://instagram.com/growmedica',
                  icon: (
                    <svg className="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                      <path fillRule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clipRule="evenodd" />
                    </svg>
                  ),
                },
                {
                  label: 'Facebook',
                  href: 'https://facebook.com/growmedica',
                  icon: (
                    <svg className="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                      <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878V14.89h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" />
                    </svg>
                  ),
                },
                {
                  label: 'YouTube',
                  href: 'https://youtube.com/@growmedica',
                  icon: (
                    <svg className="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                      <path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                    </svg>
                  ),
                },
                {
                  label: 'TikTok',
                  href: 'https://tiktok.com/@growmedica',
                  icon: (
                    <svg className="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                      <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z" />
                    </svg>
                  ),
                },
                {
                  label: 'Pinterest',
                  href: 'https://pinterest.com/growmedica',
                  icon: (
                    <svg className="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                      <path d="M12 0C5.373 0 0 5.372 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 01.083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.631-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12 0-6.628-5.373-12-12-12z" />
                    </svg>
                  ),
                },
              ].map(({ label, href, icon }) => (
                <a
                  key={label}
                  href={href}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="flex h-11 w-11 sm:h-8 sm:w-8 items-center justify-center rounded-md transition-colors bg-white/8 hover:bg-white/15"
                  style={{ color: 'var(--color-footer-text)' }}
                  aria-label={label}
                >
                  {icon}
                </a>
              ))}
            </div>
          </div>

          {/* Link columns */}
          {Object.entries(FOOTER_LINKS).map(([title, links]) => (
            <nav key={title} aria-label={`${title} navigácia`}>
              <h3
                className="text-xs font-bold uppercase tracking-widest mb-4 text-white"
                style={{ fontFamily: 'Montserrat, sans-serif', letterSpacing: '0.1em' }}
              >
                {title}
              </h3>
              <ul className="space-y-2.5">
                {links.map((link) => (
                  <li key={link.href}>
                    <Link
                      href={link.href}
                      className="text-sm transition-colors hover:text-white py-1.5 inline-block"
                      style={{ color: 'var(--color-footer-text)' }}
                    >
                      {link.label}
                    </Link>
                  </li>
                ))}
              </ul>
            </nav>
          ))}

          {/* Contact column */}
          <div>
            <h3
              className="text-xs font-bold uppercase tracking-widest mb-4 text-white"
              style={{ fontFamily: 'Montserrat, sans-serif', letterSpacing: '0.1em' }}
            >
              Kontakt
            </h3>
            <ul className="space-y-2.5 text-sm" style={{ color: 'var(--color-footer-text)' }}>
              <li>
                <a href="mailto:info@growmedica.sk" className="hover:text-white transition-colors">
                  info@growmedica.sk
                </a>
              </li>
              <li>
                <span className="block font-medium text-white mb-0.5">Zákaznícka linka:</span>
                <a href="tel:+421900000000" className="hover:text-white transition-colors">
                  +421 900 000 000
                </a>
              </li>
              <li className="leading-relaxed mt-2 pt-2 border-t border-white/10">
                <span className="block font-medium text-white mb-0.5">Sídlo spoločnosti:</span>
                GrowMedica s.r.o.<br />
                BELLOVA 6, KOŠICE, 040 01
              </li>
              <li className="pt-2">
                <Link
                  href="/kontakt"
                  className="inline-block px-4 py-2 mt-2 border border-white/30 rounded text-white text-xs font-semibold hover:bg-white/10 transition-colors uppercase tracking-wider"
                  style={{ fontFamily: 'Montserrat, sans-serif' }}
                >
                  Kontaktovať nás
                </Link>
              </li>
            </ul>
          </div>
        </div>

        {/* Bottom bar */}
        <div
          className="py-5 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs"
          style={{ borderTop: '1px solid var(--color-footer-border)', color: 'var(--color-footer-text)' }}
        >
          <p>© {new Date().getFullYear()} Growmedica s.r.o. Všetky práva vyhradené.</p>

          {/* Payment logos (text placeholders) */}
          <div className="flex items-center gap-3 text-white/90">
            {['VISA', 'MC', 'GPAY', 'APAY', 'DPD', 'Packeta'].map((method) => (
              <span
                key={method}
                className="px-2 py-0.5 border border-white/20 rounded text-[0.65rem] font-bold tracking-wider"
                style={{ fontFamily: 'Montserrat, sans-serif' }}
              >
                {method}
              </span>
            ))}
          </div>
        </div>
      </Container>
    </footer>
  )
}
