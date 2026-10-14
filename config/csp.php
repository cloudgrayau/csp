<?php
/**
 * CSP config.php
 *
 * Publish this file to `config/craft/csp.php` and change the settings there;
 * values set here override the ones saved in the control panel.
 */

return [
  'cspEnabled' => true,
  'cspMode' => 'header', /* header|tag|report */
  'cspOptions' => [ /* enabled options */
    'defaultSrc',
    'scriptSrc',
    'styleSrc',
    'imgSrc',
    'connectSrc',
    'formAction',
    'baseUri'
  ],
  'protectionEnabled' => true,
  'headerProtection' => [
    ['Referrer-Policy', 'strict-origin-when-cross-origin'],
    ['Strict-Transport-Security', 'max-age=31536000;includeSubDomains;preload'],
    ['X-Content-Type-Options', 'nosniff'],
    ['X-Frame-Options', 'SAMEORIGIN'],
    ['X-Xss-Protection', '1; mode=block'],
  ],
  'defaultSrc' => [
    ["'none'"]
  ],
  'scriptSrc' => [
    ["'self'"]
  ],
  'scriptSrcAttr' => [
  ],
  'scriptSrcElem' => [
  ],
  'styleSrc' => [
    ["'self'"]
  ],
  'styleSrcAttr' => [
  ],
  'styleSrcElem' => [
  ],
  'imgSrc' => [
    ["'self'"]
  ],
  'connectSrc' => [
    ["'self'"]
  ],
  'fontSrc' => [
  ],
  'objectSrc' => [
  ],
  'mediaSrc' => [
  ],
  'frameSrc' => [
  ],
  'sandbox' => [
  ],
  'reportUri' => [
  ],
  'childSrc' => [
  ],
  'formAction' => [
    ["'self'"]
  ],
  'frameAncestors' => [
  ],
  'pluginTypes' => [
  ],
  'baseUri' => [
    ["'self'"]
  ],
  'reportTo' => [
  ],
  'workerSrc' => [
  ],
  'manifestSrc' => [
  ],
  'prefetchSrc' => [
  ],
  'navigateTo' => [
  ],
];
