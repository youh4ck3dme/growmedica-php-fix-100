import Link from 'next/link'
import { Container } from '@/components/ui/Container'

const FOOTER_LINKS = {
  'Menu': [
    { href: '/produkty', label: 'Všetky produkty' },
    { href: '/kolekcie', label: 'Kolekcie' },
    { href: '/vyhladavanie', label: 'Vyhľadávanie' },
    { href: '/o-nas', label: 'O nás' },
    { href: '/blog', label: 'Blog' },
  ],
  'Informácie': [
    { href: '/obchodne-podmienky', label: 'Obchodné podmienky' },
    { href: '/ochrana-osobnych-udajov', label: 'Ochrana osobných údajov' },
    { href: '/reklamacie', label: 'Reklamácie' },
    { href: '/dodanie', label: 'Doprava a doručenie' },
    { href: '/faq', label: 'Časté otázky' },
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
            <div className="flex gap-3">
              {[
                {
                  label: 'Facebook',
                  href: 'https://facebook.com',
                  icon: (
                    <svg className="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                      <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878V14.89h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" />
                    </svg>
                  ),
                },
                {
                  label: 'Instagram',
                  href: 'https://instagram.com',
                  icon: (
                    <svg className="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                      <path fillRule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clipRule="evenodd" />
                    </svg>
                  ),
                },
              ].map(({ label, href, icon }) => (
                <a
                  key={label}
                  href={href}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="flex h-8 w-8 items-center justify-center rounded-md transition-colors bg-white/8 hover:bg-white/15"
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
                      className="text-sm transition-colors hover:text-white"
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
                <a href="tel:+421900000000" className="hover:text-white transition-colors">
                  +421 900 000 000
                </a>
              </li>
              <li className="leading-relaxed">
                Slovenská republika
              </li>
              <li className="pt-2">
                <Link
                  href="/kontakt"
                  className="inline-block px-4 py-2 border border-white/30 rounded text-white text-xs font-semibold hover:bg-white/10 transition-colors uppercase tracking-wider"
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
          <div className="flex items-center gap-3 text-white/50">
            {['VISA', 'MC', 'GPAY', 'APAY', 'DPD', 'Packeta'].map((method) => (
              <span
                key={method}
                className="px-2 py-0.5 border border-white/15 rounded text-[0.65rem] font-bold tracking-wider"
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
