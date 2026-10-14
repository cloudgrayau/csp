<?php
namespace cloudgrayau\csp\variables;

use cloudgrayau\csp\Csp;
use cloudgrayau\csp\services\PolicyService;

class CspVariable {

  public function registerNonce(string $type = 'script-src'): string {
    return app(PolicyService::class)->registerNonce($type);
  }

  public function config(array $config = []): void {
    $settings = Csp::getInstance()->getSettings();
    foreach ($config as $property => $value) {
      if (property_exists($settings, $property)) {
        $settings->$property = $value;
      }
    }
  }

}