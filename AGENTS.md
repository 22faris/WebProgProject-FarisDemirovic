# SpendWise project guide

## Purpose and status

SpendWise tracks income, expenses, budgets, and reports. The frontend is a static HTML/CSS/JavaScript demo using sample JSON and is not connected to the backend for financial persistence. Milestone 2 provides a MySQL schema and PDO DAOs. Milestone 3 adds a FlightPHP JSON CRUD API, entity services, OpenAPI documentation/Swagger UI, and a Frankfurter-backed converter. Authentication, authorization, and user-scoped access are deferred to Milestone 4; do not claim the current open CRUD API is secure for public deployment.

## Project structure

- `frontend/index.html`, `frontend/views/`, `frontend/assets/css/`, `frontend/assets/js/`: existing SPA. `frontend/assets/data/sampledata.json` is demo data.
- `backend/composer.json` and `backend/composer.lock`: one Composer project and locked FlightPHP dependency. Install with `cd backend; composer install`. Do not commit `backend/vendor/`.
- `backend/public/index.php`: FlightPHP front controller and request/response helpers; `backend/public/.htaccess` rewrites API paths.
- `backend/routes/api.php`: HTTP routes only. Routes parse/validate transport structure and return JSON; they must not contain SQL or call DAO methods.
- `backend/services/`: business logic and input validation. Every SQL-backed entity has a service corresponding to its DAO. `ServiceFactory` wires each service to its DAO. Services are the only application layer that calls DAOs. Do not put SQL in routes or services.
- `backend/services/ExchangeRateService.php`: external Frankfurter adapter; no key is needed. Use its current documented v2 rate format and a bounded timeout.
- `backend/dao/`: PDO prepared CRUD; `BaseDAO` owns generic SQL and entity DAOs set table/column allow-lists. Keep schema identifiers in this layer.
- `backend/config/config.example.php`: safe MySQL template; copy to ignored `config.php` for local credentials. Never put secrets in frontend or tracked config.
- `backend/database/schema.sql`: existing five-table schema; it drops and recreates tables when imported. Inspect before running.
- `backend/openapi.yaml`, `backend/docs/index.html`: API contract and Swagger UI. Keep documented paths, methods, schemas, errors, and examples synchronized with routes.
- `backend/tests/dao_crud_test.php`: rollback-only DAO exercise; use a disposable database.
- `docs/AI_LOG.md`: milestone-by-milestone record, including genuine generated errors or clearly marked review placeholders.

## Database and DAO conventions

The schema entities are `users`, `user_settings`, `categories`, `transactions`, and `budgets`. User settings are one-to-one with users and use `user_id` as their primary key and API resource identifier. Transaction and budget category references must belong to the same user. Preserve these relationships and existing sample data. MySQL uses InnoDB, utf8mb4, PDO exception mode, native prepared statements, and allow-listed dynamic DAO identifiers. Missing lookups return `null`; missing update/delete operations return `false`; database exceptions are handled at the API boundary.

Services validate required fields, types, limits, enum values, positive IDs, dates/month boundaries, and reference relationships. On user writes, hash the submitted password into the existing `password_hash` column; never return or log it. Map invalid input to JSON 400, missing records to 404, constraint conflicts to 400, external provider outages to a friendly 503, and unexpected errors to a generic 500. Never include SQL, stack traces, credentials, or internal paths in responses. Do not implement pretend authentication.

## Frontend conventions

Serve `frontend/` through Apache. Keep existing SPA shell IDs (`view-container`, `page-title`), view filenames/page IDs, loader behavior, and working sample-data/forms intact unless a task requires a change. Fetched views are fragments. Put view behavior in `initializePage(pageId)` and check elements exist. The Reports currency converter may call the backend API, but other existing demo behavior remains sample-only. Login and registration screens must not claim authentication or account persistence.

## Security and Git

Never commit database credentials, API keys, real passwords, or private financial data. Keep `config.php`, logs, and `vendor/` ignored. Authentication and ownership authorization are future work; do not expose this unauthenticated API to the public internet. Preserve uncommitted user changes. Do not reset, overwrite, commit, push, merge, publish, or create a PR unless explicitly asked. Milestone 3 work belongs on `milestone3`; inspect branch/status before editing.

## Documentation and verification

README setup commands must match XAMPP paths and actual configuration. OpenAPI must match the running API. Report exactly which checks ran and which require a configured database or browser. Do not claim database/API integration passed without testing against a real database. Never import the destructive schema into valuable data or run CRUD verification against an important database.
