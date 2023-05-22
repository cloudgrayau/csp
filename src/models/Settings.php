<?php
namespace cloudgrayau\csp\models;

use craft\base\Model;
use craft\validators\ArrayValidator;

class Settings extends Model {
  
  // Static Variables
  // =========================================================================
  
  public array $modeOptions = [
    ['label' => 'Response Headers', 'value' => 'header'],
    ['label' => 'Meta Tags', 'value' => 'tag'],
    ['label' => 'Report Only', 'value' => 'report']
  ];
  public array $policyOptions = [
    'defaultSrc' => 'default-src',
    'scriptSrc' => 'script-src',
    'styleSrc' => 'style-src',
    'imgSrc' => 'img-src',
    'connectSrc' => 'connect-src',
    'fontSrc' => 'font-src',
    'objectSrc' => 'object-src',
    'mediaSrc' => 'media-src',
    'frameSrc' => 'frame-src',
    'sandbox' => 'sandbox',
    'reportUri' => 'report-uri',
    'childSrc' => 'child-src',
    'formAction' => 'form-action',
    'frameAncestors' => 'frame-ancestors',
    'pluginTypes' => 'plugin-types',
    'baseUri' => 'base-uri',
    'reportTo' => 'report-to',
    'workerSrc' => 'worker-src',
    'manifestSrc' => 'manifest-src',
    'prefetchSrc' => 'prefetch-src',
    'navigateTo' => 'navigate-to'
  ];
  
  // Editable Variables
  // =========================================================================
  
  public bool $cspEnabled = true;
  public string $cspMode = 'header';
  public array $cspOptions = [ /* Starter Policy */
    'defaultSrc',
    'scriptSrc',
    'styleSrc',
    'imgSrc',
    'connectSrc',
    'formAction',
    'baseUri'
  ];
  public bool $protectionEnabled = true;
  public array $headerProtection = [
    ['Referrer-Policy', 'strict-origin-when-cross-origin'],
    ['Strict-Transport-Security', 'max-age=31536000;includeSubDomains;preload'],
    ['X-Content-Type-Options', 'nosniff'],
    ['X-Frame-Options', 'SAMEORIGIN'],
    ['X-Xss-Protection', '1; mode=block'],
  ];
  
  public array $defaultSrc = [
    ["'none'"]
  ];
  public array $scriptSrc = [
    ["'self'"]
  ];
  public array $styleSrc = [
    ["'self'"]
  ];
  public array $imgSrc = [
    ["'self'"]
  ];
  public array $connectSrc = [
    ["'self'"]
  ];
  public array $fontSrc = [];
  public array $objectSrc = [];
  public array $mediaSrc = [];
  public array $frameSrc = [];
  public array $sandbox = [];
  public array $reportUri = [];
  public array $childSrc = [];
  public array $formAction = [
    ["'self'"]
  ];
  public array $frameAncestors = [];
  public array $pluginTypes = [];
  public array $baseUri = [
    ["'self'"]
  ];
  public array $reportTo = [];
  public array $workerSrc = [];
  public array $manifestSrc = [];
  public array $prefetchSrc = [];
  public array $navigateTo = [];
  
  // Public Methods
  // =========================================================================

  public function rules(): array {
    return [
      [['cspEnabled','protectionEnabled'], 'boolean'],
      [['cspMode'], 'string'],
      [
        [
          'headerProtection',
          'cspOptions',
          'defaultSrc',
          'scriptSrc',
          'styleSrc',
          'imgSrc',
          'connectSrc',
          'fontSrc',
          'objectSrc',
          'mediaSrc',
          'frameSrc',
          'sandbox',
          'reportUri',
          'childSrc',
          'formAction',
          'frameAncestors',
          'pluginTypes',
          'baseUri',
          'reportTo',
          'workerSrc',
          'manifestSrc',
          'prefetchSrc',
          'navigateTo'
        ], ArrayValidator::class
      ]
    ];
  }
  
}