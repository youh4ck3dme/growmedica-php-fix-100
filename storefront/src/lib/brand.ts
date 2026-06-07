/** GrowMedica.sk — single source of truth for brand copy and tokens */

export const BRAND_COLORS = {
  primary: '#35C79A',
  primaryDark: '#2AA882',
  primaryLight: '#E7F8F2',
  text: '#101615',
  footerBg: '#101615',
  white: '#FFFFFF',
} as const

/** Legacy navy palette — must not appear in UI */
export const LEGACY_COLORS = {
  navy: '#1E3A5F',
  navyDark: '#152B46',
  oldGreen: '#6BAE2E',
} as const

export const ANNOUNCEMENT_BAR = {
  enabled: true,
  message: 'Doprava zdarma pri objednávke nad 50 € · Overené biomedicínske supplementy',
  href: '/doprava-a-platba',
  linkLabel: 'Viac info',
} as const

export const SHIPPING_TAB_CONTENT = [
  'Objednávky odosielame do 24 hodín v pracovné dni. Doručenie zvyčajne do 1–3 pracovných dní v rámci Slovenska.',
  'Ak produkt nevyhovuje, môžete ho vrátiť do 30 dní od prevzatia v súlade s našimi obchodnými podmienkami.',
  'Podrobnosti o doprave, platbe a reklamáciách nájdete v sekcii Doprava a platba.',
] as const

export const BRAND_COPY = {
  tagline: 'Biomedicínske supplementy · Stredná Európa',
  footerBlurb:
    'Moderná, prémiová a dôveryhodná značka pre zdravie, doplnky výživy a zdravotné produkty.',
  heroEyebrow: 'Najrýchlejšie rastúci e-shop v regióne',
  heroTitle: 'Prirodzené biomedicínske supplementy pre váš život',
  heroSubtitle:
    'GrowMedica.sk — najrýchlejšie rastúci e-shop v strednej Európe zameraný na prirodzené biomedicínske supplementy.',
  heroSubtitleShort:
    'Najrýchlejšie rastúci e-shop biomedicínskych supplementov v strednej Európe.',
  heroCta: 'Nakupovať',
  featuredHeading: 'Najpredávanejšie produkty',
  valueProps: ['DÔVERYHODNOSŤ', 'KVALITA', 'RAST', 'PODPORA'] as const,
  logoParts: ['Grow', 'Medica', '.sk'] as const,
  themeColor: '#35C79A',
  siteName: 'GrowMedica.sk',
  siteTitle: 'GrowMedica.sk — biomedicínske supplementy',
  siteDescription:
    'GrowMedica.sk — najrýchlejšie rastúci e-shop biomedicínskych supplementov v strednej Európe. Prirodzené produkty pre zdravie a vitalitu.',
  aboutLabel: 'Prečo Growmedica',
  aboutHeading: 'Rast, príroda a lekárska presnosť v jednom e-shope',
  aboutBody:
    'GrowMedica.sk je najrýchlejšie rastúci e-shop v strednej Európe zameraný na prirodzené biomedicínske supplementy. Spájame lekársku presnosť s prírodnou rovnováhou — overené produkty, dôveryhodný nákup a rastúca komunita spokojných zákazníkov.',
  companyName: 'GrowMedica.sk',
  aboutPageTitle: 'O spoločnosti GrowMedica.sk',
  aboutPageIntro:
    'Sme tím, ktorý verí v silu prirodzených biomedicínskych supplementov podložených skutočnou vedou a rastúcou komunitou v strednej Európe.',
  trustStripStats: ['460+ produktov', 'Stredná Európa', 'Biomedicínske supplementy'] as const,
  pageDescriptions: {
    about:
      'Zistite viac o poslaní, hodnotách a prístupe GrowMedica.sk k prirodzeným biomedicínskym supplementom.',
    blog:
      'Články o zdraví, výžive a prírodných riešeniach od tímu GrowMedica.sk.',
    products:
      'Preskúmajte celý sortiment prirodzených biomedicínskych supplementov GrowMedica.sk.',
    search: 'Vyhľadajte biomedicínske supplementy v GrowMedica.sk.',
    collections:
      'Prehliadajte kategórie produktov. Vyberte si biomedicínske supplementy podľa vašich potrieb.',
    cart: 'Nákupný košík s vybranými biomedicínskymi supplementmi.',
    contact:
      'Kontaktujte nás — GrowMedica s.r.o., BELLOVA 6, KOŠICE. E-mail: info@growmedica.sk',
  },
} as const

export const BRAND_ASSETS = [
  '/logo.svg',
  '/logo-icon.svg',
  '/logo-dark.svg',
  '/manifest.webmanifest',
] as const

export const REQUIRED_CSS_VARS = [
  '--color-primary',
  '--color-primary-light',
  '--color-text',
  '--color-footer-bg',
] as const
