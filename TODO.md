# TODO: what Tom still has to supply

All of these show as `[bracketed]` text on the site until replaced. Copy lives in `src/i18n/nl.ts` and `src/i18n/en.ts`
(search for `[`), case studies in `src/content/work/*.json`.

## Blocking launch
- [ ] **Mailbox + email authentication.** Create `info@elvate.be` in Hostinger and check SPF/DKIM for elvate.be,
      otherwise the form emails land in spam. Then send one test through each form (audit + contact).
- [ ] **Legal texts.** Privacy (`legal.privacy`) and Voorwaarden (`legal.terms`): all `[...]` placeholders, your name,
      business address and the "last updated" date. Have a professional check them.

## Photos
- [ ] **Portrait of Tom**: used twice (Home "Waarom elvate", Over mij). Currently a grey `[Foto van mij]` panel.
      Drop the file in `src/assets/photos/` and replace the `PhotoPanel` placeholder in `HomePage.astro` and `AboutPage.astro`.
- [ ] **Photo choices.** Current picks from "Potential Photos": hero = fjord (Home + Over mij CTA), Gratis audit hero = mountain
      slope, Home audit section = moss, service cards = laptop / clothes rail / abstract / studio, Over mij strip = jellyfish /
      escalator / moss. Swap any you don't like.
- [ ] **Alt texts.** All `[Alt-tekst: ...]` / `[Alt text: ...]` strings are drafts. Write real ones.

## Portfolio (`src/content/work/*.json`)
- [ ] Appliqué + BlackBean: "De opdracht", "Doelgroep" and the rest of "Ontwerpkeuzes" (NL + EN).
- [ ] Fonts: `"font": "[Lettertype]"`. Colours are sampled from your style guides, so check they're the exact brand colours.
- [ ] Mobile screens: the "Eindontwerp" screens are crops of the desktop designs. Replace with real exports if you have them.
- [ ] First real client case: add one file with `"concept": false` (the "Conceptproject" tag disappears automatically).

## Prices and timings
- [ ] Prices `[€1.500]` / `[€2.800]` / `[€4.000]` (Home pricing + FAQ).
- [ ] Timing `[2–4 weken]` and `[2–4] wkn` (Home hero, steps, FAQ).
- [ ] Audit turnaround `[48 uur]` (Home, Gratis audit, Contact, form success message, thanks page).

## Copy
- [ ] **Speed proof:** `[Lighthouse-scores na build]` ("Snel" on Home). Fill in the real scores after launch.
- [ ] **FAQ answers:** `[Jij, volledig.]` and `[Ja, een extra taal is beschikbaar als add-on.]`.
- [ ] **Gratis audit page:** one-line descriptions for Snelheid / Mobiel gebruik / Vindbaarheid, the sample report
      (an anonymised real audit) and its explanation, and "Wat gebeurt er na de audit?".
- [ ] **Over mij:** the three personal paragraphs. Confirm the headline "Hoi, ik ben Tom."
- [ ] **EN copy:** everything in `en.ts` and the EN halves of the JSON files is a translation draft. Please review it.

## Nice to have
- [ ] Final logo SVG: the site uses `src/assets/elvate.svg` (inlined, so it recolours with the text).
- [ ] Better OG image than the wordmark on dark (`public/og-default.png`, 1200×630).
- [ ] Business address in the JSON-LD (`HomePage.astro`) once you want it public; nothing but the known facts is in there now.
- [ ] Per-case pages (`/werk/applique`, `/werk/blackbean`) if you want them split from the Werk page later.

## Others
- [ ] Scroling item on the right of the screen.
- [ ] Static pop up for free audit.
- [ ] SEO optimasation 
- [ ] Coockies and tracking of emails?
- [ ] Loading a different language is really slow.
