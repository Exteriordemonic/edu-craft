# EduCraft Tasks

This file defines the implementation backlog for the recruitment project.

Each task should be completed on its own feature branch.
Each task includes a clear goal, scope, out-of-scope section, success story, and verification notes.

---

✅ ## Task 01 — Domain plugin setup

### Branch
`feature/domain-plugin-setup`

### Goal
Create a project-specific domain plugin named `edu-craft-domain` and prepare a clean structure for all business logic.

### Scope
- create plugin directory
- add main plugin bootstrap file
- add plugin header
- create internal folder structure
- wire include/boot process
- prepare modules/placeholders for:
  - CPT
  - taxonomy
  - WooCommerce
  - REST
  - admin
  - CLI/demo content helpers
- ensure plugin can be activated safely

### Out of scope
- actual CPT registration
- actual taxonomy registration
- checkout logic
- REST endpoints
- data seeding

### Success story
- plugin exists under `web/wp-content/plugins/edu-craft-domain`
- plugin can be activated
- bootstrap structure is readable
- plugin is ready for future domain modules
- no domain logic is added to the theme

### Verification
- activate plugin in wp-admin
- confirm no PHP warnings/fatals
- confirm plugin files load correctly

### Suggested commit type
`feat`

---

✅ ## Task 02 — Case Study CPT and Industry taxonomy

### Branch
`feature/case-study-cpt-taxonomy`

### Goal
Register the core content model for Case Studies.

### Scope
- register `case_study` CPT
- register `industry` taxonomy
- define labels
- define supported features
- enable archive
- ensure taxonomy is attached correctly

### Out of scope
- ACF fields
- single template rendering
- archive filtering
- demo content

### Success story
- Case Study menu appears in admin
- Industry taxonomy appears in admin
- user can create Case Studies
- user can assign Industry terms
- Case Study archive exists

### Verification
- create one draft/published Case Study
- create taxonomy terms
- assign taxonomy terms
- verify admin labels and archive URL

### Suggested commit type
`feat`

---

✅ ## Task 03 — ACF fields for Case Study

### Branch
`feature/case-study-acf-fields`

### Goal
Add and sync ACF field groups required by the assignment.

### Scope
- create ACF field group for `case_study`
- fields:
  - client
  - short_description
  - gallery
  - client_url
- verify editor UX
- commit ACF JSON files

### Out of scope
- frontend rendering
- archive logic
- WooCommerce changes

### Success story
- ACF fields appear on Case Study edit screen
- values can be saved
- ACF JSON files are synced into repo
- field naming is clean and consistent

### Verification
- edit Case Study in admin
- fill fields
- save post
- confirm JSON sync

### Suggested commit type
`feat`

---

✅ ## Task 04 — Single Case Study rendering

### Branch
`feature/case-study-single-template`

### Goal
Build a minimal, readable single Case Study page using the existing FSE-first theme approach.

### Scope
- decide FSE/template integration point
- create or wire single Case Study template
- render:
  - title
  - featured image
  - short description
  - client
  - industry
  - client URL
  - gallery
  - main content
- use Bootstrap 5 classes
- handle missing optional fields gracefully

### Out of scope
- archive filtering
- WooCommerce checkout logic
- advanced custom design

### Success story
- reviewer can open a Case Study
- all important data is visible
- layout is clean and understandable
- template fits FSE wrapper approach

### Verification
- create realistic sample post
- confirm rendering with full data
- confirm rendering when some optional fields are empty

### Suggested commit type
`feat`

---

✅ ## Task 05 — Archive Case Study template with filter UI

### Branch
`feature/case-study-archive-filter`

### Goal
Implement Case Study archive filtering without full page reload using REST API + WordPress Interactivity API.

### Scope
- create or wire archive template
- render filter UI
- create REST endpoint for filtered results
- implement Interactivity API store/actions/state
- update visible results dynamically
- support loading state
- support empty state
- support URL query parameter for active filter
- initialize archive state from URL query parameter

### Out of scope
- pagination unless needed for basic clarity
- advanced animation
- search
- multi-filter combinations

### Success story
- archive page loads all Case Studies by default
- clicking an industry filter updates results without full reload
- current filter is reflected in URL
- opening filtered URL directly restores same view
- empty results are handled cleanly

### Verification
- no filter scenario
- valid filter scenario
- invalid/nonexistent filter scenario
- reload page with active filter query param
- use browser back/forward if implemented

### Suggested commit type
`feat`

---

## Task 06 — WooCommerce B2B NIP checkout logic

### Branch
`feature/woo-b2b-nip-checkout`

### Goal
Add conditional NIP handling in checkout based on cart product categories.

### Scope
- detect whether cart contains at least one product in category `B2B`
- display NIP field conditionally
- require NIP only when condition is met
- validate NIP server-side
- normalize/sanitize submitted value
- save NIP in order meta
- display NIP in admin order screen

### Out of scope
- integration with external tax systems
- VIES validation
- invoice generation
- customer account area enhancements

### Success story
- cart without B2B product does not require NIP
- cart with at least one B2B product requires NIP
- product with multiple categories is handled correctly
- invalid NIP blocks order submission
- valid NIP is saved and visible in admin

### Verification
- cart with non-B2B products
- cart with B2B product
- cart with product assigned to B2B + another category
- invalid NIP format
- invalid checksum
- valid NIP saved in admin

### Suggested commit type
`feat`

---

## Task 07 — Demo content setup with DDEV + WP-CLI

### Branch
`feature/demo-content-setup`

### Goal
Prepare repeatable local demo data setup.

### Scope
- create product categories
- create products
- create industry terms
- create Case Studies
- document or script DDEV + WP-CLI commands
- ensure seeded content matches assignment requirements

### Out of scope
- production migration scripts
- external media imports
- multilingual content

### Success story
- local environment can be populated quickly
- reviewer/developer can recreate content without manual clicking
- content structure reflects assignment brief

### Verification
- run documented setup commands
- confirm categories exist
- confirm products exist
- confirm Case Studies exist
- confirm taxonomy assignments exist

### Suggested commit type
`feat`

---

## Task 08 — Final notes and delivery polish

### Branch
`feature/final-notes-and-polish`

### Goal
Prepare final review-ready documentation and cleanup.

### Scope
- complete `FINAL_NOTES.md`
- document architectural decisions
- explain plugin vs theme decision
- explain REST + Interactivity API decision
- explain manual NIP validation decision
- explain ACF Pro usage decision
- ensure setup instructions are complete
- perform final documentation cleanup

### Out of scope
- major refactoring
- redesign
- new features

### Success story
- repository is easy to review
- all key architectural decisions are justified
- reviewer can understand tradeoffs quickly

### Verification
- read docs from top to bottom
- confirm no placeholders remain
- confirm notes align with actual implementation

### Suggested commit type
`docs`

---

# Optional task execution template

Use this structure when handing a task to the AI agent.

## Task handoff template

### Current task
[task name]

### Branch
[branch name]

### Goal
[one sentence]

### Constraints
- follow SPEC.md
- follow IMPLEMENTATION_PLAN.md
- work only in current scope
- do not refactor unrelated code
- use Git Flow literally: `git flow feature start <feature-name>` and `git flow feature finish <feature-name>`

### Deliverables
- implementation
- short technical summary
- manual test checklist
- suggested commit message