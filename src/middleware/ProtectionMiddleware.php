<?php
namespace cloudgrayau\csp\middleware;

use cloudgrayau\csp\helpers\RequestHelper;
use cloudgrayau\csp\services\PolicyService;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProtectionMiddleware {

  public function __construct(private PolicyService $policy) {}

  public function handle(Request $request, Closure $next): Response {
    if (RequestHelper::isCp($request)) {
      return $next($request);
    }
    $response = $next($request);
    foreach ($this->policy->protectionHeaders() as $name => $value) {
      $response->headers->set($name, $value);
    }
    return $response;
  }

}