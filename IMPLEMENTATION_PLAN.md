# EduCraft Implementation Plan

## 1. Delivery model

The project is delivered incrementally using git flow.

### Mandatory Git Flow commands

For every task, the AI agent must use Git Flow commands literally:

1. Start a feature branch with:
   `git flow feature start <feature-name>`
2. Implement the task and commit on that feature branch.
3. Finish the feature branch with:
   `git flow feature finish <feature-name>`

Rules:

- do not create feature branches with `git checkout -b` or `git switch -c`
- do not replace `git flow feature finish` with manual merge steps
- if Git Flow is unavailable or fails, stop and ask the user before continuing

### Branch strategy

- `main` → stable branch
- `develop` → integration branch
- `feature/*` → task branches

### Workflow per task

1. Start feature branch from `develop`
2. Review relevant files and documentation
3. Prepare task-level implementation plan
4. Implement only the agreed scope
5. Perform manual verification
6. Self-review changes
7. Commit
8. Open PR / merge into `develop`
9. Iterate if needed

---

## 2. Execution principles for AI-assisted work

The AI agent in Cursor acts as an implementer, not as an architecture owner.

### Rules

- Always read `SPEC.md`, `IMPLEMENTATION_PLAN.md`, and current task scope first
- Do not work outside the current task
- Do not refactor unrelated code
- Before implementation, propose a short technical plan
- After implementation, provide:
  - what changed
  - why it changed
  - manual test cases
  - risks / follow-up notes
  - suggested commit message

### Decision model

- user = architect / decision maker
- AI = planner and implementer inside approved scope

---

## 3. Implementation phases

## Phase 0 — Documentation and project alignment

### Goal
Prepare the repository to support structured implementation.

### Outputs
- `SPEC.md`
- `IMPLEMENTATION_PLAN.md`
- `TASKS.md`
- `FINAL_NOTES.md`

### Success criteria
- architecture is documented
- task list is agreed
- implementation order is defined

---

## Phase 1 — Domain plugin foundation

### Goal
Create and wire the `edu-craft-domain` plugin.

### Scope
- create plugin directory
- main plugin bootstrap file
- organize includes/modules
- set up namespacing or consistent prefixing
- prepare loader structure for:
  - CPT registration
  - taxonomy registration
  - Woo hooks
  - REST endpoints
  - admin enhancements
  - optional CLI helpers

### Success criteria
- plugin can be activated
- plugin structure is clean and extensible
- no business logic in theme that belongs in plugin

---

## Phase 2 — Case Study domain model

### Goal
Register Case Study content model.

### Scope
- register `case_study` CPT
- register `industry` taxonomy
- choose supported features
- verify archive support
- verify admin labels and usability

### Success criteria
- Case Study appears in admin
- Industry taxonomy appears in admin
- archive is enabled
- structure is ready for ACF integration

---

## Phase 3 — ACF integration

### Goal
Attach field groups to Case Study content.

### Scope
- create field groups in ACF
- sync to local JSON
- ensure location rules target `case_study`
- confirm fields are editor-friendly

### Expected fields
- client
- industry or taxonomy-related helper field if needed
- short_description
- gallery
- client_url

### Success criteria
- fields appear correctly in admin
- ACF JSON is committed
- fields are available in rendering layer

---

## Phase 4 — Single Case Study rendering

### Goal
Implement a readable single Case Study page.

### Scope
- define single template strategy in FSE-compatible way
- render featured image
- render ACF data
- render main content
- use Bootstrap 5 layout classes
- keep output minimal and clean

### Success criteria
- all required data is visible
- page is readable and coherent
- template follows theme/plugin responsibility split

---

## Phase 5 — Archive filtering with REST + Interactivity API

### Goal
Implement filtered Case Study archive without full page reload.

### Scope
- archive template
- filter UI
- REST endpoint
- Interactivity API store/state/actions
- initialize filter from URL query param
- update URL when filter changes
- loading state
- empty state

### Success criteria
- filtering works without page reload
- selected industry affects visible results
- URL reflects current filter
- shared URL restores same filter state
- archive remains understandable without visual complexity

---

## Phase 6 — WooCommerce B2B NIP checkout rule

### Goal
Implement conditional NIP field and validation.

### Scope
- inspect cart for `B2B` category products
- conditionally add checkout field
- require field only when needed
- normalize and validate NIP server-side
- save to order meta
- show value in admin

### Success criteria
- no B2B product → no required NIP
- B2B product in cart → NIP visible and required
- invalid NIP blocks checkout
- valid NIP is saved
- admin can see stored value

---

## Phase 7 — Demo data via DDEV + WP-CLI

### Goal
Prepare fast local setup for reviewer/demo use.

### Scope
- product categories
- products
- industry terms
- case studies
- helper commands or command documentation

### Success criteria
- local environment can be populated quickly
- content matches assignment requirements
- setup is repeatable

---

## Phase 8 — Final polish and delivery notes

### Goal
Prepare final documentation and review pass.

### Scope
- final setup steps
- architectural justification
- plugin vs theme reasoning
- REST + Interactivity API reasoning
- manual NIP validation reasoning
- ACF Free vs Pro note
- known tradeoffs / future improvements

### Success criteria
- repo is reviewer-friendly
- architectural choices are defendable
- final notes explain tradeoffs clearly

---

## 4. Recommended directory targets

## Theme
`web/wp-content/themes/edu-craft-theme`

Used for:
- FSE templates
- parts
- patterns
- block presentation
- Bootstrap styling
- Interactivity-facing UI integration where presentation belongs

## Plugin
`web/wp-content/plugins/edu-craft-domain`

Suggested module structure:

- `edu-craft-domain.php`
- `includes/`
  - `post-types/`
  - `taxonomies/`
  - `woocommerce/`
  - `rest/`
  - `admin/`
  - `cli/`
  - `helpers/`

This does not need to be overengineered. Keep it flat enough to stay readable.

---

## 5. Manual verification approach

Each feature should be verified manually after implementation.

### Core verification areas

#### Plugin setup
- plugin activates without warnings
- no fatal errors
- hooks load correctly

#### Case Study
- CPT available in admin
- taxonomy available in admin
- entries can be created and assigned

#### Single template
- all fields render
- missing optional fields do not break layout

#### Archive filtering
- default archive renders
- filtering works without reload
- URL query param updates
- direct filtered URL works
- empty state is readable

#### Woo NIP
- cart without B2B product
- cart with B2B product
- mixed category product
- invalid NIP
- valid NIP
- admin order view

#### Demo data
- categories created correctly
- products assigned to categories
- case studies assigned to industries

---

## 6. Definition of done

A task is considered done when:

- implementation matches task scope
- success story is satisfied
- manual verification passes
- no unrelated code was changed
- commit message is prepared
- branch is ready for review/merge into `develop`