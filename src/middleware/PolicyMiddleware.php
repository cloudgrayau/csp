<?php
namespace cloudgrayau\csp\middleware;

use cloudgrayau\csp\Csp;
use cloudgrayau\csp\helpers\RequestHelper;
use cloudgrayau\csp\services\PolicyService;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PolicyMiddleware {

  public function __construct(private PolicyService $policy) {}

  public function handle(Request $request, Closure $next): Response {
    if (RequestHelper::isCp($request)) {
      return $next($request);
    }
    $response = $next($request);
    $settings = Csp::getInstance()->getSettings();
    if (!$settings->cspEnabled || !$this->isHtml($response)) {
      return $response;
    }
    foreach (['Content-Security-Policy', 'X-Content-Security-Policy', 'X-Webkit-Csp'] as $header) {
      $response->headers->remove($header);
    }
    $this->policy->build();
    $value = $this->policy->headerValue();
    if ($value === '') {
      return $response;
    }
    if ($settings->cspMode === 'tag') {
      $content = $response->getContent();
      if (is_string($content)) {
        $meta = '<meta http-equiv="' . $this->policy->headerName() . '" content="' . e($value) . '">';
        $response->setContent($this->injectMeta($content, $meta));
      }
    } else {
      $response->headers->set($this->policy->headerName(), $value);
    }
    return $response;
  }

  private function isHtml(Response $response): bool {
    if ($response instanceof JsonResponse || $response instanceof BinaryFileResponse
      || $response instanceof StreamedResponse || $response->isRedirection() || $response->isEmpty()) {
      return false;
    }
    // Laravel adds Content-Type in prepare(), after middleware runs, so a missing header means HTML.
    $type = $response->headers->get('Content-Type');
    return $type === null || str_contains(strtolower($type), 'text/html');
  }
  
  private function injectMeta(string $html, string $meta): string {
    if (!preg_match('/<head(?:\s[^>]*)?>/i', $html, $head, PREG_OFFSET_CAPTURE)) {
      return $html;
    }
    $pos = $head[0][1] + strlen($head[0][0]);
    // Keep a leading <meta charset> first, so the encoding is still declared early in the document
    if (preg_match('/\G\s*<meta\s+charset=[^>]*>/i', $html, $charset, 0, $pos)) {
      $pos += strlen($charset[0]);
    }
    return substr_replace($html, $meta, $pos, 0);
  }

}