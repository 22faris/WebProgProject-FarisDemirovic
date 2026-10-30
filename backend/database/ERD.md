# SpendWise database relationships

This schema keeps the four core entities shown in the original `database-diagram.png` and adds `user_settings` for the preferred currency already shown on the profile screen.

```mermaid
erDiagram
    USERS ||--|| USER_SETTINGS : has
    USERS ||--o{ CATEGORIES : owns
    USERS ||--o{ TRANSACTIONS : records
    CATEGORIES ||--o{ TRANSACTIONS : classifies
    USERS ||--o{ BUDGETS : sets
    CATEGORIES ||--o{ BUDGETS : limits

    USERS {
        bigint id PK
        varchar full_name
        varchar email UK
        varchar password_hash
        timestamp created_at
    }
    USER_SETTINGS {
        bigint user_id PK,FK
        char preferred_currency
        timestamp updated_at
    }
    CATEGORIES {
        bigint id PK
        bigint user_id FK
        varchar name
        enum type
    }
    TRANSACTIONS {
        bigint id PK
        bigint user_id FK
        bigint category_id FK
        decimal amount
        enum type
        varchar description
        date transaction_date
        timestamp created_at
    }
    BUDGETS {
        bigint id PK
        bigint user_id FK
        bigint category_id FK
        decimal amount_limit
        date month
        timestamp created_at
    }
```

Transactions and budgets include both `user_id` and `category_id`; composite foreign keys require that each selected category belongs to the same user as the row. Categories are unique by `(user_id, name, type)`, and each user can have only one budget per category and month.
