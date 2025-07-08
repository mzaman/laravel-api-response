<?php

namespace Tests;

use MasudZaman\LaravelApiResponse\Http\Resources\BaseResponse;
use PHPUnit\Framework\TestCase;

class ApiResponseTest extends TestCase
{
    public function test_success_response()
    {
        $response = api_response(['data' => 'Some data'], 'success', 200, 'Data fetched successfully');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Data fetched successfully', $response->getData()->message);
    }

    public function test_error_response()
    {
        $response = api_error('Something went wrong', 'error', 500, ['error' => 'Details of error']);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('Something went wrong', $response->getData()->message);
    }
}
