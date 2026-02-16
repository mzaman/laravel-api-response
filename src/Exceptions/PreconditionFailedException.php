<?php

namespace MasudZaman\LaravelApiResponse\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class PreconditionFailedException extends \Exception
{
  protected int $statusCode = Response::HTTP_PRECONDITION_FAILED;

  public function __construct(string $message = 'Precondition failed', ?\Throwable $previous = null)
  {
    parent::__construct($message, $this->statusCode, $previous);
  }

  public function getStatusCode(): int
  {
    return $this->statusCode;
  }
}
