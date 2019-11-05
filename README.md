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

## Install Passport 

````html
php artisan passport:install
````
 
## Serve the app
````html
php artisan serve
````


## API Details

For Registration (POST API)

Input field will be name, email, password and c_password 

````html
http://127.0.0.1:8000/api/register
````

For Login (POST API)

Field will be email and password.

````html
http://127.0.0.1:8000/api/login
````

## For Upading Profile (POST API) - Account will be active after updating profile infos.

Field will be img, national_id, contact_no, country_id, city_id, present_address and permenent_address.

````html
http://127.0.0.1:8000/api/update-profile
````

## We have to use Bearer Token to get after login.

For depositing Amount (POST API)

Field will be from_account_no and deposit.

````html
http://127.0.0.1:8000/api/deposit 
````

For Transfering Balance (POST API)

Field will be from_account_no and to_account_no and withdraw.

````html
http://127.0.0.1:8000/api/transfer
````


For Checking Balance (POST API)

Field will be account_no.

````html
http://127.0.0.1:8000/api/get-balance
````






