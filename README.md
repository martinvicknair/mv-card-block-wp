# MV Card Block

A lightweight Gutenberg block that renders a “card” with:

- Title + subtitle
- Body content (simple rich text)
- Optional CTA (button / text link / text link with icon 🔗 / none)
- Per-card accent color, with a plugin-wide default

## Install

1. Upload the plugin folder to `wp-content/plugins/mv-card-block/` (or upload the ZIP in WP Admin).
2. Activate **MV Card Block**.

## Where to change the global default accent color

WP Admin → **Settings → MV Card Block**

Cards can override the accent color per-block in the block sidebar.

## Notes

- This plugin uses no build step.
- The block is dynamic-rendered via PHP (`render.php`), so it always renders on the front end.
