# Changelog

All notable changes to `laravel-api-response` will be documented in this file.

## [3.3.0] - 2026-02-13

### Added
- **Request ID Support**: Automatically includes `X-Request-ID` header in all success and error responses for request correlation
- **Custom Error Codes**: Added `$errorCode` parameter to `error()` method for machine-readable error codes
- **Context Data**: Added `$context` parameter to `error()` method for additional debugging information (only shown in debug mode)
- **Retry-After Header**: Added `$retryAfter` parameter to `manyRequests()` and `serviceUnavailable()` methods to tell clients when to retry

### Changed
- Enhanced `success()` method to automatically add request ID to meta array
- Enhanced `error()` method with new optional parameters (backward compatible)
- Enhanced `exceptionResponse()` to include request ID
- Updated `manyRequests()` to support Retry-After header
- Updated `serviceUnavailable()` to support Retry-After header

### Backward Compatibility
- All changes are backward compatible
- New parameters are optional with default values
- Existing code will continue to work without modifications

### Usage Examples

#### Request ID (Automatic)
```php
// Request ID is automatically added if X-Request-ID header is present
return ApiResponse::success($data);
// Response includes: "meta": {"request_id": "abc-123"}
```

#### Custom Error Code
```php
return ApiResponse::error(
    404,
    'Raindrop not found',
    [],
    [],
    'RAINDROP_NOT_FOUND'  // Machine-readable code
);
```

#### Context Data (Debug Mode Only)
```php
return ApiResponse::error(
    404,
    'Raindrop not found',
    [],
    [],
    'RAINDROP_NOT_FOUND',
    ['raindrop_id' => 123, 'user_id' => 456]  // Debug context
);
```

#### Retry-After Header
```php
// Rate limit exceeded
return ApiResponse::manyRequests(
    'Too many requests',
    [],
    [],
    60  // Retry after 60 seconds
);

// Service unavailable
return ApiResponse::serviceUnavailable(
    'External API temporarily unavailable',
    [],
    [],
    120  // Retry after 120 seconds
);
```

## [3.2.1] - Previous Release

### Previous features
- Basic success and error responses
- Exception handling
- Localization support
- Pagination support
- Multiple helper functions
