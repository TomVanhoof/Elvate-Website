# elvate.be — build brief for Claude Code

Put this file in the repo root as `CLAUDE.md` (or point Claude Code at it). The `design/` folder holds the approved designs as static HTML. Open them in a browser; they are the visual source of truth. `design-brief.md` is the original content brief.

## 1. What we're building

The website of **elvate**, a solo freelance web designer in Belgium. It moves from Webflow to Astro. The site's one job is to get Belgian SMB owners to request a **free website audit**. Secondary goals: "Bekijk mijn werk" and direct contact.

- Voice: first person ("ik", never "wij"). Calm, direct, no hype.
- Primary language NL at `/`, secondary EN at `/en/`.
- **Never invent** testimonials, client logos, statistics, scores or client claims. Text in `[brackets]` is a placeholder: keep the brackets visible until Tom supplies the real content (see §12).
- The two portfolio projects are **concept projects** and must stay labelled "Conceptproject".

## 2. Stack and constraints

- **Astro**, static output (`output: 'static'`). No UI framework (no React/Vue). Plain `.astro` components plus one global CSS file with the tokens below. Scoped `<style>` per component is fine.
- **Minimal JS.** Only these three bits need it: the mobile menu, the sticky audit pill, and form submission/validation. Use small inline `<script>` blocks in those components. FAQ uses native `<details>`.
- **Images:** `astro:assets` `<Image>`/`<Picture>`, AVIF + WebP. The hero image loads eagerly with `fetchpriority="high"`; all other images are lazy.
- **Fonts:** self-host **Archivo variable** with BOTH axes, `wdth` 62–125 and `wght` 100–900, as woff2. Preload the one file used above the fold, with `font-display: swap`. The wide headings depend on `font-stretch: 125%`. A static Archivo file will NOT render them correctly.
- **Hosting:** any static host. Tom wants it cheaper than Webflow. **Decision needed before building the forms:** pick the host first, then use that host's own form/function feature (§9).
- **Target:** Lighthouse 95+ on all four categories, mobile and desktop. The real scores go into the "Snel" proof point after launch.

## 3. Routes

| NL | EN | Design file |
|---|---|---|
| `/` | `/en/` | `design/home.html`, `design/home-mobiel.html` |
| `/werk` | `/en/work` | `design/werk.html` |
| `/werk/applique` · `/werk/blackbean` | `/en/work/…` | optional: split the cases from `werk.html` into their own pages later; start with one Werk page |
| `/over-mij` | `/en/about` | `design/over-mij.html` |
| `/gratis-audit` | `/en/free-audit` | `design/gratis-audit.html` |
| `/contact` | `/en/contact` | `design/contact.html` |
| `/privacy` | `/en/privacy` | `design/privacy.html` |
| `/voorwaarden` | `/en/terms` | `design/voorwaarden.html` |
| `/bedankt` | `/en/thanks` | not designed. Fallback after a form post when JS is off: reuse the success message from `componenten.html` |
| `404` | — | not designed: a short message plus links to Home and Gratis audit, same header and footer |

- Use Astro's built-in i18n routing (`defaultLocale: 'nl'`, `prefixDefaultLocale: false`).
- Keep all copy in `src/i18n/nl.ts` and `src/i18n/en.ts` (or content collections), never hard-coded in components.
- The **language switch must link to the same page in the other language**, using the route table above. Add `hreflang` alternates on every page.
- **EN copy:** translate the NL copy and mark the whole EN file `// TODO: Tom to review`. Dutch runs 15–20% longer, and every component already handles that.

## 4. Design tokens (global CSS)

```css
:root{
  /* colour */
  --bg:#0C0F11; --surface:#13181B; --surface-2:#1B2226; /* hero frame */
  --border:#262E33; --line:#3A4247; --nav-line:#1E2529;
  --text:#FFFFFF; --text-2:#D5DBDE; --text-3:#C9D0D4; --muted:#9AA3A8; --placeholder:#7D878D;
  --accent:#8FD3FF; --on-accent:#06182A; --error:#FF8A7A;
  --paper:#F3F4F2; --ink:#121619; /* sample audit report only */
  --scrim:rgba(8,10,12,.85); --glass:rgba(16,20,23,.86);

  /* type */
  --font:'Archivo',system-ui,sans-serif;
  --fs-display-1:clamp(30px,3.9vw,56px);   /* H1, wide, 700, lh 1.08 */
  --fs-display-2:clamp(28px,3.2vw,46px);   /* section titles, wide, 700, lh 1.1 */
  --fs-body-l:18px; --fs-body:16px; --fs-small:14px; --fs-label:12px;

  /* layout */
  --container:1280px; --gutter:clamp(20px,5vw,64px);
  --section-y:clamp(72px,9vw,140px);       /* 64px on phones */
  --radius-control:4px; --radius-pill:999px; /* cards & photos: 0 */
  --tap:44px;
}
.wide{font-stretch:125%;text-transform:uppercase;letter-spacing:.02em}
.eyebrow{font-size:12px;font-weight:600;font-stretch:125%;text-transform:uppercase;letter-spacing:.16em;color:var(--muted)}
:focus-visible{outline:2px solid var(--accent);outline-offset:3px}
```

Rules:

- Accent only appears as a fill with `--on-accent` text, or as text/lines on dark. Never put white text on accent.
- Apply uppercase only via CSS; write the copy itself in normal case.
- Single breakpoint at **820px**: the nav collapses to the menu button. Grids use `repeat(auto-fit, minmax(min(100%, Npx), 1fr))` so they stack without extra breakpoints.
- Section eyebrows on Home are numbered "02 — Herkenbaar?" up to "09 — FAQ". The hero rail reads 01–10.

## 5. Components

All of these are shown, with their states, in `design/componenten.html`.

| Component | Props / notes |
|---|---|
| `BaseLayout` | `title`, `description`, `lang`, `alternates`, `ogImage`; includes Header, Footer, AuditPill (unless `hidePill`) |
| `Header` | `variant: 'overlay' \| 'solid'`. Use overlay on Home: transparent over the hero photo. Use solid on subpages, with a bottom border `--nav-line`. Active link gets `--accent` and `aria-current="page"`. Gratis audit page uses `minimal`: logo + language switch only, no nav, no CTA |
| `MobileMenu` | full screen below the top bar. Big wide links, the audit CTA at full width, phone number and language switch. Escape closes it, focus is trapped inside, body scroll is locked. Toggle button carries `aria-expanded` and `aria-controls` |
| `LangSwitch` | `NL / EN`; the current language is in accent with `aria-current="true"`. Every link needs a ≥44px tap area |
| `Button` | `variant: primary \| secondary`, `href?` (renders `<a>`) or `type` (renders `<button>`). Height 52px, 44px in the nav; full width on phones. States: hover (`brightness(1.08)`), focus, disabled |
| `LinkRing` | 52px circle with an arrow, plus a wide label ("Bekijk mijn werk", "Jouw project hier?") |
| `Tag` | accent fill, wide 11px 700, e.g. "Conceptproject" |
| `Eyebrow` | `number?`, `text` |
| `Section` | `tone: 'bg' \| 'surface'`, container + `--section-y` padding |
| `PhotoPanel` | image + bottom scrim `linear-gradient(0deg, rgba(8,10,12,.85), transparent 60%)` + slot. Used by ServiceCard and ProjectCard, and as the full-bleed backgrounds |
| `ServiceCard` | `title`, `line`, `eyebrow?`, `image`, `size: 'lg' \| 'sm'`. Websites/Webshops are `lg` |
| `ProjectCard` | `title`, `line`, `image`, `href`, always shows Tag "Conceptproject" |
| `ProofItem` | `n`, `title`, `text` |
| `Step` | `n`, `title`, `text`, `active?`. Horizontal (dot + line) at ≥820px, vertical timeline on phones |
| `PriceCard` | `name`, `forWhom`, `price`, `includes`, `featured?`. Featured = accent border + accent title (Branding + Website) |
| `FaqList` | items → native `<details>/<summary>`; the "+" rotates 45°. Use the second item `open` as default on Home |
| `GlassPanel` | `background: var(--glass)`, `backdrop-filter: blur(10px)`, 1px border `rgba(255,255,255,.1)`. Used for the hero info box and the audit card over a photo |
| `AuditForm` | `variant: 'stacked' \| 'inline'`. Fields: website URL (`type=url`), e-mail. On the Gratis audit page add a privacy checkbox. Shows "Binnen [48 uur] in je mailbox." |
| `ContactForm` | naam, e-mail, website (optional), bericht, privacy checkbox |
| `Field` / `Checkbox` | visible label above the input (never a placeholder used as the label). Error = `--error` border + message linked with `aria-describedby`, plus `aria-invalid` |
| `FormStatus` | success (accent border + check) / error (error border + "mail naar info@elvate.be"). Replaces the form in place and moves focus to it |
| `AuditPill` | see §8 |
| `Footer` | logo, info@elvate.be, +32 499 70 29 28, BE 1013.537.063, nav, Privacy, Voorwaarden, LangSwitch |

## 6. Pages — section order

**Home** (`home.html`, phone layout in `home-mobiel.html`)

1. **Hero:** full-bleed landscape photo inside a framed card (frame `--surface-2`, radius 18px on desktop, no frame on phone). Left-to-right scrim. Contents: H1 = longest option ("Een website die klanten oplevert, niet alleen een visitekaartje."), sub, LinkRing "Bekijk mijn werk", primary "Vraag je gratis website-audit aan". Right side: vertical rail 01–10 (desktop only). GlassPanel "Gratis audit" (desktop only). Fact row: Vaste prijs / [2–4] wkn / 1 contact.
2. **Herkenbaar?:** 3 pain points in columns with a top line, then the closing line with the accent second half.
3. **Wat ik doe:** a photo strip of 4 ServiceCards. Websites and Webshops are wider (flex 1.5). On phones, the two big ones are full width and the small two sit in a 2-column grid.
4. **Waarom elvate:** portrait photo + "Geen bureau. Gewoon ik." + 3 ProofItems.
5. **Zo werkt het:** 4 Steps on a surface background, with "Meestal live binnen [2–4 weken]."
6. **Mijn werk:** 2 ProjectCards, then a full-width "Jouw project hier?" row that links to the audit.
7. **Gratis audit:** full-bleed photo with a GlassPanel holding the AuditForm (stacked), aligned right on desktop.
8. **Prijzen:** 3 PriceCards, the add-ons line and "Weet je niet wat je nodig hebt? Start met de gratis audit."
9. **FAQ:** title left, 6 FaqList items right.
10. **Slot-CTA:** headline, Gratis audit + Mail me direct, email/phone, and the ContactForm on the right.

**Gratis audit** (`gratis-audit.html`): minimal header, no nav and no AuditPill.

1. Hero with the form beside the text.
2. Wat je krijgt: 3 cards.
3. Hoe het werkt: 3 Steps.
4. Sample report figure on paper colours.
5. Audit FAQ.
6. Inline form repeat.
7. Slim footer: name, BTW number, Privacy, Voorwaarden.

**Werk** (`werk.html`): intro, then per case:

- hero screen
- title + type line
- 3-column meta: De opdracht / Doelgroep / Ontwerpkeuzes
- style guide: colour swatches, plus a font sample and logo
- 3 final screens

Second case on a surface background. Then a dashed "Klantprojecten — Jouw project hier?" slot. **Build the cases as a content collection** (`src/content/work/`) with fields `title, type, concept: boolean, brief, audience, decisions, colors[], font, logo, screens[]`. When the first real client case arrives, it's one new file with `concept: false` and no tag.

**Over mij** (`over-mij.html`): photo + "Hoi, ik ben Tom." + intro, then:

- Wat me drijft: personal-story placeholders
- strip of 3 of Tom's own landscape photos
- Hoe ik werk: 4 numbered rows
- photo CTA: audit + contact

No values list.

**Contact** (`contact.html`): headline + email/phone/BTW list + an accent-bordered "Liever eerst een analyse?" card linking to the audit. The ContactForm sits on the right.

**Privacy / Voorwaarden**: one `LegalLayout` with a 760px text column, eyebrow "Juridisch", H1, "Laatst bijgewerkt", and prose. The section headings are in the design files; the content is placeholders Tom must complete. ⚠ Tom must have these texts checked; don't generate legal content beyond the placeholders.

## 7. Responsive rules

- Phone frame is 390px; nothing may scroll sideways at 320px.
- Phone H1 is 27px (clamp minimum is 30px; override it on the hero so "VISITEKAARTJE." fits).
- Primary buttons become full width; the fact row becomes a 3-column grid with a top border.
- Steps become a vertical timeline. FAQ question text drops to 16px.
- Hero rail and GlassPanel are hidden below 820px.

## 8. Behaviour

- **AuditPill:**
  - Position: fixed bottom-left on desktop; full width minus 12px margins and 12px above the bottom on phones.
  - Appears only once the hero CTA has scrolled out of view (IntersectionObserver). Hides while any audit form is in view, and never shows on `/gratis-audit`.
  - Dismiss is remembered for the session (`sessionStorage`, wrapped in try/catch).
  - On phones, give the footer 80–110px extra bottom padding so the pill never covers content.
- **Anchor links** ("Gratis audit" in the nav → the audit section on Home, or `/gratis-audit` from other pages): use `scroll-margin-top` on sections. Smooth scroll only when `prefers-reduced-motion: no-preference`.
- **Mobile menu:** see Components. It must work with focus trap and Escape.
- **No other animation.** At most a 150–200ms colour/transform transition on hover and on the FAQ icon.

## 9. Forms

- **Audit form:** URL + e-mail. **Contact form:** naam, e-mail, website?, bericht.
- Both forms need a privacy checkbox on the dedicated pages. Add one to the Home forms as well, linking to `/privacy`.
- Use progressive enhancement:
  - With JS: `fetch` POST, then show FormStatus in place.
  - Without JS: normal POST → redirect to `/bedankt`.
- Validation:
  - Native `required` + `type`, with Dutch messages via `setCustomValidity`.
  - Normalise the URL: accept "bakkerij.be" without `https://`.
- Spam: a honeypot field plus a time-to-submit check. No visible captcha.
- Delivery: the submission is emailed to info@elvate.be.
- **Implementation depends on the host:**
  - Netlify → Netlify Forms.
  - Cloudflare Pages → Pages Function + a mail API.
  - Otherwise → a form service.
  - Ask Tom which host before building this; until then, keep the form endpoint in one config value.

## 10. SEO

Per-page `<title>` / description (NL drafts below; Tom may edit). Also add a canonical, hreflang nl/en/x-default, `sitemap.xml` (@astrojs/sitemap) and `robots.txt`. Add an OG image; until Tom has one, make it 1200×630 with the wordmark on `--bg`.

| Page | Title | Description |
|---|---|---|
| Home | elvate — Websites voor Belgische kmo's | Ik ontwerp en bouw snelle, duidelijke websites voor kleine en middelgrote bedrijven. Vraag je gratis website-audit aan. |
| Gratis audit | Gratis website-audit — elvate | 3 tot 5 concrete verbeterpunten voor je website: snelheid, mobiel gebruik en vindbaarheid. Gratis, zonder verplichtingen. |
| Werk | Werk — elvate | Conceptprojecten: van merk tot website. |
| Over mij | Over mij — elvate | Je werkt rechtstreeks met mij, geen tussenpersonen. |
| Contact | Contact — elvate | Mail, bel of stuur een bericht: info@elvate.be, +32 499 70 29 28. |

**Structured data:** JSON-LD `ProfessionalService` on Home with ONLY these known facts: name elvate, url, email, telephone, vatID BE 1013.537.063, areaServed BE. No ratings and no address until Tom gives one.

## 11. Accessibility checklist

- One H1 per page, logical H2/H3, and landmarks (`header`, `nav` with `aria-label`, `main`, `footer`).
- Contrast AA, which the tokens already meet. Every text over a photo sits on the scrim or a glass panel.
- Visible `:focus-visible` everywhere. Tap targets ≥44px.
- Decorative SVG gets `aria-hidden`. Icon-only buttons get `aria-label`. Photos get real `alt` text (placeholder alts are TODOs).
- Labels on every field, and errors announced (`aria-live="polite"` on FormStatus).
- Respect `prefers-reduced-motion`.

## 12. Placeholders Tom must supply

Keep these visibly bracketed in the build and list them in a `TODO.md`:

- **Photos:** hero landscape (Home, Gratis audit, Over mij CTA, Home audit section), 4 service photos, portrait of Tom (×2), 3 landscape photos for the Over mij strip. All are Tom's own Unsplash photos; credit isn't needed for his own work.
- **Portfolio:** Appliqué and BlackBean hero screens, 3 final screens each, logos, exact brand colours and fonts, brief/audience/decisions text.
- **Prices:** [€1.500] / [€2.800] / [€4.000]. Timing: [2–4 weken]. Audit turnaround: [48 uur].
- **Speed proof:** [Lighthouse-scores na build]; fill in after launch from the real scores.
- **FAQ answers:** [Jij, volledig.] and [Ja, extra taal als add-on.].
- **Audit page:** one-line descriptions for Snelheid / Mobiel gebruik / Vindbaarheid, the sample report (an anonymised real audit), and the "Wat gebeurt er na de audit?" answer.
- **Over mij:** personal story (3 paragraphs). Confirm the "Hoi, ik ben Tom." headline.
- **Legal:** Privacy and Voorwaarden texts, business address, last-updated date.
- **Logo:** the existing lowercase "elvate" wordmark as SVG. The designs use Archivo 700 as a stand-in.
- **Hosting choice** (blocks the forms).

## 13. Build order

1. Scaffold Astro with i18n routing, tokens, fonts, BaseLayout, Header/MobileMenu/Footer/LangSwitch.
2. Build the static components from `componenten.html`, then Home desktop + phone.
3. Gratis audit page, then forms (after the host decision).
4. Werk (content collection), Over mij, Contact, Legal, 404, Bedankt.
5. AuditPill, SEO/meta/sitemap/JSON-LD, EN translation pass.
6. Lighthouse + axe pass on every page at 390px and 1440px. Fix anything under 95.
