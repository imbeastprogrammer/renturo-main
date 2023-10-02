# How to Run a Laravel Project

This README file provides instructions on how to set up and run the project on your local machine.

## Tech Stack

**Client:** HTML, CSS, JavaScript, React JS

**Server:** PHP, Laravel

## Prerequisites

Before you begin, ensure that you have the following installed on your system:

1. **PHP:** Laravel 9.x requires PHP to run. You'll need PHP version 8.0 or higher. Server Requirements (https://laravel.com/docs/9.x/deployment#server-requirements):

2. **Composer:** Composer is a PHP package manager that Laravel uses for dependency management.

3. **Database:** For storing your application's data, a database server like MySQL, PostgreSQL, or SQLite is necessary. In this project, MySQL is the chosen database system.

4. **Web Server:** You can use Apache or Nginx as your web server to serve the Laravel application.

## Configuration

1. **Environment File:** Duplicate the **.env.example** file in your project's root directory and rename it to **.env**. Update the necessary configuration options like database connection settings.

2. **Generate Application Key** In your terminal, run the following command to generate an application key:

```bash
cd /to/your-laravel-project

php artisan key:generate
```

3. **Database Setup:** Create a database for your project and update the **.env** file with the appropriate database credentials.

## Installing Dependencies

1. In your terminal, navigate to the project directory:

```bash
cd your-laravel-project
```

2. Run the following command to install project dependencies using Composer:

```bash
composer install
```

## Running table migrations

1. To create the necessary tables in the database with seeder, run the migrations using the following command:

```bash
php artisan migrate --seed
```

## Runnning the application

1. In your terminal, navigate to the project directory if you're not already there.

2. Use the following command to start the local development server:

```bash
php artisan serve
```

## For troubleshooting

**Run the Command:** Execute the following command to run the optimization:

```bash
php artisan optimize
```

This command performs several optimization tasks, including:
- Generating a "compiled" class file for your Blade views for faster rendering.
- Caching the configuration files to reduce disk I/O.
- Caching the routes to improve route registration speed.
- Clearing the compiled class file if it exists to force recompilation.