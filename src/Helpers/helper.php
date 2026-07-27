<?php

use Illuminate\Http\Response;

if (!function_exists('apiResponse')) {
    /**
     * Gold Standard API response helper - Type-Priority Detection with Zero-Overhead.
     *
     * **Type-Priority Detection (Protected Zone):**
     * 1. Objects/arrays → ALWAYS data (200 OK) - prevents DTO ID collisions
     * 2. Valid HTTP codes (100-599) → Status code
     * 3. Everything else → Data (200 OK) - null, bool, invalid ints, etc.
     *
     * **Collision Zone Protection:**
     * Even if a Resource collection has ID 404, this helper will NEVER send a
     * "Not Found" error because object/array type-hints take precedence.
     *
     * **Performance:**
     * - Zero transformation overhead
     * - Minimal type checking (2 boolean checks)
     * - Optimized for 10,000+ item syncs
     * - CPU focuses on Serde serialization, not helper logic
     *
     * **Usage Examples:**
     * ```php
     * // Protected Zone - Objects/Arrays always data
     * return apiResponse($user);                    // Object → 200 OK
     * return apiResponse(['id' => 404]);            // Array → 200 OK (NOT 404!)
     * return apiResponse($resourceCollection);      // Even if id=404
     *
     * // Valid HTTP codes
     * return apiResponse(201, $resource);           // 201 Created
     * return apiResponse(204);                      // 204 No Content
     *
     * // Edge cases - Everything else is data
     * return apiResponse(999);                      // Invalid code → data
     * return apiResponse(null);                     // null → data
     * return apiResponse(true);                     // bool → data
     * ```
     *
     * @param  mixed            $codeOrData  HTTP status code (100-599) OR response data
     * @param  mixed            $data        Response data (when code is provided)
     * @param  string|null      $message     Optional custom message
     * @param  array            $meta        Optional metadata
     * @param  \\Throwable|null  $exception   Optional exception for error details
     * @return \\Illuminate\\Http\\JsonResponse
     */
    function apiResponse(
        mixed $codeOrData = 200,
        mixed $data = null,
        ?string $message = null,
        array $meta = [],
        ?Throwable $exception = null
    ): \Illuminate\Http\JsonResponse {

        // Type-Priority Detection
        $isCode = is_int($codeOrData) && $codeOrData >= 100 && $codeOrData < 600;
        $isData = is_object($codeOrData) || is_array($codeOrData);

        if ($isData || !$isCode) {
            // Protected Zone: Treat $codeOrData as $data, shift parameters
            // Captures: DTOs, Arrays, null, bool, invalid ints (999, -1, etc.)
            return app('api-response')->respond(
                $codeOrData,                                    // data
                is_string($data) ? $data : null,                // message
                is_array($message) ? $message : [],             // meta
                200,                                            // code (default)
                $meta instanceof \Throwable ? $meta : $exception // exception
            );
        }

        // Standard Code-First Pattern (100-599)
        return app('api-response')->respond($data, $message, $meta, $codeOrData, $exception);
    }
}

if (!function_exists('apiResult')) {
    /**
     * Legacy API response helper - delegates to respond() for all status codes.
     *
     * @deprecated Use apiResponse() instead (code-first parameter order)
     *
     * This is the legacy helper with data-first parameter order.
     * For new code, use apiResponse($code, $data, ...) instead.
     *
     * **Usage Examples:**
     * ```php
     * // Success response
     * return apiResult($data, 'Success', [], 200);
     *
     * // Error response
     * return apiResult(null, 'Not found', [], 404);
     *
     * // With metadata
     * return apiResult($users, null, ['total' => 100], 200);
     * ```
     *
     * @param  mixed            $data      Response data
     * @param  string|null      $message   Optional custom message
     * @param  array            $meta      Optional metadata
     * @param  int              $code      HTTP status code (default: 200)
     * @param  \Throwable|null  $exception Optional exception for debugging
     * @return \Illuminate\Http\JsonResponse
     */
    function apiResult(
        mixed $data = null,
        ?string $message = null,
        array $meta = [],
        int $code = Response::HTTP_OK,
        ?Throwable $exception = null
    ) {
        return app('api-response')->respond($data, $message, $meta, $code, $exception);
    }
}

if (!function_exists('errorResponse')) {
    /**
     * Send a generic error response.
     *
     * Flexible error helper that accepts any HTTP error status code.
     *
     * **Usage Examples:**
     * ```php
     * // Custom error code
     * return apiError(418, 'I\'m a teapot');
     *
     * // Standard error
     * return apiError(500, 'Internal server error');
     * ```
     *
     * @param  int          $code    HTTP error status code
     * @param  string|null  $message Optional custom message
     * @param  array        $errors  Optional error details
     * @param  array        $headers Optional HTTP headers
     * @return \Illuminate\Http\JsonResponse
     */
    function apiError($code = Response::HTTP_INTERNAL_SERVER_ERROR, $message = null, $errors = [], $headers = [])
    {
        return app('api-response')->error($code, $message, $errors, $headers);
    }
}

if (!function_exists('errorResponse')) {
    /**
     * Send an internal server error response (500 Internal Server Error).
     *
     * Use this for unexpected server errors. Should be used sparingly as it
     * indicates a server-side problem that needs to be fixed.
     *
     * **Usage Examples:**
     * ```php
     * // Generic server error
     * return serverErrorResponse('An unexpected error occurred');
     *
     * // Database error
     * return serverErrorResponse('Database connection failed');
     *
     * // With error details (non-production)
     * return serverErrorResponse('Internal error', ['trace' => $exception->getTraceAsString()]);
     * ```
     *
     * @param  string|null  $message Optional custom message
     * @param  array|null   $errors  Optional error details
     * @param  array|null   $headers Optional HTTP headers
     * @return \Illuminate\Http\JsonResponse
     */
    function errorResponse($code = Response::HTTP_INTERNAL_SERVER_ERROR, $message = null, $errors = null, $headers = [])
    {
        return app('api-response')->error($code, $message, $errors, $headers);
    }
}

if (!function_exists('exceptionResponse')) {
    /**
     * Helper function to build a complete error response from a Throwable.
     *
     * Automatically includes debug data (exception class, file, line, trace)
     * in non-production environments.  This is the **recommended** single-point
     * helper for converting any exception into a JSON response.
     *
     * @param  \Throwable  $exception
     * @param  int         $code       HTTP status code (default derives from exception if possible)
     * @param  string|null $message    Override message (null = use exception message)
     * @param  array       $errors     Structured error details
     * @param  array       $headers    Extra HTTP headers
     * @return \Illuminate\Http\JsonResponse
     */
    function exceptionResponse(
        \Throwable $exception,
        int $code = Response::HTTP_INTERNAL_SERVER_ERROR,
        ?string $message = null,
        array $errors = [],
        array $headers = []
    ) {
        return app('api-response')->exceptionResponse($exception, $code, $message, $errors, $headers);
    }
}

if (!function_exists('successResponse')) {
    /**
     * Send a successful response (200 OK).
     *
     * Use this for standard successful GET, PUT, PATCH, or DELETE operations
     * that return data.
     *
     * **Usage Examples:**
     * ```php
     * // Basic success
     * return successResponse($user);
     *
     * // With custom message
     * return successResponse($user, 'User retrieved successfully');
     *
     * // With metadata (e.g., pagination)
     * return successResponse($users, null, ['total' => 100, 'page' => 1]);
     * ```
     *
     * @param  mixed        $data    Response data
     * @param  string|null  $message Optional custom message
     * @param  array|null   $meta    Optional metadata (pagination, timestamps, etc.)
     * @param  array|null   $headers Optional HTTP headers
     * @return \Illuminate\Http\JsonResponse
     */
    function successResponse($data = [], $message = null, $meta = null, $headers = [])
    {
        return app('api-response')->success($data, Response::HTTP_OK, $message, $meta, $headers);
    }
}

if (!function_exists('createdResponse')) {
    /**
     * Send a resource created response (201 Created).
     *
     * Use this for successful POST operations that create a new resource.
     * Typically includes the created resource in the response body.
     *
     * **Usage Examples:**
     * ```php
     * // Basic created response
     * return createdResponse($newUser);
     *
     * // With custom message
     * return createdResponse($newUser, 'User created successfully');
     *
     * // With Location header
     * return createdResponse($newUser, null, null, ['Location' => '/users/123']);
     * ```
     *
     * @param  mixed        $data    The created resource data
     * @param  string|null  $message Optional custom message
     * @param  array|null   $meta    Optional metadata
     * @param  array|null   $headers Optional HTTP headers (e.g., Location)
     * @return \Illuminate\Http\JsonResponse
     */
    function createdResponse($data = [], $message = null, $meta = null, $headers = [])
    {
        return app('api-response')->created($data, $message, $meta, $headers);
    }
}

if (!function_exists('acceptedResponse')) {
    /**
     * Send an accepted response (202 Accepted).
     *
     * Use this for requests that have been accepted for processing but are not yet complete.
     * Common for async operations, batch processing, or queued jobs.
     *
     * **Usage Examples:**
     * ```php
     * // Async job accepted
     * return acceptedResponse(['job_id' => 'abc123']);
     *
     * // With custom message
     * return acceptedResponse(['job_id' => 'abc123'], 'Processing started');
     *
     * // With status URL in metadata
     * return acceptedResponse(['job_id' => 'abc123'], null, ['status_url' => '/jobs/abc123']);
     * ```
     *
     * @param  mixed        $data    Response data (typically job/task info)
     * @param  string|null  $message Optional custom message
     * @param  array|null   $meta    Optional metadata (status URL, estimated time, etc.)
     * @param  array|null   $headers Optional HTTP headers
     * @return \Illuminate\Http\JsonResponse
     */
    function acceptedResponse($data = [], $message = null, $meta = null, $headers = [])
    {
        return app('api-response')->accepted($data, $message, $meta, $headers);
    }
}

if (!function_exists('noContentResponse')) {
    /**
     * Send a no content response (204 No Content).
     *
     * Use this for successful operations that don't return data, such as
     * DELETE operations or updates that don't need to return the updated resource.
     * Per RFC 9110, this response MUST NOT include a message body.
     *
     * **Usage Examples:**
     * ```php
     * // After successful deletion
     * return noContentResponse();
     *
     * // After successful update (no data to return)
     * return noContentResponse();
     *
     * // With custom headers
     * return noContentResponse(['X-Request-ID' => '123']);
     * ```
     *
     * @param  array|null  $headers Optional HTTP headers
     * @return \Illuminate\Http\JsonResponse
     */
    function noContentResponse($headers = [])
    {
        return app('api-response')->noContent($headers);
    }
}

if (!function_exists('unavailableResponse')) {
    /**
     * Send a service unavailable response (503 Service Unavailable).
     *
     * Use this when the server is temporarily unable to handle requests,
     * typically during maintenance or when overloaded.
     *
     * **Usage Examples:**
     * ```php
     * // Maintenance mode
     * return serviceUnavailableResponse('Service temporarily unavailable');
     *
     * // With Retry-After header
     * return serviceUnavailableResponse('Maintenance in progress', null, ['Retry-After' => '3600']);
     *
     * // Overloaded
     * return serviceUnavailableResponse('Server overloaded, please try again later');
     * ```
     *
     * @param  string|null  $message Optional custom message
     * @param  array|null   $errors  Optional error details
     * @param  array|null   $headers Optional HTTP headers (e.g., Retry-After)
     * @return \Illuminate\Http\JsonResponse
     */
    function unavailableResponse($message = null, $errors = null, $headers = [])
    {
        return app('api-response')->serviceUnavailable($message, $errors, $headers);
    }
}

if (!function_exists('maintenanceResponse')) {
    /**
     * Send a maintenance mode response (503 Service Unavailable).
     *
     * Use this when the application is in maintenance mode.
     * Typically includes a Retry-After header.
     *
     * **Usage Examples:**
     * ```php
     * // Basic maintenance
     * return maintenanceResponse('System maintenance in progress');
     *
     * // With retry time
     * return maintenanceResponse('Scheduled maintenance', null, ['Retry-After' => '7200']);
     *
     * // With estimated completion
     * return maintenanceResponse('Maintenance until 2:00 AM UTC');
     * ```
     *
     * @param  string|null  $message Optional custom message
     * @param  array|null   $errors  Optional error details
     * @param  array|null   $headers Optional HTTP headers (e.g., Retry-After)
     * @return \Illuminate\Http\JsonResponse
     */
    function maintenanceResponse($message = null, $errors = null, $headers = [])
    {
        return app('api-response')->serviceUnavailable($message, $errors, $headers);
    }
}

if (!function_exists('failResponse')) {
    /**
     * Send a bad request response (400 Bad Request).
     *
     * Alias for badRequestResponse(). Use this when the request fails basic validation.
     *
     * **Usage Examples:**
     * ```php
     * // Basic fail
     * return failResponse('Request validation failed');
     *
     * // With error details
     * return failResponse('Invalid input', ['field' => ['Required']]);
     * ```
     *
     * @param  string|null  $message Optional custom message
     * @param  array|null   $errors  Optional error details
     * @param  array|null   $headers Optional HTTP headers
     * @return \Illuminate\Http\JsonResponse
     */
    function failResponse($message = null, $errors = null, $headers = [])
    {
        return app('api-response')->badRequest($message, $errors, $headers);
    }
}

if (!function_exists('unauthorizedResponse')) {
    /**
     * Send an unauthorized response (401 Unauthorized).
     *
     * Use this when authentication is required but not provided or invalid.
     * Typically includes a WWW-Authenticate header.
     *
     * **Usage Examples:**
     * ```php
     * // Basic unauthorized
     * return unauthorizedResponse('Authentication required');
     *
     * // With WWW-Authenticate header
     * return unauthorizedResponse('Invalid token', null, ['WWW-Authenticate' => 'Bearer']);
     *
     * // With error details
     * return unauthorizedResponse('Token expired', ['token' => ['Expired']]);
     * ```
     *
     * @param  string|null  $message Optional custom message
     * @param  array|null   $errors  Optional error details
     * @param  array|null   $headers Optional HTTP headers (e.g., WWW-Authenticate)
     * @return \Illuminate\Http\JsonResponse
     */
    function unauthorizedResponse($message = null, $errors = null, $headers = [])
    {
        return app('api-response')->unauthorized($message, $errors, $headers);
    }
}

if (!function_exists('forbiddenResponse')) {
    /**
     * Send a forbidden response (403 Forbidden).
     *
     * Use this when the user is authenticated but doesn't have permission
     * to access the requested resource.
     *
     * **Usage Examples:**
     * ```php
     * // Basic forbidden
     * return forbiddenResponse('You do not have permission to access this resource');
     *
     * // With error details
     * return forbiddenResponse('Access denied', ['permission' => 'admin_required']);
     *
     * // Generic forbidden
     * return forbiddenResponse();
     * ```
     *
     * @param  string|null  $message Optional custom message
     * @param  array|null   $errors  Optional error details
     * @param  array|null   $headers Optional HTTP headers
     * @return \Illuminate\Http\JsonResponse
     */
    function forbiddenResponse($message = null, $errors = null, $headers = [])
    {
        return app('api-response')->forbidden($message, $errors, $headers);
    }
}

if (!function_exists('notFoundResponse')) {
    /**
     * Send a not found response (404 Not Found).
     *
     * Use this when a requested resource cannot be found.
     *
     * **Usage Examples:**
     * ```php
     * // Basic not found
     * return notFoundResponse('User not found');
     *
     * // With error details
     * return notFoundResponse('Resource not found', ['id' => ['Invalid ID']]);
     *
     * // Generic not found
     * return notFoundResponse();
     * ```
     *
     * @param  string|null  $message Optional custom message
     * @param  array|null   $errors  Optional error details
     * @param  array|null   $headers Optional HTTP headers
     * @return \Illuminate\Http\JsonResponse
     */
    function notFoundResponse($message = null, $errors = null, $headers = [])
    {
        return app('api-response')->notFound($message, $errors, $headers);
    }
}

if (!function_exists('validationErrorResponse')) {
    /**
     * Send a validation error response (422 Unprocessable Entity).
     *
     * Use this for form validation failures. The errors array should contain
     * field-specific validation messages.
     *
     * **Usage Examples:**
     * ```php
     * // Basic validation error
     * return validationErrorResponse('Validation failed', [
     *     'email' => ['The email field is required'],
     *     'password' => ['The password must be at least 8 characters']
     * ]);
     *
     * // From Laravel validator
     * return validationErrorResponse('Validation failed', $validator->errors()->toArray());
     *
     * // Generic validation error
     * return validationErrorResponse();
     * ```
     *
     * @param  string|null  $message Optional custom message
     * @param  array|null   $errors  Field-specific validation errors
     * @param  array|null   $headers Optional HTTP headers
     * @return \Illuminate\Http\JsonResponse
     */
    function validationErrorResponse($message = null, $errors = [], $headers = [])
    {
        return app('api-response')->validationError($message, $errors, $headers);
    }
}

if (!function_exists('manyRequestsResponse')) {
    /**
     * Send a too many requests response (429 Too Many Requests).
     *
     * Use this for rate limiting. Should include Retry-After or X-RateLimit headers.
     *
     * **Usage Examples:**
     * ```php
     * // Basic rate limit
     * return tooManyRequestsResponse('Rate limit exceeded', null, ['Retry-After' => '60']);
     *
     * // With rate limit info
     * return tooManyRequestsResponse('Too many requests', null, [
     *     'X-RateLimit-Limit' => '100',
     *     'X-RateLimit-Remaining' => '0',
     *     'X-RateLimit-Reset' => time() + 3600
     * ]);
     * ```
     *
     * @param  string|null  $message Optional custom message
     * @param  array|null   $errors  Optional error details
     * @param  array|null   $headers Optional HTTP headers (Retry-After, X-RateLimit-*)
     * @return \Illuminate\Http\JsonResponse
     */
    function manyRequestsResponse($message = null, $errors = [], $headers = [])
    {
        return app('api-response')->tooManyRequests($message, $errors, $headers);
    }
}

if (!function_exists('updatedResponse')) {
    /**
     * Send a resource updated response (200 OK).
     *
     * Use this for successful PUT/PATCH operations that update a resource.
     * Returns the updated resource in the response body.
     *
     * **Usage Examples:**
     * ```php
     * // Basic update
     * return updatedResponse($updatedUser);
     *
     * // With custom message
     * return updatedResponse($updatedUser, 'User updated successfully');
     *
     * // With metadata
     * return updatedResponse($updatedUser, null, ['updated_at' => time()]);
     * ```
     *
     * @param  mixed        $data    The updated resource data
     * @param  string|null  $message Optional custom message
     * @param  array|null   $meta    Optional metadata
     * @param  array|null   $headers Optional HTTP headers
     * @return \Illuminate\Http\JsonResponse
     */
    function updatedResponse($data = [], $message = null, $meta = null, $headers = [])
    {
        return app('api-response')->updated($data, $message, $meta, $headers);
    }
}

if (!function_exists('deletedResponse')) {
    /**
     * Send a resource deleted response (200 OK).
     *
     * Use this for successful DELETE operations that return a confirmation message.
     * For DELETE operations with no response body, use noContentResponse() instead.
     *
     * **Usage Examples:**
     * ```php
     * // Basic deletion
     * return deletedResponse('User deleted successfully');
     *
     * // With metadata
     * return deletedResponse('Resource deleted', ['deleted_at' => time()]);
     *
     * // With count
     * return deletedResponse('5 items deleted', ['count' => 5]);
     * ```
     *
     * @param  string|null  $message Optional custom message
     * @param  array|null   $meta    Optional metadata (e.g., deleted count)
     * @param  array|null   $headers Optional HTTP headers
     * @return \Illuminate\Http\JsonResponse
     */
    function deletedResponse($message = null, $meta = null, $headers = [])
    {
        return app('api-response')->deleted($message, $meta, $headers);
    }
}

if (!function_exists('conflictResponse')) {
    /**
     * Send a conflict response (409 Conflict).
     *
     * Use this when a request conflicts with the current state of the resource.
     * Common for duplicate entries, concurrent modifications, or business rule violations.
     *
     * **Usage Examples:**
     * ```php
     * // Duplicate resource
     * return conflictResponse('User with this email already exists');
     *
     * // Concurrent modification
     * return conflictResponse('Resource has been modified', ['version' => ['Stale version']]);
     *
     * // Business rule violation
     * return conflictResponse('Cannot delete resource with active dependencies');
     * ```
     *
     * @param  string|null  $message Optional custom message
     * @param  array|null   $errors  Optional error details
     * @param  array|null   $headers Optional HTTP headers
     * @return \Illuminate\Http\JsonResponse
     */
    function conflictResponse($message = null, $errors = null, $headers = [])
    {
        return app('api-response')->error(Response::HTTP_CONFLICT, $message, $errors, $headers);
    }
}

if (!function_exists('badRequestResponse')) {
    /**
     * Send a bad request response (400 Bad Request).
     *
     * Use this when the request is malformed or contains invalid parameters.
     *
     * **Usage Examples:**
     * ```php
     * // Malformed request
     * return badRequestResponse('Invalid JSON format');
     *
     * // Invalid parameters
     * return badRequestResponse('Invalid parameters', ['format' => ['Expected JSON']]);
     *
     * // Missing required data
     * return badRequestResponse('Required fields missing');
     * ```
     *
     * @param  string|null  $message Optional custom message
     * @param  array|null   $errors  Optional error details
     * @param  array|null   $headers Optional HTTP headers
     * @return \Illuminate\Http\JsonResponse
     */
    function badRequestResponse($message = null, $errors = null, $headers = [])
    {
        return app('api-response')->badRequest($message, $errors, $headers);
    }
}

if (!function_exists('serverErrorResponse')) {
    /**
     * Send an internal server error response (500 Internal Server Error).
     *
     * Alias for serverErrorResponse(). Use this for unexpected server errors.
     *
     * **Usage Examples:**
     * ```php
     * // Generic error
     * return internalServerErrorResponse('An unexpected error occurred');
     *
     * // With error details
     * return internalServerErrorResponse('Processing failed', ['error' => $e->getMessage()]);
     * ```
     *
     * @param  string|null  $message Optional custom message
     * @param  array|null   $errors  Optional error details
     * @param  array|null   $headers Optional HTTP headers
     * @return \Illuminate\Http\JsonResponse
     */
    function serverErrorResponse($message = null, $errors = null, $headers = [])
    {
        return app('api-response')->error(Response::HTTP_INTERNAL_SERVER_ERROR, $message, $errors, $headers);
    }
}

if (!function_exists('notAcceptableResponse')) {
    /**
     * Send a not acceptable response (406 Not Acceptable).
     *
     * Use this when the server cannot produce a response matching the Accept headers.
     *
     * **Usage Examples:**
     * ```php
     * // Unsupported format
     * return notAcceptableResponse('Only JSON format is supported');
     *
     * // With supported formats
     * return notAcceptableResponse('Format not acceptable', ['supported' => ['application/json']]);
     * ```
     *
     * @param  string|null  $message Optional custom message
     * @param  array|null   $errors  Optional error details
     * @param  array|null   $headers Optional HTTP headers
     * @return \Illuminate\Http\JsonResponse
     */
    function notAcceptableResponse($message = null, $errors = null, $headers = [])
    {
        return app('api-response')->notAcceptable($message, $errors, $headers);
    }
}

if (!function_exists('methodNotAllowedResponse')) {
    /**
     * Send a method not allowed response (405 Method Not Allowed).
     *
     * Use this when the HTTP method is not supported for the requested resource.
     * Should include an Allow header listing supported methods.
     *
     * **Usage Examples:**
     * ```php
     * // Basic method not allowed
     * return methodNotAllowedResponse('Method not allowed', null, ['Allow' => 'GET, POST']);
     *
     * // With error details
     * return methodNotAllowedResponse('PUT not supported', ['method' => ['Use POST instead']]);
     * ```
     *
     * @param  string|null  $message Optional custom message
     * @param  array|null   $errors  Optional error details
     * @param  array|null   $headers Optional HTTP headers (should include Allow)
     * @return \Illuminate\Http\JsonResponse
     */
    function methodNotAllowedResponse($message = null, $errors = null, $headers = [])
    {
        return app('api-response')->error(Response::HTTP_METHOD_NOT_ALLOWED, $message, $errors, $headers);
    }
}

if (!function_exists('unprocessableEntityResponse')) {
    /**
     * Send an unprocessable entity response (422 Unprocessable Entity).
     *
     * Alias for validationErrorResponse(). Use this for semantic validation failures
     * where the request is well-formed but contains semantic errors.
     *
     * **Usage Examples:**
     * ```php
     * // Validation failure
     * return unprocessableEntityResponse('Validation failed', [
     *     'email' => ['Invalid email format'],
     *     'password' => ['Must be at least 8 characters']
     * ]);
     *
     * // Business rule violation
     * return unprocessableEntityResponse('Cannot process', ['age' => ['Must be 18+']]);
     *
     * // From Laravel validator
     * return unprocessableEntityResponse('Validation failed', $validator->errors()->toArray());
     * ```
     *
     * @param  string|null  $message Optional custom message
     * @param  array        $errors  Field-specific validation errors
     * @param  array|null   $headers Optional HTTP headers
     * @return \Illuminate\Http\JsonResponse
     */
    function unprocessableEntityResponse($message = null, $errors = [], $headers = [])
    {
        return app('api-response')->validationError($message, $errors, $headers);
    }
}

if (!function_exists('payloadTooLargeResponse')) {
    /**
     * Send a payload too large response (413 Payload Too Large).
     *
     * Use this when the request payload exceeds size limits.
     *
     * **Usage Examples:**
     * ```php
     * // File too large
     * return payloadTooLargeResponse('File size exceeds 10MB limit');
     *
     * // With size info
     * return payloadTooLargeResponse('Payload too large', ['max_size' => '10MB', 'received' => '15MB']);
     *
     * // With Retry-After
     * return payloadTooLargeResponse('Too large', null, ['Retry-After' => '3600']);
     * ```
     *
     * @param  string|null  $message Optional custom message
     * @param  array|null   $errors  Optional error details (size limits, etc.)
     * @param  array|null   $headers Optional HTTP headers
     * @return \Illuminate\Http\JsonResponse
     */
    function payloadTooLargeResponse($message = null, $errors = null, $headers = [])
    {
        return app('api-response')->error(Response::HTTP_REQUEST_ENTITY_TOO_LARGE, $message, $errors, $headers);
    }
}

if (!function_exists('unsupportedMediaTypeResponse')) {
    /**
     * Send an unsupported media type response (415 Unsupported Media Type).
     *
     * Use this when the request payload is in an unsupported format.
     *
     * **Usage Examples:**
     * ```php
     * // Unsupported content type
     * return unsupportedMediaTypeResponse('Content-Type not supported');
     *
     * // With supported types
     * return unsupportedMediaTypeResponse('Unsupported format', ['supported' => ['application/json']]);
     * ```
     *
     * @param  string|null  $message Optional custom message
     * @param  array|null   $errors  Optional error details
     * @param  array|null   $headers Optional HTTP headers
     * @return \Illuminate\Http\JsonResponse
     */
    function unsupportedMediaTypeResponse($message = null, $errors = null, $headers = [])
    {
        return app('api-response')->error(Response::HTTP_UNSUPPORTED_MEDIA_TYPE, $message, $errors, $headers);
    }
}

if (!function_exists('goneResponse')) {
    /**
     * Send a gone response (410 Gone).
     *
     * Use this when a resource was previously available but has been permanently removed.
     * Different from 404 as it indicates the absence is intentional and permanent.
     *
     * **Usage Examples:**
     * ```php
     * // Resource permanently deleted
     * return goneResponse('This resource has been permanently removed');
     *
     * // Deprecated endpoint
     * return goneResponse('This API endpoint is no longer available');
     *
     * // With migration info
     * return goneResponse('Resource moved', ['new_location' => '/v2/users']);
     * ```
     *
     * @param  string|null  $message Optional custom message
     * @param  array|null   $errors  Optional error details
     * @param  array|null   $headers Optional HTTP headers
     * @return \Illuminate\Http\JsonResponse
     */
    function goneResponse($message = null, $errors = null, $headers = [])
    {
        return app('api-response')->error(Response::HTTP_GONE, $message, $errors, $headers);
    }
}

if (!function_exists('badGatewayResponse')) {
    /**
     * Send a bad gateway response (502 Bad Gateway).
     *
     * Use this when the server received an invalid response from an upstream server.
     *
     * **Usage Examples:**
     * ```php
     * // Upstream service error
     * return badGatewayResponse('Upstream service unavailable');
     *
     * // With service info
     * return badGatewayResponse('Bad gateway', ['service' => 'payment-api']);
     *
     * // With retry info
     * return badGatewayResponse('Gateway error', null, ['Retry-After' => '60']);
     * ```
     *
     * @param  string|null  $message Optional custom message
     * @param  array|null   $errors  Optional error details
     * @param  array|null   $headers Optional HTTP headers
     * @return \Illuminate\Http\JsonResponse
     */
    function badGatewayResponse($message = null, $errors = null, $headers = [])
    {
        return app('api-response')->error(Response::HTTP_BAD_GATEWAY, $message, $errors, $headers);
    }
}

if (!function_exists('gatewayTimeoutResponse')) {
    /**
     * Send a gateway timeout response (504 Gateway Timeout).
     *
     * Use this when the server didn't receive a timely response from an upstream server.
     *
     * **Usage Examples:**
     * ```php
     * // Upstream timeout
     * return gatewayTimeoutResponse('Upstream service timeout');
     *
     * // With timeout info
     * return gatewayTimeoutResponse('Gateway timeout', ['timeout' => '30s', 'service' => 'api']);
     *
     * // With retry suggestion
     * return gatewayTimeoutResponse('Timeout', null, ['Retry-After' => '120']);
     * ```
     *
     * @param  string|null  $message Optional custom message
     * @param  array|null   $errors  Optional error details
     * @param  array|null   $headers Optional HTTP headers
     * @return \Illuminate\Http\JsonResponse
     */
    function gatewayTimeoutResponse($message = null, $errors = null, $headers = [])
    {
        return app('api-response')->error(Response::HTTP_GATEWAY_TIMEOUT, $message, $errors, $headers);
    }
}

if (!function_exists('preconditionFailedResponse')) {
    /**
     * Send a precondition failed response (412 Precondition Failed).
     *
     * Use this when a precondition in the request headers evaluated to false.
     *
     * **Usage Examples:**
     * ```php
     * // If-Match failed
     * return preconditionFailedResponse('If-Match precondition failed');
     *
     * // If-Unmodified-Since failed
     * return preconditionFailedResponse('Resource has been modified');
     * ```
     *
     * @param  string|null  $message Optional custom message
     * @param  array|null   $errors  Optional error details
     * @param  array|null   $headers Optional HTTP headers
     * @return \Illuminate\Http\JsonResponse
     */
    function preconditionFailedResponse($message = null, $errors = null, $headers = [])
    {
        return app('api-response')->error(Response::HTTP_PRECONDITION_FAILED, $message, $errors, $headers);
    }
}

if (!function_exists('requestTimeoutResponse')) {
    /**
     * Send a request timeout response (408 Request Timeout).
     *
     * Use this when the server times out waiting for the request.
     *
     * **Usage Examples:**
     * ```php
     * // Request timeout
     * return requestTimeoutResponse('Request timeout');
     *
     * // With timeout info
     * return requestTimeoutResponse('Request took too long', ['timeout' => '30s']);
     * ```
     *
     * @param  string|null  $message Optional custom message
     * @param  array|null   $errors  Optional error details
     * @param  array|null   $headers Optional HTTP headers
     * @return \Illuminate\Http\JsonResponse
     */
    function requestTimeoutResponse($message = null, $errors = null, $headers = [])
    {
        return app('api-response')->error(Response::HTTP_REQUEST_TIMEOUT, $message, $errors, $headers);
    }
}

if (!function_exists('notImplementedResponse')) {
    /**
     * Send a not implemented response (501 Not Implemented).
     *
     * Use this when the server does not support the functionality required to fulfill the request.
     *
     * **Usage Examples:**
     * ```php
     * // Feature not implemented
     * return notImplementedResponse('This feature is not yet implemented');
     *
     * // Unsupported operation
     * return notImplementedResponse('Operation not supported');
     *
     * // Planned feature
     * return notImplementedResponse('Coming soon', ['eta' => '2026-Q2']);
     * ```
     *
     * @param  string|null  $message Optional custom message
     * @param  array|null   $errors  Optional error details
     * @param  array|null   $headers Optional HTTP headers
     * @return \Illuminate\Http\JsonResponse
     */
    function notImplementedResponse($message = null, $errors = null, $headers = [])
    {
        return app('api-response')->error(Response::HTTP_NOT_IMPLEMENTED, $message, $errors, $headers);
    }
}

if (!function_exists('preconditionRequiredResponse')) {
    /**
     * Send a precondition required response (428 Precondition Required).
     *
     * Use this when the server requires the request to be conditional.
     *
     * **Usage Examples:**
     * ```php
     * // Conditional request required
     * return preconditionRequiredResponse('Conditional request required');
     *
     * // With required headers
     * return preconditionRequiredResponse('If-Match header required', ['required' => 'If-Match']);
     * ```
     *
     * @param  string|null  $message Optional custom message
     * @param  array|null   $errors  Optional error details
     * @param  array|null   $headers Optional HTTP headers
     * @return \Illuminate\Http\JsonResponse
     */
    function preconditionRequiredResponse($message = null, $errors = null, $headers = [])
    {
        return app('api-response')->error(Response::HTTP_PRECONDITION_REQUIRED, $message, $errors, $headers);
    }
}


