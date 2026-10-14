<?php
namespace cloudgrayau\csp\models;

use CraftCms\Cms\Plugin\PluginSettings;
use Illuminate\Validation\Rule;

class Settings extends PluginSettings {

  public const MODE_OPTIONS = [
    'header' => 'Response Headers',
    'tag' => 'Meta Tags',
    'report' => 'Report Only',
  ];

  public const POLICY_OPTIONS = [
    'defaultSrc' => 'default-src', 'scriptSrc' => 'script-src', 'scriptSrcAttr' => 'script-src-attr',
    'scriptSrcElem' => 'script-src-elem', 'styleSrc' => 'style-src', 'styleSrcAttr' => 'style-src-attr',
    'styleSrcElem' => 'style-src-elem', 'imgSrc' => 'img-src', 'connectSrc' => 'connect-src',
    'fontSrc' => 'font-src', 'objectSrc' => 'object-src', 'mediaSrc' => 'media-src',
    'frameSrc' => 'frame-src', 'sandbox' => 'sandbox', 'reportUri' => 'report-uri',
    'childSrc' => 'child-src', 'formAction' => 'form-action', 'frameAncestors' => 'frame-ancestors',
    'pluginTypes' => 'plugin-types', 'baseUri' => 'base-uri', 'reportTo' => 'report-to',
    'workerSrc' => 'worker-src', 'manifestSrc' => 'manifest-src', 'prefetchSrc' => 'prefetch-src',
    'navigateTo' => 'navigate-to',
  ];

  public bool $cspEnabled = true;
  public string $cspMode = 'header';
  public array $cspOptions = ['defaultSrc', 'scriptSrc', 'styleSrc', 'imgSrc', 'connectSrc', 'formAction', 'baseUri'];
  public bool $protectionEnabled = true;
  public array $headerProtection = [
    ['Referrer-Policy', 'strict-origin-when-cross-origin'],
    ['Strict-Transport-Security', 'max-age=31536000;includeSubDomains;preload'],
    ['X-Content-Type-Options', 'nosniff'],
    ['X-Frame-Options', 'SAMEORIGIN'],
    ['X-Xss-Protection', '1; mode=block'],
  ];

  public array $defaultSrc = [["'none'"]];
  public array $scriptSrc = [["'self'"]];
  public array $scriptSrcAttr = [];
  public array $scriptSrcElem = [];
  public array $styleSrc = [["'self'"]];
  public array $styleSrcAttr = [];
  public array $styleSrcElem = [];
  public array $imgSrc = [["'self'"]];
  public array $connectSrc = [["'self'"]];
  public array $fontSrc = [];
  public array $objectSrc = [];
  public array $mediaSrc = [];
  public array $frameSrc = [];
  public array $sandbox = [];
  public array $reportUri = [];
  public array $childSrc = [];
  public array $formAction = [["'self'"]];
  public array $frameAncestors = [];
  public array $pluginTypes = [];
  public array $baseUri = [["'self'"]];
  public array $reportTo = [];
  public array $workerSrc = [];
  public array $manifestSrc = [];
  public array $prefetchSrc = [];
  public array $navigateTo = [];

  public function getRules(): array {
    $rules = [
      'cspEnabled' => ['boolean'],
      'protectionEnabled' => ['boolean'],
      'cspMode' => ['string', Rule::in(array_keys(self::MODE_OPTIONS))],
      'cspOptions' => ['array'],
      'cspOptions.*' => [Rule::in(array_keys(self::POLICY_OPTIONS))],
      'headerProtection' => ['array'],
    ];
    foreach (array_keys(self::POLICY_OPTIONS) as $option) {
      $rules[$option] = ['array'];
    }
    return array_merge(parent::getRules(), $rules);
  }

  /** Policy rows normalised to [[value], ...], whether set via the form or a config file. */
  public function rows(string $option): array {
    $rows = [];
    foreach ((array) ($this->$option ?? []) as $row) {
      $row = (array) $row;
      $value = isset($row[0]) ? trim((string) $row[0]) : '';
      if ($value !== '') {
        $rows[] = [$value];
      }
    }
    return $rows;
  }

}