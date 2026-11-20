# AI assistance log

## Milestone 1 review fixes — 2026-10-25

### Tools and contributions

- **OpenAI Codex (GPT-6)** was used to inspect the repository, read the professor review request, inspect Git status and existing frontend files, add the login and registration SPA views, extend routing and form initialization, write the root `AGENTS.md`, and prepare this log.
- Shell tools (`git`, PowerShell, `rg`, and `node --check`) were used through Codex to inspect project state and verify files. No external AI service or authentication backend was used.

### Important prompts

- The supplied task requested preserving the existing SPA, creating static login/register screens, documenting actual architecture and planned backend technologies in `AGENTS.md`, maintaining this log, verifying changes, and providing safe PR instructions without merging.
- The prior project debugging request asked Codex to inspect the real files and Git changes, diagnose the SPA view-loading issue, make minimal fixes, and report verification accurately.

### AI mistakes and corrections

1. During earlier repository inspection, Codex issued an `rg` command with malformed regular-expression quoting. `rg` returned a parse error instead of searching. This was recognized as a command construction error; subsequent checks used simpler search patterns and direct file/Git inspection. It did not modify project files.
2. During this review, Codex attempted to pass the `backend` directory itself to PowerShell `Get-Content`, which returned an access error because the path is a directory. The directory was then inspected with `Get-ChildItem`; no backend source files were found, only the reserved directories/placeholders. This did not modify project files.
3. A later PowerShell search command used double quotes around patterns containing escaped double quotes. PowerShell interpreted the inner quotes as syntax and attempted to run words from the pattern as commands. The search did not run; the command needs PowerShell single-quoted patterns (or a here-string) and was not needed for the implementation.

These examples document command/tool output mistakes, not application defects. No authentication implementation or backend behavior was generated for this milestone.

## Milestone 2 database and DAO foundation — 2026-10-28

### Tools and contributions

- OpenAI Codex inspected `AGENTS.md`, `README.md`, the frontend's transaction/profile examples, the existing ERD, Git status/history, and backend placeholder directories before editing.
- Codex implemented a MySQL schema and ERD, PDO DAO classes, a local configuration template, a CRUD demonstration script, and documentation updates in response to the user's Milestone 2 request.
- PHP 8.2.12 lint passed for every backend PHP file. The CLI has `pdo_mysql`; the available MySQL client identifies as MariaDB 10.4.32. No MySQL/MariaDB server process is running and the ignored local `backend/config/config.php` is absent. The CRUD demonstration was attempted and stopped at the missing-config check, before any database operation. CRUD and SQL import are therefore unverified and still require a configured running server.

### Important prompt

- The user requested implementing Milestone 2 database and DAO requirements, preserving Milestone 1, updating project instructions and documentation, demonstrating CRUD for each entity, running possible checks, and preparing a `milestone-2` branch without merging.

### AI mistakes and corrections

- An initial attempt to create the branch was denied because the repository's `.git` metadata is read-only in the default sandbox. The command was retried with an explicit escalation request, approved, and `milestone-2` was created from the inspected `milestone-1` branch. No project files had been edited at that point.
## Milestone 3 business logic and API — 2026-11-19

### AI tools and contributions

- OpenAI Codex (GPT-6) inspected the real repository tree, schema, DAOs, config, Composer availability, frontend, and Git branch/status; switched to `milestone3`; then authored the FlightPHP API, services, OpenAPI/Swagger page, currency conversion service, frontend Reports converter, and project documentation.
- Shell tools (`git`, PowerShell, Composer, PHP, Node) were used for inspection and verification. The current Frankfurter v2 endpoint and response format were checked against its official documentation.

### Important prompt

- The supplied Milestone 3 implementation request required preserving the five existing entities/DAOs, implementing validated services and FlightPHP CRUD routes, documenting only implemented endpoints, adding Frankfurter conversion, and updating AGENTS.md, README.md, and this log.

### AI mistakes and corrections

1. An early service-layer draft included raw SQL for checking related user/category records, which violated the project's DAO boundary. This was caught during review before API validation; the checks were changed to use `UserDAO`/`CategoryDAO` `findById()` methods through the service layer. No route or service now contains SQL statements.
2. The first route draft evaluated `serviceFor()` before parsing a request body, so a malformed JSON POST tried to connect to MySQL before it could return 400. An actual HTTP check showed the incorrect 500; body/ID validation was reordered ahead of service creation. That check also exposed that the pre-existing connection guard rejected an empty MySQL password even though the ignored local XAMPP config uses one; it now allows an empty password but still requires a DSN and username.

### Verification status

- Composer resolved and installed `flightphp/core` v3.19.5 and wrote `backend/composer.lock`. XAMPP's PHP ZIP extension had to be enabled for the install command; the extension exists locally but is disabled in the default CLI configuration.
- All backend PHP files linted successfully; JavaScript syntax and Composer manifest validation passed. HTTP checks confirmed malformed JSON and invalid IDs return 400, an unknown route returns JSON 404, invalid currency returns 400, and live EUR-to-BAM conversion returned 200. With sandbox egress blocked the same rate endpoint returned its friendly 503 response.
- The added `backend/tests/service_validation_test.php` passed all seven no-MySQL service validation cases. After the user started XAMPP MySQL, PDO connected to `spendwise`; read-only counts matched the expected sample rows (1 user, 1 setting, 5 categories, 5 transactions, 4 budgets). Collection and sample-item GETs returned 200 JSON for all entities through Flight, and all five collection GETs plus the converter returned 200 through XAMPP Apache. The Swagger HTML and OpenAPI YAML also returned HTTP 200. No database rows were changed.
- Swagger UI was not visually rendered because the computer-use browser host was unavailable. Composer install completed with no dependency changes; it emitted a non-blocking warning that Packagist metadata could not be fetched, but all locked packages were already installed.
