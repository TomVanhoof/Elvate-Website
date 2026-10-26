import { defineConfig } from 'astro/config';
import sitemap from '@astrojs/sitemap';
import { routes } from './src/i18n/routes.ts';

const SITE = 'https://elvate.be';

// url -> its NL/EN pair, from the same route table the site uses (slugs differ: /werk/ vs /en/work/).
const pairs = new Map();
for (const r of Object.values(routes)) {
  pairs.set(SITE + r.nl, r);
  pairs.set(SITE + r.en, r);
}

export default defineConfig({
  site: SITE,
  output: 'static',
  trailingSlash: 'always',
  build: { format: 'directory' },
  prefetch: { prefetchAll: true, defaultStrategy: 'hover' },
  i18n: {
    defaultLocale: 'nl',
    locales: ['nl', 'en'],
    routing: { prefixDefaultLocale: false },
  },
  integrations: [
    sitemap({
      filter: (page) => !/\/(bedankt|thanks|404)\/?$/.test(page),
      serialize(item) {
        const pair = pairs.get(item.url);
        if (pair) {
          item.links = [
            { url: SITE + pair.nl, lang: 'nl-BE' },
            { url: SITE + pair.en, lang: 'en' },
            { url: SITE + pair.nl, lang: 'x-default' },
          ];
        }
        return item;
      },
    }),
  ],
});
