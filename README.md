# Web Programming project
My project for the IT 2001 Web Programming course.

# SpendWise – Personal Expense Tracker

## About the Project

SpendWise is a personal finance web application designed to help users manage their money, track their income and expenses, and monitor their spending habits. The project provides a simple and user-friendly interface for organizing financial transactions and keeping track of monthly budgets.

The application includes a dashboard that summarizes financial activity, a transactions page for viewing income and expenses, a budgets page for managing spending limits, a reports page for reviewing financial activity, and a profile page for user-related information.

## Features

Dashboard: View an overview of income, expenses, balance, and recent financial activity.

Transactions: View and organize income and expense records.

Budgets: Monitor budgets and track spending against planned limits.

Reports: Review financial activity and spending patterns.

Profile: View and manage profile-related information.

## Technologies Used

HTML5

CSS

JavaScript

JSON for sample financial data

## Project Goal

The goal of SpendWise is to provide a convenient way to understand personal financial habits, organize transactions, and plan spending more effectively. The project also serves as an opportunity to apply web development concepts, including page structure, styling, JavaScript-based interactivity, and data management.

## Future Improvements

Connect the application to a MySQL database.

Implement user registration and authentication.

Add, edit, and delete transactions.

Allow users to create and update budgets.

Generate more detailed financial reports and visualizations.

Improve data validation and error handling.

## Milestone 2: MySQL schema and DAO layer

Milestone 2 adds a MySQL 8.0+ schema in `backend/database/schema.sql` with five related entities: users, user settings, categories, transactions, and budgets. The SQL file creates the `spendwise` database, defines keys, constraints, and sample rows, and can be re-imported to rebuild the demo database. **Re-import drops and recreates these five tables**, so export any data you need first.

The PHP PDO DAO layer is in `backend/dao/`. `BaseDAO` implements prepared create/read/update/delete operations with an allow-list for writable column names. Entity-specific classes cover all five tables. A missing ID lookup returns `null`, update/delete of a missing row returns `false`, invalid fields raise `InvalidArgumentException`, and PDO/database errors propagate as exceptions. This milestone does not add HTTP routes, login, or connect the static frontend to this database.

### Database setup (XAMPP)

1. Start MySQL in the XAMPP Control Panel.
2. In phpMyAdmin or a MySQL client, import `backend/database/schema.sql`. The script creates and selects `spendwise`, then rebuilds its tables and adds sample data.
3. Copy `backend/config/config.example.php` to `backend/config/config.php` and set the local DSN, username, and password for your MySQL installation. For a non-default port or database, update the DSN. `config.php` is ignored by Git.
4. Make sure the PHP CLI has the PDO MySQL driver enabled. In PowerShell from the repository root, run:

   ```powershell
   php -m | Select-String 'PDO|pdo_mysql'
   php backend/tests/dao_crud_test.php
   ```

The CRUD demonstration inserts a temporary user and related rows, checks create/read/update/delete and missing-record behavior for every DAO, then rolls back its transaction. Run it against a disposable `spendwise` database: sample database data is not altered, but test runs need permission to insert rows and start transactions. The script prints `PASS` only after the database operations have executed successfully. PHP syntax checks can be run with `php -l` on each PHP file, but they do not prove that MySQL operations work.

### Submission

Commit milestone changes on the `milestone-2` branch and push that branch, then open a pull request from `milestone-2` into the intended base branch (`main` if that is the course submission target). Review the base branch and diff on GitHub before creating the PR; no merge is part of this work.
