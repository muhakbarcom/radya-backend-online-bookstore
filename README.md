# Online Bookstore Backend RESTful API

## Tech Stack

1. **PHP Framework:** Laravel 11
2. **Unit Testing:** PHPUnit
3. **Documentation:** Swagger
4. **Logging UI:** Telescope

## Requirements

1. **PHP:** 8.2+
2. **Composer**

## Getting Started

1. Open Terminal/Bash.
2. Clone the repository:
    ```bash
    git clone https://github.com/muhakbarcom/radya-backend-online-bookstore.git
    ```
3. Create the configuration environment (URL, Database, etc.) or copy from the example:
    ```bash
    cp .env.example .env
    ```
4. Create SQlite Database
    ```bash
    touch database/database.sqlite
    ```
5. Install Composer dependencies:
    ```bash
    composer install
    ```
6. Run migrations and seeders:
    ```bash
    php artisan migrate:refresh --seed
    ```
7. Generate New Key
    ```bash
    php artisan key:generate
    ```
8. Run the application:
    ```bash
    php artisan serve
    ```
9. Open the Swagger documentation at [http://127.0.0.1:8000](http://127.0.0.1:8000) (Default) or Logging Dashboard at [http://127.0.0.1:8000/telescope](http://127.0.0.1:8000/telescope)

## Demo Accounts

-   **Admin:**
    -   Email: `admin@gmail.com`
    -   Password: `password`
-   **Customer:**
    -   Email: `customer_1@gmail.com`
    -   Password: `password`

## Running PHPUnit Tests

To run unit tests, follow these steps:

1. Open Terminal/Bash.
2. Run the following command:
    ```bash
    php artisan test
    ```
