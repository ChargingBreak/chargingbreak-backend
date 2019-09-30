# api.chargingbreak.com

## Stack

* **Backend:** Laravel 6
* **Infrastructure:** Hosted on AWS Lambda via Laravel Vapor
* **Database:** MySQL 8 on RDS (Using SQLite locally for development)

## Development

### Install dependencies
```
composer install
php artisan migrate
```

### Development server
```
php artisan serve
```

## Deployment

```
vapor deploy production
```
