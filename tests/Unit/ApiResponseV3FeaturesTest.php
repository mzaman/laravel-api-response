<?php

namespace Tests\Unit;

use MasudZaman\LaravelApiResponse\Response\ApiResponse;
use Illuminate\Http\Response;
use Orchestra\Testbench\TestCase;

class ApiResponseV3FeaturesTest extends TestCase
{
  protected $apiResponse;

  protected function setUp(): void
  {
    parent::setUp();
    $this->apiResponse = new ApiResponse();
  }

  /**
   * Get package providers.
   */
  protected function getPackageProviders($app)
  {
    return [
      \MasudZaman\LaravelApiResponse\Providers\LaravelApiResponseServiceProvider::class,
    ];
  }

  /**
   * Test Request ID is included when X-Request-ID header is present
   */
  public function test_request_id_is_included_in_success_response()
  {
    // Simulate request with X-Request-ID header
    $this->app['request']->headers->set('X-Request-ID', 'test-request-123');

    $response = $this->apiResponse->success(['data' => 'test'], 200, 'Success');
    $data = $response->getData(true);

    $this->assertArrayHasKey('meta', $data);
    $this->assertArrayHasKey('request_id', $data['meta']);
    $this->assertEquals('test-request-123', $data['meta']['request_id']);
  }

  /**
   * Test Request ID is included in error responses
   */
  public function test_request_id_is_included_in_error_response()
  {
    $this->app['request']->headers->set('X-Request-ID', 'error-request-456');

    $response = $this->apiResponse->error(404, 'Not found');
    $data = $response->getData(true);

    $this->assertArrayHasKey('request_id', $data);
    $this->assertEquals('error-request-456', $data['request_id']);
  }

  /**
   * Test custom error code is included
   */
  public function test_custom_error_code_is_included()
  {
    $response = $this->apiResponse->error(
      404,
      'Raindrop not found',
      [],
      [],
      'RAINDROP_NOT_FOUND'
    );
    $data = $response->getData(true);

    $this->assertArrayHasKey('error_code', $data);
    $this->assertEquals('RAINDROP_NOT_FOUND', $data['error_code']);
  }

  /**
   * Test auto-generated error code when custom code is not provided
   */
  public function test_auto_generated_error_code()
  {
    $response = $this->apiResponse->error(404, 'Not found');
    $data = $response->getData(true);

    $this->assertArrayHasKey('error_code', $data);
    $this->assertNotEmpty($data['error_code']);
  }

  /**
   * Test context data is included in debug mode
   */
  public function test_context_data_is_included_in_debug_mode()
  {
    // Enable debug mode
    config(['app.debug' => true]);

    $context = [
      'raindrop_id' => 12345,
      'user_id' => 67890,
      'collection_id' => -1
    ];

    $response = $this->apiResponse->error(
      404,
      'Raindrop not found',
      [],
      [],
      'RAINDROP_NOT_FOUND',
      $context
    );
    $data = $response->getData(true);

    $this->assertArrayHasKey('context', $data);
    $this->assertEquals($context, $data['context']);
  }

  /**
   * Test context data is excluded in production mode
   */
  public function test_context_data_is_excluded_in_production_mode()
  {
    // Disable debug mode (production)
    config(['app.debug' => false]);

    $context = [
      'raindrop_id' => 12345,
      'user_id' => 67890
    ];

    $response = $this->apiResponse->error(
      404,
      'Raindrop not found',
      [],
      [],
      'RAINDROP_NOT_FOUND',
      $context
    );
    $data = $response->getData(true);

    $this->assertArrayNotHasKey('context', $data);
  }

  /**
   * Test empty context is not included even in debug mode
   */
  public function test_empty_context_is_not_included()
  {
    config(['app.debug' => true]);

    $response = $this->apiResponse->error(
      404,
      'Not found',
      [],
      [],
      null,
      [] // Empty context
    );
    $data = $response->getData(true);

    $this->assertArrayNotHasKey('context', $data);
  }

  /**
   * Test Retry-After header is included in manyRequests response
   */
  public function test_retry_after_header_in_many_requests()
  {
    $response = $this->apiResponse->manyRequests(
      'Too many requests',
      [],
      [],
      60 // Retry after 60 seconds
    );

    $this->assertEquals(Response::HTTP_TOO_MANY_REQUESTS, $response->getStatusCode());
    $this->assertEquals('60', $response->headers->get('Retry-After'));
  }

  /**
   * Test Retry-After header is included in serviceUnavailable response
   */
  public function test_retry_after_header_in_service_unavailable()
  {
    $response = $this->apiResponse->serviceUnavailable(
      'Service temporarily unavailable',
      [],
      [],
      120 // Retry after 120 seconds
    );

    $this->assertEquals(Response::HTTP_SERVICE_UNAVAILABLE, $response->getStatusCode());
    $this->assertEquals('120', $response->headers->get('Retry-After'));
  }

  /**
   * Test Retry-After header is not included when parameter is null
   */
  public function test_retry_after_header_not_included_when_null()
  {
    $response = $this->apiResponse->manyRequests('Too many requests');

    $this->assertEquals(Response::HTTP_TOO_MANY_REQUESTS, $response->getStatusCode());
    $this->assertNull($response->headers->get('Retry-After'));
  }

  /**
   * Test backward compatibility - old error() signature still works
   */
  public function test_backward_compatibility_error_method()
  {
    // Old signature: error($code, $message, $errors, $headers)
    $response = $this->apiResponse->error(404, 'Not found', ['field' => 'error'], []);

    $this->assertEquals(404, $response->getStatusCode());
    $data = $response->getData(true);
    $this->assertEquals('Not found', $data['message']);
    $this->assertArrayHasKey('errors', $data);
  }

  /**
   * Test backward compatibility - old manyRequests() signature still works
   */
  public function test_backward_compatibility_many_requests_method()
  {
    // Old signature: manyRequests($message, $errors, $headers)
    $response = $this->apiResponse->manyRequests('Too many requests', [], []);

    $this->assertEquals(Response::HTTP_TOO_MANY_REQUESTS, $response->getStatusCode());
    $data = $response->getData(true);
    $this->assertEquals('Too many requests', $data['message']);
  }

  /**
   * Test backward compatibility - old serviceUnavailable() signature still works
   */
  public function test_backward_compatibility_service_unavailable_method()
  {
    // Old signature: serviceUnavailable($message, $errors, $headers)
    $response = $this->apiResponse->serviceUnavailable('Service unavailable', [], []);

    $this->assertEquals(Response::HTTP_SERVICE_UNAVAILABLE, $response->getStatusCode());
    $data = $response->getData(true);
    $this->assertEquals('Service unavailable', $data['message']);
  }

  /**
   * Test all features combined
   */
  public function test_all_features_combined()
  {
    config(['app.debug' => true]);
    $this->app['request']->headers->set('X-Request-ID', 'combined-test-789');

    $response = $this->apiResponse->error(
      404,
      'Resource not found',
      ['field' => 'validation error'],
      [],
      'RESOURCE_NOT_FOUND',
      ['resource_id' => 999, 'resource_type' => 'raindrop']
    );

    $data = $response->getData(true);

    // Check status code
    $this->assertEquals(404, $response->getStatusCode());

    // Check message
    $this->assertEquals('Resource not found', $data['message']);

    // Check errors
    $this->assertArrayHasKey('errors', $data);
    $this->assertEquals(['field' => 'validation error'], $data['errors']);

    // Check custom error code
    $this->assertArrayHasKey('error_code', $data);
    $this->assertEquals('RESOURCE_NOT_FOUND', $data['error_code']);

    // Check request ID
    $this->assertArrayHasKey('request_id', $data);
    $this->assertEquals('combined-test-789', $data['request_id']);

    // Check context
    $this->assertArrayHasKey('context', $data);
    $this->assertEquals(['resource_id' => 999, 'resource_type' => 'raindrop'], $data['context']);
  }

  /**
   * Test response structure consistency
   */
  public function test_response_structure_consistency()
  {
    $response = $this->apiResponse->error(
      400,
      'Bad request',
      [],
      [],
      'BAD_REQUEST'
    );

    $data = $response->getData(true);

    // Check required fields
    $this->assertArrayHasKey('status', $data);
    $this->assertArrayHasKey('code', $data);
    $this->assertArrayHasKey('message', $data);
    $this->assertArrayHasKey('errors', $data);
    $this->assertArrayHasKey('error_type', $data);
    $this->assertArrayHasKey('error_code', $data);
  }

  /**
   * Test success response structure
   */
  public function test_success_response_structure()
  {
    $response = $this->apiResponse->success(['data' => 'test'], 200, 'Success');
    $data = $response->getData(true);

    $this->assertEquals(200, $response->getStatusCode());
    $this->assertArrayHasKey('result', $data);
    $this->assertTrue($data['result']);
  }

  /**
   * Test error response structure
   */
  public function test_error_response_structure()
  {
    $response = $this->apiResponse->error(404, 'Not found');
    $data = $response->getData(true);

    $this->assertEquals(404, $response->getStatusCode());
    $this->assertArrayHasKey('status', $data);
    $this->assertArrayHasKey('message', $data);
  }
}
