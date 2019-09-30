# api.chargingbreak.com

## Stack

* **Backend:** Laravel 6
* **Infrastructure:** Hosted on AWS Lambda via Laravel Vapor
* **Database:** MySQL 8

## Development

### Install dependencies

```
composer install
```


### Configure environment variables
```
cp .env.example .env
```

### Database

```
docker run -d -p 3306:3306 --name chargingbreak-db -e MYSQL_ROOT_PASSWORD=local -e MYSQL_DATABASE=chargingbreak -d mysql:8 --default-authentication-plugin=mysql_native_password
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
