<?php

namespace MasudZaman\LaravelApiResponse\Support;

use Illuminate\Http\Request;

trait IsApiRequest
{
  /**
   * Determine if the request is for an API route
   * 
   * Checks if the ForceJsonResponse middleware has set the Accept header to application/json
   * or if the route has the 'api' middleware applied.
   * 
   * @param \Illuminate\Http\Request $request
   * @return bool
   */
  protected function isApiRequest(Request $request): bool
  {
    // Check if request expects JSON
    // Ensure this is an API request
    if ($request->is('api/*') || $request->isJson() || $request->expectsJson()) {
      return true;
    }

    // Check if middleware set the Accept header to application/json
    $accept = $request->header('Accept', '');
    if (strpos($accept, 'application/json') !== false) {
      return true;
    }

    // Check if the route has 'api' middleware
    $route = $request->route();
    if ($route) {
      $middlewares = $route->middleware() ?? [];
      if (in_array('api', $middlewares, true)) {
        return true;
      }
    }

    return false;
  }
}
