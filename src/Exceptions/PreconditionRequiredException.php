<?php

namespace MasudZaman\LaravelApiResponse\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class PreconditionRequiredException extends \Exception
{
  protected int $statusCode = Response::HTTP_PRECONDITION_REQUIRED;

  public function __construct(string $message = 'Precondition required', ?\Throwable $previous = null)
  {
    parent::__construct($message, $this->statusCode, $previous);
  }

  public function getStatusCode(): int
  {
    return $this->statusCode;
  }
}
