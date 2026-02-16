<?php

namespace MasudZaman\LaravelApiResponse\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class NotImplementedException extends \Exception
{
  protected int $statusCode = Response::HTTP_NOT_IMPLEMENTED;

  public function __construct(string $message = 'Not implemented', ?\Throwable $previous = null)
  {
    parent::__construct($message, $this->statusCode, $previous);
  }

  public function getStatusCode(): int
  {
    return $this->statusCode;
  }
}
