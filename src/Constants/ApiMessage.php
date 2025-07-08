<?php

namespace MasudZaman\LaravelApiResponse\Constants;

/**
 * API Response Messages for common scenarios.
 * You can override these messages using Laravel's language files.
 */
class ApiMessage
{
    const SUCCESS = 'Request was successful';
    const CREATED_SUCCESS = 'Resource created successfully';
    const BAD_REQUEST = 'Invalid request';
    const UNAUTHORIZED = 'Authentication required';
    const FORBIDDEN = 'Permission denied';
    const NOT_FOUND = 'Resource not found';
    const INTERNAL_SERVER_ERROR = 'An unexpected error occurred';
    const SERVICE_UNAVAILABLE = 'Service temporarily unavailable';
    const VALIDATION_FAILED = 'Validation failed';
    const CONFLICT = 'Conflict detected';
    const RATE_LIMIT_EXCEEDED = 'Rate limit exceeded';
}
