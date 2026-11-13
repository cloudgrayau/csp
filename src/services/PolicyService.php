<?php
namespace cloudgrayau\csp\services;

use cloudgrayau\csp\Csp;
use cloudgrayau\csp\models\Settings;

class PolicyService {

  private array $cspData = [];
  private array $nonceData = [];

  private function addSeomaticNonces(): void {
    $class = '\\nystudio107\\seomatic\\helpers\\DynamicMeta';
    if (!class_exists($class)) {
      return; // SEOMatic isn't ported to Craft 6 yet; revisit when it is
    }
    try {
      foreach ($class::getCspNonces() as $row) {
        foreach (self::sources((string) $row) as $source) {
          if (!in_array($source, $this->cspData['script-src'] ?? [], true)) {
            $this->cspData['script-src'][] = $source;
          }
        }
      }
    } catch (\Throwable) {
    }
  }

  /** A row's space-separated sources, ignoring repeated spaces. */
  private static function sources(string $row): array {
    return preg_split('/\s+/', trim($row), -1, PREG_SPLIT_NO_EMPTY) ?: [];
  }

  private function settings(): Settings {
    return Csp::getInstance()->getSettings();
  }

  public function protectionHeaders(): array {
    $settings = $this->settings();
    $headers = [];
    if ($settings->protectionEnabled) {
      foreach ($settings->headerProtection as $header) {
        $header = (array) $header;
        if (!empty($header[0]) && !empty($header[1])) {
          $headers[trim($header[0])] = trim($header[1]);
        }
      }
    }
    return $headers;
  }

  public function build(): void {
    $settings = $this->settings();
    $options = $settings->cspOptions;
    if ($settings->cspMode === 'tag') { // these directives aren't supported in meta tags
      $options = array_values(array_diff($options, ['reportUri', 'frameAncestors', 'sandbox']));
    }
    $this->cspData = [];
    foreach ($options as $option) {
      if (!isset(Settings::POLICY_OPTIONS[$option])) {
        continue;
      }
      $key = Settings::POLICY_OPTIONS[$option];
      foreach ($settings->rows($option) as $row) {
        foreach (self::sources($row[0]) as $source) {
          $this->cspData[$key][] = $source;
        }
      }
    }
    foreach ($this->nonceData as $key => $nonces) {
      foreach ($nonces as $nonce) {
        $csp = "'nonce-" . trim($nonce) . "'";
        if (!in_array($csp, $this->cspData[$key] ?? [], true)) {
          $this->cspData[$key][] = $csp;
        }
      }
    }
    $this->addSeomaticNonces();
  }

  public function headerName(): string {
    return ($this->settings()->cspMode === 'report')
      ? 'Content-Security-Policy-Report-Only'
      : 'Content-Security-Policy';
  }

  public function headerValue(): string {
    $value = '';
    foreach ($this->cspData as $key => $sources) {
      $value .= $key . ' ' . implode(' ', $sources) . '; ';
    }
    return trim($value);
  }

  public function registerNonce(string $type = 'script-src'): string {
    $nonce = base64_encode(random_bytes(18)); // 144 random bits; a collision isn't a practical concern
    if (in_array($type, Settings::POLICY_OPTIONS, true)) {
      $this->nonceData[$type][] = $nonce;
    }
    return $nonce;
  }

}