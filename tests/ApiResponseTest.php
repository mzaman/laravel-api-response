<?php

namespace Tests;

use Orchestra\Testbench\TestCase;

class ApiResponseTest extends TestCase
{
    /**
     * Register the package service provider so helpers and app('api-response') are available.
     */
    protected function getPackageProviders($app): array
    {
        return [
            \MasudZaman\LaravelApiResponse\Providers\LaravelApiResponseServiceProvider::class,
        ];
    }

    public function test_success_response()
    {
        $response = apiResponse(['data' => 'Some data'], 'Data fetched successfully');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Data fetched successfully', $response->getData()->message);
    }

    public function test_error_response()
    {
        $response = apiError(500, 'Something went wrong', ['error' => 'Details of error']);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('Something went wrong', $response->getData()->message);
    }
}
