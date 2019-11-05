# MiniBank Online Management System(MBS)

## Clone the app 

````html
 git clone https://github.com/abdulhalimcse/minibankingsystem-laravel-api.git
````

````html
cd minibankingsystem-laravel-api/

````

## Update the composer

````html
cp .env.example .env
````

````html
composer update
````

````html
php artisan key:generate
````

## Change DB info in .env file 

````html
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=minibankingsystem
DB_USERNAME=root
DB_PASSWORD=

````
## Migrate DB 

````html
php artisan migrate
````
 

