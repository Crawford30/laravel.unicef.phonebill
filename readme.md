## UNICEF PLATFORM TEMPLATE (SKELETON)

Incase of creating a new module this repo helps your with the initial configuration.

#### Used Libraries.
- [passport](https://github.com/laravel/passport) for api authentication.
- [cors](https://github.com/fruitcake/laravel-cors) for handling cors.

#### Env variables to take note of.

Key | Description
------|------------
APP_KEY| This should be the same as the Main platform key since its used for encryption
AUTH_DATABASE| Main Platform DB Name
AUTH_DB_USERNAME| Main Platform DB user
AUTH_DB_PASSWORD| Main Platform DB Password
PLATFORM_URL| Main platform URL

#### Installation
- Run composer install.
- Set up the database.
- Run `php artisan migrate`.
- Run `php artisan passport:install`
- **NOTE** Every time you run `php artisan migrate:refresh` or truncate the database, remember to reinstall passport tokens.

# laravel.unicef.phonebill
