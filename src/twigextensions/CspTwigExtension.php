<?php
namespace cloudgrayau\csp\twigextensions;

use cloudgrayau\csp\services\PolicyService;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CspTwigExtension extends AbstractExtension {

  public function getFunctions(): array {
    return [
      new TwigFunction('csp', [$this, 'registerNonce']),
    ];
  }

  public function registerNonce(string $type = 'script-src'): string {
    return app(PolicyService::class)->registerNonce($type);
  }

}