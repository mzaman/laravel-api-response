<?php

namespace MasudZaman\LaravelApiResponse\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class NotAcceptableException extends \Exception
{
  protected int $statusCode = Response::HTTP_NOT_ACCEPTABLE;

  public function __construct(string $message = 'Not acceptable', ?\Throwable $previous = null)
  {
    parent::__construct($message, $this->statusCode, $previous);
  }

  public function getStatusCode(): int
  {
    return $this->statusCode;
  }
}
