# Edu Craft Theme

Production-ready WordPress Full Site Editing (FSE) starter theme with:

- `theme.json` driven global styles/settings
- Modular block architecture in `blocks/*`
- ACF Pro block integration with PHP render templates
- WordPress Interactivity API example block
- WooCommerce support via PHP and JS hooks
- SCSS + Bootstrap 5 (module-based imports)
- Webpack dev/prod pipeline outputting to `dist/`

## Requirements

- WordPress `6.5+`
- PHP `8.1+`
- Node.js `20+`
- npm `10+`
- ACF Pro plugin (for ACF blocks)
- WooCommerce plugin (optional, for commerce features)

## Setup

1. Move into theme directory:
   - `cd /home/kmirosz/projects/hypercrew/edu-craft/web/wp-content/themes/edu-craft-theme`
2. Install dependencies:
   - `npm install`
3. Build assets for development:
   - `npm run dev`
4. Build minified production assets:
   - `npm run build`
5. Activate `Edu Craft Theme` from WordPress admin.
6. Ensure ACF Pro is active, then insert **Edu Craft Example** block.
7. Verify Interactivity API behavior by toggling the example block message.
8. If WooCommerce is active, verify product-loop helper text output and JS cart toggle hooks.

## Block Development

Each block must live in:

- `blocks/{block-name}/block.json`
- `blocks/{block-name}/index.php`
- `blocks/{block-name}/style.scss`
- `blocks/{block-name}/editor.scss`
- `blocks/{block-name}/script.js`
- `blocks/{block-name}/functions.php` (optional)

Blocks are auto-registered by `inc/blocks.php` using `register_block_type()`.

## Performance Notes

- No jQuery dependencies.
- Frontend scripts use deferred loading strategy.
- Block assets are loaded per block via `block.json`.
- Image loading defaults to lazy when possible.
