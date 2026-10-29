import { defineCollection } from 'astro:content';
import { glob } from 'astro/loaders';
import { z } from 'astro/zod';

const bilingual = z.object({ nl: z.string(), en: z.string() });

// One JSON file per case. A real client case is one new file with `concept: false`.
const work = defineCollection({
  loader: glob({ pattern: '*.json', base: './src/content/work' }),
  schema: ({ image }) =>
    z.object({
      order: z.number(),
      title: z.string(),
      type: bilingual,
      concept: z.boolean().default(true),
      url: z.string().url().optional(),
      brief: bilingual,
      audience: bilingual,
      decisions: bilingual,
      colors: z.array(z.object({ hex: z.string(), label: z.string().optional() })),
      font: z.string(),
      logo: image().optional(),
      hero: image().optional(),
      heroAlt: bilingual,
      screens: z.array(z.object({ image: image().optional(), alt: bilingual })),
    }),
});

export const collections = { work };
