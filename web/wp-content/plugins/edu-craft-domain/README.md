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
