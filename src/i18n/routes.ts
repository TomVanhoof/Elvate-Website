// Route table (BUILD-BRIEF §3). Keys are page ids; values are the path per language.
export type Lang = 'nl' | 'en';

export const routes = {
  home: { nl: '/', en: '/en/' },
  work: { nl: '/werk/', en: '/en/work/' },
  about: { nl: '/over-mij/', en: '/en/about/' },
  audit: { nl: '/gratis-audit/', en: '/en/free-audit/' },
  contact: { nl: '/contact/', en: '/en/contact/' },
  privacy: { nl: '/privacy/', en: '/en/privacy/' },
  terms: { nl: '/voorwaarden/', en: '/en/terms/' },
  thanks: { nl: '/bedankt/', en: '/en/thanks/' },
} as const;

export type RouteId = keyof typeof routes;

export const path = (id: RouteId, lang: Lang) => routes[id][lang];
