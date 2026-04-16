# Final Notes

## 1. Project summary

This project implements a recruitment assignment for an EduCraft WordPress + WooCommerce store.

The implementation is based on an existing custom starter theme and extends it with a domain-specific plugin to keep business logic separate from presentation.

Main delivered areas:

- custom Case Study content type
- Industry taxonomy
- single Case Study template
- filtered Case Study archive
- WooCommerce B2B checkout rule with NIP validation
- repeatable demo content setup

---

## 2. Why I used a plugin for domain logic

I chose a separate project-specific plugin (`edu-craft-domain`) for content model and business logic instead of putting everything in the theme.

### Reasoning

- custom post types and domain rules should not depend on presentation
- business logic is easier to maintain when separated from the theme
- WooCommerce checkout customization is not a theme concern
- this structure makes future theme changes safer

I considered using a `mu-plugin`, but for this assignment a standard plugin was a better fit because it offers a simpler lifecycle and easier local development workflow.

---

## 3. Why I used REST API + WordPress Interactivity API

For Case Study archive filtering, I chose:

- REST API on the backend
- WordPress Interactivity API on the frontend

### Reasoning

- filtering without full page reload was a requirement
- REST keeps the data flow clear and explicit
- Interactivity API is a modern WordPress-native way to manage state and interactions
- this approach fits the existing FSE/block-oriented direction of the theme

I also added support for URL query parameters so filtered archive states can be shared directly.

---

## 4. Why I validated NIP manually

I implemented NIP validation manually in PHP using WooCommerce hooks.

### Reasoning

- scope is small and well-defined
- introducing an additional dependency was unnecessary
- manual implementation keeps the feature transparent and easy to review
- checksum validation for Polish NIP is straightforward enough for this use case

Validation includes:

- normalization/sanitization
- format validation
- checksum verification

---

## 5. ACF Pro usage note

This project uses ACF Pro (license provided by the project owner).

I used ACF Pro capabilities where they improve editor experience while keeping the implementation practical and review-friendly.

ACF Pro is especially useful here for:

- more flexible gallery handling
- additional reusable block patterns
- stronger editorial UX where needed

The implementation still avoids overengineering and keeps field setup focused on assignment scope.

---

## 6. Theme and FSE approach

The project uses an FSE-first theme, but FSE is treated mainly as the layout/presentation wrapper.

### Practical meaning

- header/navigation, content wrapper, CTA areas, and footer belong to theme layout
- content areas are built from blocks
- logic and domain behavior stay in the plugin where appropriate

This keeps the theme aligned with presentation responsibility while still embracing modern WordPress structure.

---

## 7. Styling approach

The visual layer is intentionally minimal.

I used:

- Bootstrap 5
- SCSS
- lightweight custom styling only where necessary

This decision was made because the assignment explicitly prioritizes structure and engineering decisions over advanced design work.

---

## 8. Demo content approach

I prepared the project to support local demo content setup using DDEV + WP-CLI.

### Why

- faster local setup
- repeatable environment
- easier reviewer onboarding
- less manual clicking in wp-admin

---

## 9. Tradeoffs

Some decisions intentionally favored clarity and speed over abstraction.

### Examples

- standard plugin instead of `mu-plugin`
- manual NIP validator instead of external package
- focused archive filtering instead of broader filtering/search system
- minimal visual polish in favor of correctness and structure

These tradeoffs were deliberate and appropriate for the assignment scope.

---

## 10. Possible future improvements

If this project were continued beyond the recruitment scope, I would consider:

- stronger error/loading UX for archive filtering
- pagination or combined filters
- more complete admin/editor UX around Case Studies
- tests for domain logic
- WP-CLI custom command for seeding instead of only documented commands
- stronger separation of service/helper layers inside plugin if scope grows
- optional move of critical logic to `mu-plugin` in a production environment

---

## 11. Setup notes

Document local setup here, for example:

- how to start DDEV
- how to install dependencies
- how to activate theme/plugin
- how to import or seed content
- how to test B2B checkout behavior
- how to test archive filtering

Replace this section with exact final commands once implementation is complete.