# Portfollio-theme

**Riaz Portfolio** — WordPress theme + companion plugin.

A WordPress port of the Riaz Ahmed portfolio design (originally the Next.js site in `../Riaz-Ahmed`), built as a production-ready, editable theme.

| Folder | What it is |
| --- | --- |
| `riaz-portfolio/` | The theme: design, templates, Customizer, animations, block styles, Elementor/Divi layout support. |
| `riaz-portfolio-core/` | The companion plugin: content types, meta boxes, Theme Section block + Elementor widget, shortcodes, contact form, SEO/schema, GA4, starter-content importer. A copy is bundled in the theme at `plugins/riaz-portfolio-core.zip` for one-click install. |
| `dist/` | Upload-ready zips. |
| `phpcs.xml.dist` | WordPress Coding Standards config (WordPress-Extra + Docs). |

## Install

1. Appearance → Themes → Add New → Upload Theme → `dist/riaz-portfolio.zip` → Activate.
2. Appearance → Riaz Portfolio → **Install & activate Riaz Portfolio Core**.
3. Same screen → **Import starter content**.
4. Appearance → Customize → Homepage Sections / Theme Options.

Full user and developer guide: [`riaz-portfolio/documentation.html`](riaz-portfolio/documentation.html).

## Development

```bash
# Minify after editing the front-end CSS/JS (theme loads *.min.* unless SCRIPT_DEBUG is true)
npx esbuild riaz-portfolio/assets/css/main.css --minify --outfile=riaz-portfolio/assets/css/main.min.css
npx esbuild riaz-portfolio/assets/js/main.js --minify --target=es2019 --outfile=riaz-portfolio/assets/js/main.min.js

# Coding standards
phpcs

# Rebuild the bundled plugin and the distribution zips
rm -f riaz-portfolio/plugins/riaz-portfolio-core.zip
zip -qr riaz-portfolio/plugins/riaz-portfolio-core.zip riaz-portfolio-core -x '*.DS_Store'
mkdir -p dist && rm -f dist/*.zip
zip -qr dist/riaz-portfolio.zip riaz-portfolio -x '*.DS_Store'
zip -qr dist/riaz-portfolio-core.zip riaz-portfolio-core -x '*.DS_Store'
```

## Content rules carried over from the original project

- Only publish real, verified testimonials, client names, results and statistics. Starter samples are imported as drafts and sections stay hidden until they have content.
- Replace the contact details and social profiles before launch (Customizer → Theme Options).
