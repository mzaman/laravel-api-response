
# Laravel API Response

A flexible and scalable API response handling package for Laravel, designed to provide a standardized approach for handling success, error, validation, and custom responses. It supports **localization**, **customization** of messages, **pagination**, and **metadata** for API responses.

---

## Installation

To install the package, use **Composer**:

```bash
composer require mzaman/laravel-api-response
```

---

## Publishing Configuration

After installing the package, you can publish the configuration file for easy customization:

```bash
php artisan vendor:publish --provider="MasudZaman\LaravelApiResponse\Providers\LaravelApiResponseServiceProvider" --tag=config
```

This will publish the `api-response.php` configuration file to the `config/` directory in your Laravel application.

---

## Configuration

After publishing, the `config/api-response.php` file will contain the following default configuration:

```php
<?php

return [
    'default_locale' => 'en',
    'messages' => [
        'success' => 'Request was successful',
        'created_success' => 'Resource created successfully',
        'bad_request' => 'Invalid request',
        'unauthorized' => 'Authentication required',
        'forbidden' => 'Permission denied',
        'not_found' => 'Resource not found',
        'internal_server_error' => 'An unexpected error occurred',
        'service_unavailable' => 'Service temporarily unavailable',
        'validation_failed' => 'Validation failed',
        'conflict' => 'Conflict detected',
        'rate_limit_exceeded' => 'Rate limit exceeded',
    ],
];
```

You can modify the **messages**, **default locale**, and any other settings in this file to suit your needs.

---

## Usage

Once the package is installed and configured, you can use the provided methods to send standardized API responses.

### 1. Success Response

You can use the **`ApiResponse` facade** or **helper functions** to return success responses. Here’s how you can use them:

#### Using the Facade:

```php
use MasudZaman\LaravelApiResponse\Facades\ApiResponse;

class UserController extends Controller
{
    public function getUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return ApiResponse::error(404, 'User not found');
        }

        return ApiResponse::success($user, 200, 'User fetched successfully');
    }
}
```

#### Using Helper Functions:

You can also use the `apiResponse()` helper function for a more concise approach:

```php
class UserController extends Controller
{
    public function getUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return errorResponse('User not found', 404);
        }

        return apiResponse($user, 'User fetched successfully');
    }
}
```

### 2. Error Response

For error handling, you can return error responses with the `error()` method. You can also pass custom error messages and validation errors.

#### Example:

```php
public function createUser(Request $request)
{
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
    ]);

    // Handle unexpected errors
    if (!$validatedData) {
        return errorResponse(422, 'Validation failed', $errors = [
            'name' => 'Name is required',
            'email' => 'Email is invalid'
        ]);
    }

    $user = User::create($validatedData);

    return successResponse($user, 'User created successfully', 201);
}
```

---

### 3. Customizing Messages

You can customize the messages for different HTTP status codes in the `config/api-response.php` file. For instance:

```php
'messages' => [
    'success' => 'Your request was successfully processed',
    'created_success' => 'The resource has been created successfully',
    'validation_failed' => 'The data validation failed',
    'unauthorized' => 'Authentication is required to access this resource',
    // Other messages...
]
```

---

### 4. Localization

Laravel API Response supports localization for error messages and other strings.

To localize the messages, create language files in the `lang/{locale}/messages.php` directory, and Laravel will automatically load them based on the current locale.

Example:

```php
// lang/en/messages.php
return [
    'success' => 'Request was successful',
    'created_success' => 'Resource created successfully',
    'bad_request' => 'Invalid request',
    // Other messages...
];

// lang/es/messages.php (Spanish localization)
return [
    'success' => 'La solicitud fue exitosa',
    'created_success' => 'Recurso creado exitosamente',
    'bad_request' => 'Solicitud inválida',
    // Other messages...
];
```

You can switch the language based on the user's preference or application settings by setting the locale in your controller or middleware:

```php
app()->setLocale('es');
```

---

### 5. Pagination Support

The package allows you to handle paginated data using the `meta` field to include pagination metadata. Here’s how you can return paginated data:

```php
use Illuminate\Pagination\LengthAwarePaginator;

class UserController extends Controller
{
    public function listUsers(Request $request)
    {
        $users = User::paginate(10);

        return ApiResponse::success($users, 'Users fetched successfully', 200, [
            'pagination' => [
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
            ]
        ]);
    }
}
```

This will automatically add pagination metadata to your response.

---

### 6. Testing

You can test your API responses using **PHPUnit** or **Pest** (if you are using it).

Here’s an example test case for testing the success and error responses:

```php
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    public function test_get_user()
    {
        $response = $this->get('/api/user/1');
        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'User fetched successfully'
                 ]);
    }

    public function test_create_user()
    {
        $response = $this->postJson('/api/user', [
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'User created successfully'
                 ]);
    }

    public function test_validation_error()
    {
        $response = $this->postJson('/api/user', [
            'name' => '',
            'email' => 'invalid-email'
        ]);

        $response->assertStatus(422)
                 ->assertJson([
                     'status' => 'error',
                     'message' => 'Validation failed',
                     'errors' => [
                         'name' => ['The name field is required.'],
                         'email' => ['The email must be a valid email address.']
                     ]
                 ]);
    }
}
```

---

### 7. Facade and Helper Functions

For easier access, you can use the **`ApiResponse` facade** or helper functions (`apiResponse()` and `apiError()`).

```php
use MasudZaman\LaravelApiResponse\Facades\ApiResponse;

public function getUser($id)
{
    $user = User::find($id);

    if (!$user) {
        return ApiResponse::error(404, 'User not found');
    }

    return ApiResponse::success($user, 200, 'User fetched successfully');
}
```

Alternatively, use the helper function:

```php
return apiResponse($user, 'User fetched successfully');
```

---

### 8. Helper Methods

Below are the available helper functions for sending standardized responses based on different status codes:

- `apiResponse()` - Sends a success response.
- `errorResponse()` - Sends an error response.
- `successResponse()` - Sends a basic success response (200).
- `createdResponse()` - Sends a resource created response (201).
- `acceptedResponse()` - Sends an accepted response (202).
- `noContentResponse()` - Sends a no content response (204).
- `unavailableResponse()` - Sends a service unavailable response (503).
- `maintenanceResponse()` - Sends a maintenance mode response (503).
- `failResponse()` - Sends a bad request response (400).
- `unauthorizedResponse()` - Sends an unauthorized response (401).
- `forbiddenResponse()` - Sends a forbidden response (403).
- `notFoundResponse()` - Sends a not found response (404).
- `validationErrorResponse()` - Sends a validation error response (422).
- `manyRequestsResponse()` - Sends a too many requests response (429).
- `updatedResponse()` - Sends an updated response (200).
- `deletedResponse()` - Sends a deleted response (200).

---

### License

This package is open-sourced software licensed under the **MIT** license.
