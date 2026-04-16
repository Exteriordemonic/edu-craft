# Edu Craft Domain Plugin

Project plugin for EduCraft domain/business logic.

## Scope in Task 01

- plugin bootstrap and module loader
- module placeholders for CPT, taxonomy, WooCommerce, REST, admin, CLI
- defensive dependency checks for ACF and WooCommerce

## Runtime requirements

- Advanced Custom Fields (Free or Pro)
- WooCommerce

If dependencies are missing:

- admin: error notice is shown
- frontend: request is stopped with an explicit error message

## Demo content (DDEV + WP-CLI)

From the project root (where DDEV is configured), with the stack running:

```bash
ddev wp edu-craft seed-demo
```

This command is **idempotent**: it creates or updates WooCommerce product categories, simple products (by SKU), `industry` terms, and published `case_study` posts (by slug). At least one product is in the **B2B** category (`b2b`) so checkout NIP behaviour can be tested.

Requirements: **WooCommerce**, **ACF**, and this plugin must be active. Without a full WordPress bootstrap, run the same subcommand via `wp edu-craft seed-demo` inside any environment where WP-CLI is available.
