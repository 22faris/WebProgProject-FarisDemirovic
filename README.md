# SpendWise | Expense Tracker

SpendWise is a university Web Programming project. Its browser interface remains a static demo with sample transaction data; Milestone 3 adds an independent FlightPHP REST API for the existing MySQL database and a live currency conversion feature.

## Architecture

```text
frontend/ (HTML/CSS/JS SPA and sample JSON)
backend/public/index.php (FlightPHP front controller)
  -> backend/routes/api.php -> backend/services/ServiceFactory.php
  -> entity services -> backend/dao/ -> MySQL
backend/openapi.yaml + backend/docs/index.html (Swagger UI)
```

The five database entities are users, user_settings, categories, transactions, and budgets. The database schema and PDO DAOs from Milestone 2 remain in place. Services validate inputs, enforce entity relationships, and call DAOs; routes only parse HTTP input and serialize responses. CRUD endpoints are unauthenticated for this milestone; do not expose this demo API to the public internet. User passwords supplied to the user API are hashed, and `password_hash` is never returned.

## Requirements

- XAMPP with Apache, MySQL, PHP 8.1+ and PDO MySQL enabled.
- Composer 2. FlightPHP is installed from `backend/composer.json`.
- Apache `mod_rewrite` and `.htaccess` overrides enabled for the API front controller.

Install backend dependencies once:

```powershell
cd backend
composer install
```

If Composer says ZIP is missing on XAMPP, enable `extension=zip` in the active `php.ini`, restart the terminal, and rerun `composer install`.

## Local XAMPP setup

1. Put/keep the repository under `C:\xampp\htdocs\WebProgProject-FarisDemirovic`.
2. Start Apache and MySQL in XAMPP Control Panel.
3. Import `backend/database/schema.sql` using phpMyAdmin. **Importing this file drops and rebuilds the five tables**, so export valuable data first.
4. Copy `backend/config/config.example.php` to `backend/config/config.php`. Set the local MySQL DSN, username, and password. The local file is ignored by Git.
5. From repository root run `cd backend; composer install` if dependencies are not installed yet.
6. Open the SPA at `http://localhost/WebProgProject-FarisDemirovic/frontend/`.
7. Open API docs at `http://localhost/WebProgProject-FarisDemirovic/backend/docs/`.

The Swagger UI page loads its CSS/JavaScript bundle from unpkg, so the visual documentation needs an internet connection. Currency conversion also requires outbound access from PHP to Frankfurter. The API itself is served by XAMPP Apache; `backend/public/.htaccess` forwards API paths to FlightPHP.

## API overview

All API responses are JSON. Lists and records use `{ "data": ... }`; errors use `{ "error": { "code": "...", "message": "..." } }`. Create returns 201, invalid requests 400, missing records 404, unexpected errors 500, and external-rate outages 503. Password hashes are omitted from all user responses.

For each resource below the implemented methods are `GET /api/{resource}`, `GET /api/{resource}/{id}`, `POST /api/{resource}`, `PUT /api/{resource}/{id}`, `PATCH /api/{resource}/{id}`, and `DELETE /api/{resource}/{id}`:

- `/api/users` (user-settings uses `user_id` as its ID)
- `/api/user-settings`
- `/api/categories`
- `/api/transactions`
- `/api/budgets`

Currency conversion: `GET /api/exchange/convert?from=EUR&to=BAM&amount=25`. Example response:

```json
{"data":{"amount":25,"from":"EUR","to":"BAM","rate":1.95583,"converted_amount":48.9,"date":"2026-11-18"}}
```

The backend fetches Frankfurter v2 reference rates (`/v2/rates?base=EUR&quotes=BAM`) with a short timeout and performs the multiplication itself. Frankfurter does not require an API key. The Reports page includes a browser converter that uses this backend endpoint; conversion failures do not affect the rest of the static demo.

## Testing and verification

Check PHP syntax with `Get-ChildItem backend -Recurse -Filter *.php | ForEach-Object { php -l $_.FullName }` and JavaScript with `node --check frontend/assets/js/app.js`. `php backend/tests/service_validation_test.php` checks service input validation without requiring MySQL. For database CRUD verification, configure a **disposable** `spendwise` database and run `php backend/tests/dao_crud_test.php`; it rolls back its fixture transaction. Do not run that test against important data. To try APIs, use Swagger UI or `curl.exe`; POST a user and category first, then create a transaction/budget referencing their IDs. No API mutations are run automatically by the project setup.

The original SPA still loads its local `frontend/assets/data/sampledata.json`; its existing demo forms do not persist to the API. Authentication, ownership authorization, and frontend/database persistence are Milestone 4 or later work.
