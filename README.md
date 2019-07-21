# API Instructions

## Setup
> composer install

> php artisan passport:install

## How to create and seed database tables

> php artisan migrate:fresh --seed

## How authentication works

I am using Laravel Passport for the authentication.
> https://laravel.com/docs/5.8/passport

1. Grab an auth token by sending a POST request to /api/auth/token with the following:
* email
* password

2. Add the returned token to the Authorization header when making an API request.
> Authorization: Bearer {token}

## API Endpoints

### Products

Index - GET request to /api/products

Show - GET request to /api/products/{productId}

Store - POST request to /api/products with the following:
* name (string)
* description (text)
* price (decimal)

Update - PATCH request to /api/products/{productId} with any of the following:
* name (string)
* description (text)
* price (decimal)

Destroy - DELETE request to /api/products/{productId}

### Add Image to Product

Store - POST request to /api/products/{productId}/image with the following:
* image (file)

### User Products

List - GET request to /api/user-products

Attach - POST request to /api/user-products with the following:
* product_id

Detach - DELETE request to /api/user-products/{productId}
