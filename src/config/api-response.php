<?php

return [
    'default_locale' => 'en',
    'messages' => [
        // 2xx Success
        'success' => 'Your request was successful.',
        'created_success' => 'The resource was successfully created.',
        'accepted' => 'Your request has been accepted for processing.',
        'non_authoritative_information' => 'The response is not authoritative.',
        'no_content' => 'There is no content to display.',
        'reset_content' => 'Content reset successfully.',
        'partial_content' => 'Partial content returned.',

        // 3xx Redirection
        'multiple_choices' => 'There are multiple choices for your request.',
        'moved_permanently' => 'The resource has been moved permanently.',
        'found' => 'The resource has been found and redirected.',
        'see_other' => 'See another resource to complete your request.',
        'not_modified' => 'The resource has not been modified.',
        'use_proxy' => 'You must use a proxy to access this resource.',
        'temporary_redirect' => 'The resource has temporarily moved. Please try again later.',
        'permanent_redirect' => 'The resource has been permanently redirected.',

        // 4xx Client Error
        'bad_request' => 'Your request is invalid or malformed. Please check the input and try again.',
        'unauthorized' => 'Authentication is required to access this resource.',
        'payment_required' => 'Payment is required to complete the action.',
        'forbidden' => 'You do not have permission to access this resource.',
        'not_found' => 'The resource you are looking for could not be found.',
        'method_not_allowed' => 'The HTTP method used is not allowed for this resource.',
        'not_acceptable' => 'The request is not acceptable based on the headers provided.',
        'proxy_authentication_required' => 'Authentication through a proxy server is required.',
        'request_timeout' => 'Your request took too long to process. Please try again later.',
        'conflict' => 'There is a conflict with the current state of the resource.',
        'gone' => 'The resource is no longer available.',
        'length_required' => 'The server expects a valid length for the resource.',
        'precondition_failed' => 'The required precondition for the request was not met.',
        'request_entity_too_large' => 'The size of the request entity exceeds the allowed limit.',
        'request_uri_too_long' => 'The request URI is too long. Please try again with a shorter URL.',
        'unsupported_media_type' => 'The media type provided is not supported.',
        'requested_range_not_satisfiable' => 'The requested range cannot be satisfied.',
        'expectation_failed' => 'The expectation given in the request header could not be met.',
        'i_am_a_teapot' => 'I am a teapot (a playful error code).',
        'unprocessable_entity' => 'The data provided is invalid. Please review and try again.',
        'locked' => 'The resource is currently locked.',
        'failed_dependency' => 'The action failed due to a failed dependency.',
        'too_early' => 'The server is refusing the request due to early timing.',
        'upgrade_required' => 'An upgrade is required to process this request.',
        'precondition_required' => 'The request requires a specific precondition.',
        'rate_limit_exceeded' => 'You have exceeded the number of allowed requests. Please try again later.',
        'request_header_fields_too_large' => 'The request headers are too large.',
        'unavailable_for_legal_reasons' => 'The requested resource is unavailable due to legal reasons.',

        // 5xx Server Error
        'internal_server_error' => 'Something went wrong on our end. Please try again later.',
        'not_implemented' => 'This feature is not yet implemented.',
        'bad_gateway' => 'The server received an invalid response from an upstream server.',
        'service_unavailable' => 'The service is temporarily unavailable. Please try again later.',
        'gateway_timeout' => 'The server took too long to respond. Please try again later.',
        'http_version_not_supported' => 'The HTTP version used is not supported.',
        'variant_also_negotiates' => 'The server cannot fulfill your request due to a negotiation conflict.',
        'insufficient_storage' => 'The server is unable to store the representation needed to complete the request.',
        'loop_detected' => 'A loop was detected while processing your request.',
        'not_extended' => 'The server does not support the required extension.',
        'network_authentication_required' => 'Network authentication is required to complete the request.',
    ],
];

