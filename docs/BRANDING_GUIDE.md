# Branding Guide

## Identity (working, configurable)

- **Product:** EduDEV. **Company:** MacoDev Limited. **Flagship:** EduDEV AI Tutor
- **Slogan:** Learn Smarter. Achieve More.
- **Status:** working identity. Trademark, company-name and domain checks have **not** been verified.

## Changing branding

1. **Admin → Branding** (needs the `settings.branding.manage` permission) changes the name, slogan, colours, contact details, links and social URLs. Changes apply immediately and are recorded in the audit log. "Reset to defaults" removes every override.
2. **Defaults** live in `config/edudev.php` and can be set from `.env` (`BRAND_*`).
3. **Logo and favicon** are set by file path only (`BRAND_LOGO_PATH`, `BRAND_FAVICON_PATH`). They aren't editable from the UI, so uploaded files can't be abused.

In templates, always use `$brand->get('product_name')` or `brand('product_name')`. Never type the product name in a template.

## Colours

| Token / utility | Value | Use |
|---|---|---|
| `primary` | #0B2545 Deep Navy | Main buttons, headings, hero backgrounds. 15.4:1 with white. |
| `secondary` | #13A987 Education Green | **Decoration and large text only.** About 3:1 with white, which fails for small text. |
| `secondary-strong` | ≈ #0E7C63 (derived) | Green buttons, links and small labels. About 5.1:1 with white. |
| `gold` | #F2A541 Achievement Gold | Highlights and calls to action. Pair with navy text (`gold-foreground`), about 7.5:1. Never white text on gold. |
| `surface` / `surface-alt` | #FFFFFF / #F5F7FA | Backgrounds |
| `ink` | #1F2937 | Main text |
| `ink-soft` | #475569 | Secondary text, especially on `surface-alt` |
| `muted` | #64748B | Hints on **white only** (4.75:1). On #F5F7FA it is 4.4:1, below the 4.5:1 minimum. |
| `success` / `warning` / `danger` / `info` (+ `-soft`) | | Always pair with an icon or words, never colour alone |
| `focus` | #2563EB | Keyboard focus ring (3px) |

If an admin changes the primary colour, it must still work behind white text. The admin page reminds them, but doesn't block the change.

## Logo

`resources/views/components/app-logo-icon.blade.php` is an **original draft mark**: an open book in green with a gold "achievement spark" on a navy rounded tile. It uses the brand colour variables, so it follows colour changes. Replace it with final artwork when it's ready.

## Components

`btn-primary`, `btn-secondary`, `btn-gold`, `btn-outline`, `card`, `eyebrow` and `skip-link` are defined in `resources/css/app.css`. Flux components use navy as their accent in light mode and green in dark mode.

**Typeface:** Plus Jakarta Sans (served by fonts.bunny.net).
