<?php

namespace MasudZaman\LaravelApiResponse\Constants;

/**
 * HTTP Status Codes and API-related status codes
 */
class HttpStatusCode
{
    // HTTP Success Codes
    const OK = 200;
    const CREATED = 201;
    const ACCEPTED = 202;
    const NO_CONTENT = 204;

    // Client Errors
    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const METHOD_NOT_ALLOWED = 405;
    const CONFLICT = 409;
    const UNPROCESSABLE_ENTITY = 422;
    const TOO_MANY_REQUESTS = 429;

    // Server Errors
    const INTERNAL_SERVER_ERROR = 500;
    const NOT_IMPLEMENTED = 501;
    const BAD_GATEWAY = 502;
    const SERVICE_UNAVAILABLE = 503;
    const GATEWAY_TIMEOUT = 504;

    // API-specific Codes (Custom for API Logic)
    const API_SUCCESS = 1000;
    const API_CREATED = 1001;
    const API_BAD_REQUEST = 1002;
    const API_UNAUTHORIZED = 1003;
    const API_FORBIDDEN = 1004;
    const API_NOT_FOUND = 1005;
    const API_INTERNAL_ERROR = 1006;
    const API_TOO_MANY_REQUESTS = 1007;

    // Response Messages (Reusable)
    const SUCCESS_MESSAGE = 'Operation successful';
    const CREATED_MESSAGE = 'Resource created successfully';
    const NO_CONTENT_MESSAGE = 'No content to return';
    const BAD_REQUEST_MESSAGE = 'Bad Request';
    const UNAUTHORIZED_MESSAGE = 'Authentication error';
    const FORBIDDEN_MESSAGE = 'Forbidden - Invalid X-Client-ID';
    const NOT_FOUND_MESSAGE = 'Resource not found';
    const INTERNAL_SERVER_ERROR_MESSAGE = 'An unexpected error occurred';
}
