# Courier Service API

A small Laravel REST API for managing **courier master data** (create, read, update, delete). Built as a backend coding test.

There is no UI — the project exposes a JSON API only.

## Features

- Courier master data with **levels 1–5** and vehicle information.
- Paginated listing, ordered by **courier name** by default.
- Sort override by **registration date** (or `level` / `created_at`).
- **Multi-word name search** — `?search=budi+agung` matches `Budiono Hadi Agung`.
- Filter by one or more **levels** — `?level=2,3`.
- Complete input **validation** on create and update.
- **Feature and unit tests** covering every endpoint and validation rule.

## Tech stack

| | |
| --- | --- |
| Language | PHP 8.3+ |
| Framework | Laravel 13 |
| Database | SQLite (default, zero configuration) |
| Tests | PHPUnit 12 |
| Formatter | Laravel Pint |
| API docs | [darkaonline/l5-swagger](https://github.com/DarkaOnLine/L5-Swagger) (OpenAPI 3.0) |

> The default connection is SQLite so the project runs with no external services. See [Switch to MySQL](#switch-to-mysql) if you prefer.

## Getting started

```bash
git clone <repository-url>
cd simple-backend-laravel

composer install
cp .env.example .env
php artisan key:generate

touch database/database.sqlite   # only if the file does not exist yet
php artisan migrate --seed       # creates the couriers table and sample data
php artisan serve
```

The API is then available at `http://127.0.0.1:8000/api`.

### Switch to MySQL

Set the connection in `.env` and migrate again:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=simple_backend
DB_USERNAME=root
DB_PASSWORD=
```

```bash
php artisan migrate --seed
```

## Database schema: `couriers`

| Column | Type | Notes |
| --- | --- | --- |
| `id` | bigint, PK | |
| `name` | string | Courier full name. Indexed (default sort + search). |
| `email` | string, unique | Contact email. |
| `phone` | string(20), unique | Contact phone number. |
| `level` | unsigned tinyint | Courier level **1–5**. Indexed. Backed by the `CourierLevel` enum. |
| `vehicle_type` | string(20) | `motorcycle`, `car`, `van`, `truck`, `bicycle`. Backed by the `VehicleType` enum. |
| `vehicle_plate` | string(20), nullable, unique | License plate. |
| `address` | text, nullable | |
| `city` | string(100), nullable | |
| `is_active` | boolean | Defaults to `true`. |
| `registered_at` | timestamp | The date the courier was registered. Indexed. |
| `created_at`, `updated_at` | timestamps | |

Indexes are added on `name`, `level`, and `registered_at` because they are the columns the listing endpoint sorts and filters on.

## API reference

| Method | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/api/couriers` | Paginated list of couriers. |
| `POST` | `/api/couriers` | Create a courier. |
| `GET` | `/api/couriers/{courier}` | Return every attribute of one courier. |
| `PUT` / `PATCH` | `/api/couriers/{courier}` | Update a courier. |
| `DELETE` | `/api/couriers/{courier}` | Delete a courier. |

A missing courier returns `404`. Invalid input returns `422` with a `errors` object keyed by field name.

### Query parameters — `GET /api/couriers`

| Parameter | Example | Behaviour |
| --- | --- | --- |
| `search` | `?search=budi+agung` | Matches couriers whose name contains **every** word of the term (each word is an AND constraint), so `budi agung` matches `Budiono Hadi Agung`. |
| `level` | `?level=2,3` | Returns only couriers whose level is one of the listed values (1–5). |
| `sort` | `?sort=registered_at` | Sort column: `name` (**default**), `registered_at`, `level`, or `created_at`. |
| `direction` | `?direction=desc` | `asc` (**default**) or `desc`. |
| `per_page` | `?per_page=25` | Items per page, 1–100 (default `15`). |

The response is Laravel's standard paginator envelope:

```json
{
  "data": [ { "id": 1, "name": "Budiono Hadi Agung", "level": 2, "...": "..." } ],
  "links": { "first": "...", "last": "...", "prev": null, "next": "..." },
  "meta": { "current_page": 1, "per_page": 15, "total": 21, "last_page": 2, "...": "..." }
}
```

### Request body — `POST` / `PUT` / `PATCH`

| Field | Rules |
| --- | --- |
| `name` | required, string, max 255 |
| `email` | required, valid email, max 255, unique per courier |
| `phone` | required, string, max 20, unique per courier |
| `level` | required, integer, one of 1–5 |
| `vehicle_type` | required, one of `motorcycle`, `car`, `van`, `truck`, `bicycle` |
| `vehicle_plate` | nullable, string, max 20, unique per courier |
| `address` | nullable, string, max 1000 |
| `city` | nullable, string, max 100 |
| `is_active` | optional, boolean (defaults to `true`) |
| `registered_at` | required, valid date |

On `PATCH` every field is optional; only the fields present in the request are validated and updated. The `unique` rules ignore the courier being updated.

### Examples

```bash
BASE=http://127.0.0.1:8000/api

# List — default order (name), page 1
curl "$BASE/couriers"

# Search a multi-word name
curl "$BASE/couriers?search=budi+agung"

# Only levels 2 and 3, newest registered first
curl "$BASE/couriers?level=2,3&sort=registered_at&direction=desc"

# Show one courier
curl "$BASE/couriers/1"

# Create
curl -X POST "$BASE/couriers" \
  -H "Content-Type: application/json" -H "Accept: application/json" \
  -d '{
        "name": "Rina Kartika",
        "email": "rina@example.com",
        "phone": "081200000099",
        "level": 4,
        "vehicle_type": "van",
        "vehicle_plate": "D 2002 BBB",
        "city": "Bandung",
        "registered_at": "2025-05-01 09:30:00"
      }'

# Update
curl -X PATCH "$BASE/couriers/1" \
  -H "Content-Type: application/json" -H "Accept: application/json" \
  -d '{"city": "Surabaya"}'

# Delete
curl -X DELETE "$BASE/couriers/1" -H "Accept: application/json"
```

## API documentation (Swagger)

Interactive OpenAPI 3.0 documentation is generated with [l5-swagger](https://github.com/DarkaOnLine/L5-Swagger):

| | |
| --- | --- |
| Swagger UI | `http://127.0.0.1:8000/api/documentation` |
| OpenAPI JSON | `http://127.0.0.1:8000/docs` |

The specification is built from PHP attributes: the API metadata and reusable schemas live in `app/OpenApi/`, and each endpoint is annotated on `Api\CourierController`. Regenerate the documentation after changing an annotation:

```bash
php artisan l5-swagger:generate
```

With `L5_SWAGGER_GENERATE_ALWAYS=true` (the default in `.env.example`) the documentation is regenerated automatically on every request, so there is no manual step during development. The configuration lives in `config/l5-swagger.php`.

## Testing

```bash
php artisan test
```

The suite contains feature tests (one class per endpoint) and unit tests for the enums. It uses an in-memory SQLite database, so no setup is required.

```
tests/Feature/Courier/IndexCourierTest.php    # pagination, sorting, search, level filter, query validation
tests/Feature/Courier/StoreCourierTest.php    # validation rules, persistence, unique constraints
tests/Feature/Courier/ShowCourierTest.php     # single courier payload and 404
tests/Feature/Courier/UpdateCourierTest.php   # full/partial update, unique ignore, 404
tests/Feature/Courier/DestroyCourierTest.php  # deletion and 404
tests/Feature/OpenApiTest.php                 # Swagger UI and generated specification
tests/Unit/Enums/                             # enum values
```

## Design notes

- **Form Requests** (`app/Http/Requests/Courier`) hold all validation, so controllers only coordinate.
- **API Resource** (`CourierResource`) defines the JSON contract of a courier.
- **Enums** (`CourierLevel`, `VehicleType`) are the single source of truth for level and vehicle values; the model casts the columns to them.
- **Local scopes** (`search`, `ofLevel`) keep the reusable query logic on the model and out of the controller.
- **Sort columns and direction are whitelisted** before they reach `orderBy`, preventing SQL injection through the query string. Every order is given an `id` tie-breaker so pagination is stable.
- **Factory + seeder** are included; `CourierSeeder` seeds a predictable `Budiono Hadi Agung` record so the search example is demonstrable after `migrate --seed`.
- **OpenAPI annotations** live in `app/OpenApi` (info, server, schemas) and on the controller actions; the spec is rendered by `l5-swagger`.

## Assumptions

- The API is public; no authentication or authorization is required by the brief.
- `registered_at` is the "date registered" and is supplied by the client.
- Deleting a courier performs a hard delete.
