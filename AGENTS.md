# SpendWise project guide

## Purpose

SpendWise is a university Web Programming project for tracking personal income, expenses, budgets, and reports. The current application is a static frontend demo; account and financial data are not connected to a backend.

## Technology and implementation status

- Implemented: HTML, CSS, JavaScript, Bootstrap 5.3.3 loaded from jsDelivr, and JSON sample transaction data.
- Planned backend: PHP with FlightPHP, MySQL, and PDO. No PHP routes, database access, authentication service, or persistence are implemented yet.

## Project structure

- `frontend/index.html`: SPA shell, sidebar navigation, page title, and `view-container`.
- `frontend/assets/css/style.css`: application styles and responsive rules.
- `frontend/assets/js/app.js`: view loader, hash handling, sample data, and page initialization.
- `frontend/assets/data/sampledata.json`: demo transactions fetched at startup.
- `frontend/views/`: one HTML fragment per page: dashboard, transactions, budgets, reports, profile, login, and register.
- `backend/config/`, `backend/dao/`, `backend/logs/`, `backend/middleware/`, `backend/routes/`, and `backend/services/`: reserved directories for future backend work. They currently contain only placeholders.
- `database-diagram.png`: planned database design artifact.
- `README.md`: project overview and planned improvements.
- `docs/AI_LOG.md`: milestone-by-milestone record of AI assistance, when maintained.

## Frontend conventions

- Serve the `frontend/` directory through a web server. `index.html` loads `assets/css/style.css` and `assets/js/app.js`; the loader fetches `views/<page-id>.html` and startup fetches `assets/data/sampledata.json`, relative to the frontend root.
- Navigation controls use matching `href="#<page-id>"` and `data-page="<page-id>"` values. `showPage()` validates page IDs, loads the matching view, updates `#page-title` and the active `.nav-link`, and updates the URL hash. The click handler is delegated so controls inside fetched views work. One `hashchange` listener supports direct hash changes.
- Keep the shell IDs `view-container` and `page-title` in sync with JavaScript references. View filenames and page IDs are lowercase and match exactly.
- Put view-specific setup in `initializePage(pageId)`. Check that elements exist before attaching handlers because only one view is mounted at a time. Shared transaction rendering and dashboard updates must tolerate missing target elements.
- Views are HTML fragments inserted into the shell, not standalone documents. Use existing `page-section`, `panel`, `form-control`, and button styles where appropriate. The loader removes `.hidden` from the inserted page section.
- Login and registration are presentation and client-side validation demos only. Do not imply that credentials are sent, stored, or authenticated.

## Frontend and backend separation

Keep browser UI and client-side behavior under `frontend/`. When backend work is authorized, keep routing, business services, data access, middleware, configuration, and logs in their corresponding `backend/` areas. Never put secrets or database credentials in frontend files.

## Security

Never commit secrets, database credentials, JWT secrets, API keys, real passwords, or private user data. Use ignored local environment files for development secrets and sanitized examples for configuration templates. Do not log submitted passwords.

## Coding guidelines

- Preserve existing pages, styling, navigation, sample-data behavior, and handlers unless a requested change requires otherwise.
- Use lowercase page IDs, matching view filenames, and descriptive DOM IDs. Avoid unnecessary dependencies and frameworks.
- Validate user input in the browser for usability, and validate it again on the server once backend endpoints exist.
- Check JavaScript syntax, fetch paths, direct hash loads, navigation, and affected forms after changes. State which checks were actually run.

## Instructions for AI coding assistants

- Inspect relevant files, Git status, and project conventions before editing.
- Preserve unrelated user work. Do not reset, overwrite, or discard changes without explicit instruction.
- Make the smallest changes that satisfy the request and explain the files changed and why.
- Do not claim code works unless it was tested; describe untested behavior and limitations honestly.
- Do not commit, push, merge, or publish changes unless the user explicitly asks.
