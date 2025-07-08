# Laravel API Response

This package provides a flexible and scalable structure for handling API responses in Laravel. It includes support for custom API status codes, locale management, error handling, and response formatting.

## Installation

You can install the package via Composer:

```bash
composer require mzaman/laravel-api-response
```

## Usage

You can use helper functions to generate API responses:

### Success Response

```php
return api_response(['data' => 'Some data'], 1000, 200, 'Data fetched successfully');
```

### Error Response

```php
return api_error('An error occurred', 1006, 500, ['error' => 'Details of error']);
```

## Exception Handling

The package supports custom exceptions. You can throw an `ApiException` and it will automatically be caught and formatted.

## Locale Support

You can set a locale for each response:

```php
return api_response(['data' => 'Some data'], 1000, 200, 'Success', null, 'en');
```

### License

This package is open-sourced software licensed under the MIT license.
