<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Listify API

This is a simple API to manage tasks. It was developed using Laravel 11 and PostgreSQL.

## Installation On Local Machine (For Development)

1. Clone the repository
2. Run `composer install`
3. Create a new database
4. Copy the `.env.example` file to `.env` and set the database connection
5. Run `php artisan key:generate`
6. Run `php artisan migrate`
7. Run `php artisan serve`

## Deployment On Docker

1. Clone the repository
2. Run `docker-compose up -d --build`
3. Run `docker-compose exec app php artisan key:generate`
4. Run `docker-compose exec app php artisan migrate`

## API Endpoints

Postman collection: [Listify API](https://documenter.getpostman.com/view/28454077/2sAYBRFtpp)
