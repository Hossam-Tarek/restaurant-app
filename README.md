# Restaurant app

Restaurant app is a restaurant app for managing orders.

## Requirements

* Laravel v10.x
* PHP v8.1

## Installation

1. Clone the repository:
   ```shell
   git clone git@github.com:Hossam-Tarek/restaurant-app.git
   ```

2. Navigate to the project directory:
   ```shell
   cd restaurant-app
   ```

3. Install dependencies using Composer:
   ```shell
   composer install
   ```

4. Create a `.env` file by copying `.env.example`:
   ```shell
   cp .env.example .env
   ```

5. Generate a new Laravel application key:
   ```shell
   php artisan key:generate
   ```

6. Create a fresh database and update the DB name in `.env` file

7. Migrate the DB and seed the data
   ```shell
   php artisan migrate:fresh --seed
   ```

8. Start the Laravel development server:
   ```shell
   php artisan serve
   ```

