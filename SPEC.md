# EduCraft Recruitment Project Spec

## 1. Project overview

This project implements a recruitment assignment for a WordPress + WooCommerce store called **EduCraft**.

The project is based on an existing custom starter theme:

- Theme: `edu-craft-theme`
- Architecture direction: **FSE-first**
- Content approach: **main page and content areas built from blocks**
- Styling approach: **Bootstrap 5 + SCSS**
- Build tool: **Webpack**
- Frontend interaction: **WordPress Interactivity API**
- Business/domain logic: **separate project plugin**

The goal is to deliver a clean, production-oriented implementation that clearly separates:

- **presentation layer** → theme
- **domain/business logic** → plugin

---

## 2. Architectural principles

### 2.1 Theme responsibilities

The `edu-craft-theme` is responsible for:

- Full Site Editing structure
- `theme.json`
- templates / template parts / patterns
- visual layout and presentation
- block-based page composition
- ACF block rendering used in content areas
- Bootstrap-based styling
- frontend integration of dynamic content where needed

FSE is treated as a **layout wrapper**, not as a place for core domain logic.

Typical page structure:

- header / navigation
- content area
- CTA area
- footer

Main page content and content sections should be built using blocks.

---

### 2.2 Plugin responsibilities

A separate plugin named **`edu-craft-domain`** is responsible for:

- custom post type registration
- custom taxonomy registration
- domain-specific hooks and logic
- WooCommerce checkout business rules
- server-side validation
- order meta handling
- REST endpoints for dynamic filtering
- integration points used by Interactivity API
- optional WP-CLI/DDEV seed helpers

This plugin should live in:

`web/wp-content/plugins/edu-craft-domain`

---

### 2.3 Why plugin instead of theme

Custom post types and business logic should not depend on the active theme.

This project intentionally uses a project-specific plugin instead of putting domain logic in the theme because:

- content should remain portable
- business logic should remain active even if presentation changes
- WooCommerce checkout rules are domain logic, not theme logic
- the separation is easier to justify and maintain

A `mu-plugin` was considered, but rejected for this task because a standard plugin provides:

- simpler setup
- standard activation flow
- fewer lifecycle constraints
- easier local development and review

---

## 3. Main functional scope

## 3.1 Case Study custom post type

A custom post type named `case_study` must be registered programmatically in the `edu-craft-domain` plugin.

### Required fields

Case Study should support the following data:

- title
- content/editor
- featured image
- ACF field: `client`
- ACF field: `industry`
- ACF field: `short_description`
- ACF field: `gallery`
- ACF field: `client_url`

### Notes

- ACF Pro is used in this project (license provided by project owner)
- field groups should be stored via **ACF JSON**
- implementation may use ACF Pro field capabilities where they improve editor UX

---

## 3.2 Industry taxonomy

A taxonomy for Case Studies must be registered programmatically.

### Taxonomy

- name: `industry`

### Example terms

- IT
- Finanse
- E-commerce
- Edukacja
- Healthcare

The taxonomy is used for archive filtering.

---

## 3.3 Single Case Study template

A single Case Study page must be implemented.

### Requirements

- clean, minimal, readable layout
- uses theme presentation layer
- displays all relevant ACF fields
- uses Bootstrap 5 classes for layout/styling
- keeps logic minimal in templates
- integrates with FSE structure

### Expected content areas

- title
- featured image
- short description
- client name
- industry
- client URL
- gallery
- main content

---

## 3.4 Archive Case Study template

An archive page for Case Studies must be implemented.

### Requirements

- list Case Studies
- filter by `industry`
- filtering without full page reload
- filter state reflected in URL query parameter
- sharable filtered URLs
- clean and understandable UX
- empty state and loading state should be handled

### Chosen technical approach

- backend: **REST API**
- frontend state/interactions: **WordPress Interactivity API**
- presentation: theme layer + Bootstrap 5

### URL behavior

Filtered archive should be shareable via query param, for example:

`/case-study/?industry=it`

The archive should initialize from the URL state when possible.

---

## 3.5 WooCommerce checkout modification

A business rule must be implemented in checkout.

### Rule

If the cart contains at least one product assigned to the category **`B2B`**, checkout must display a **NIP** field.

### Requirements

- field appears only when at least one B2B product is in cart
- products may belong to multiple categories
- mixed category products must be handled correctly
- NIP becomes required only when the condition is met
- validation must happen on the server side
- NIP must be saved in order meta
- NIP must be visible in admin order view

### Validation strategy

Validation is implemented manually in PHP using WooCommerce hooks.

Validation includes:

- basic format normalization
- format validation
- checksum validation

No external package is required.

---

## 3.6 Demo content

Project demo content should be prepared using WP-CLI through DDEV.

### Products to add

- Kurs JavaScript od podstaw
- Warsztat React dla zespołów
- Szkolenie AI w marketingu
- Bootcamp UX/UI
- Akademia Project Managera

### Product categories

- Programowanie
- Marketing
- Design
- Zarządzanie
- B2B
- Indywidualne
- Promocja

### Case Studies to add

- Jak firma X zredukowała time-to-market o 40%
- Warsztat React dla zespołu 12 osób
- AI w kampanii Black Friday

### Industry terms

- IT
- Finanse
- E-commerce
- Edukacja
- Healthcare

Demo data can be inserted via DDEV + WP-CLI commands and/or helper command documentation.

---

## 4. Technical decisions

## 4.1 FSE approach

This project uses an FSE-first theme that acts as the presentation wrapper.

Important rule:

- layout is managed through FSE
- content is composed from blocks
- business logic stays outside the theme where possible

---

## 4.2 ACF approach

ACF fields are managed with:

- ACF UI
- local JSON sync committed to repository

This keeps implementation fast and practical while remaining portable in the repo.

---

## 4.3 Styling approach

Styling is based primarily on:

- Bootstrap 5
- SCSS
- minimal custom additions only where required

The goal is clarity and speed, not custom design polish.

---

## 4.4 Build approach

Assets are built using the existing Webpack setup from the starter theme.

No new build system should be introduced.

---

## 4.5 Interaction approach

For archive filtering, the chosen approach is:

- REST API for data fetching
- WordPress Interactivity API for frontend interaction/state

This is intentional to demonstrate modern WordPress capabilities in a practical feature.

---

## 5. Non-goals

The following are explicitly out of scope:

- advanced custom design
- unit tests
- legacy browser compatibility
- extreme-scale performance optimization
- invoice systems
- EU VAT validation APIs
- full ERP/CRM integrations

---

## 6. Deliverables

The final repository should include:

- working `edu-craft-theme`
- working `edu-craft-domain` plugin
- ACF JSON files
- WooCommerce checkout logic
- Case Study single/archive implementation
- demo data instructions or helper commands
- documentation files:
  - `SPEC.md`
  - `IMPLEMENTATION_PLAN.md`
  - `TASKS.md`
  - `FINAL_NOTES.md`

---

## 7. Quality expectations

Implementation should be:

- clear
- maintainable
- WordPress-native
- easy to review
- separated by responsibility
- scoped correctly
- readable for a recruitment reviewer

The project is evaluated more on **structure and engineering decisions** than on visual complexity.

---

## 8. Mandatory Git workflow

All task branches must be handled with Git Flow commands literally:

- start with `git flow feature start <feature-name>`
- finish with `git flow feature finish <feature-name>`

Do not replace this workflow with manual branch creation or manual merge steps. If Git Flow is unavailable, stop and ask the user.