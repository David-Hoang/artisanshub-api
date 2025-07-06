
# ArtisansHub - API BackEnd (Laravel)

A plateform to connect client and craftsman in their regions. The main feature is the listing of craftsman available and the handle of prestations.

## About the project

API RESTful developped with Laravel 12 to deserve ArtisansHub platform ArtisansHub frontend (React)

This backend handles : 
- Registration and authentication for both client and craftsman
- Management of prestations
- Messages system
- Listings of craftsmans
- Admin features : handle users, prestations, add job category

## Getting Started

### Dependencies

- PHP >= 8.2
- Composer
- Laravel >= 12.0 
- MySQL

### Installing

1. Clone the repository
```
git clone https://github.com/David-Hoang/artisanshub-api.git
cd artisanshub-api
```

3. Install dependencies
```
composer install
npm install
```
3. Copy `.env.example` and rename to `.env`. 
Configure your database connection in the `.env` file

5. Generate artisan key
`php artisan key:generate`

6. Execute migration and database seeders
`php artisan migrate:fresh --seed`

### Executing program (local development)
After setting up, launch the development server :
`php artisan serve`

The first endpoint you can check is :
`/api/craftsmen`

## Authors

Contributors names and contact info

David Hoang
[LinkedIn](https://www.linkedin.com/in/dav-hoang/)
