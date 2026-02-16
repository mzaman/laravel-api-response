<?php

namespace MasudZaman\LaravelApiResponse\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class RequestTimeoutException extends \Exception
{
  protected int $statusCode = Response::HTTP_REQUEST_TIMEOUT;

  public function __construct(string $message = 'Request timeout', ?\Throwable $previous = null)
  {
    parent::__construct($message, $this->statusCode, $previous);
  }

  public function getStatusCode(): int
  {
    return $this->statusCode;
  }
}
