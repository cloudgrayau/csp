<?php
/**
 * CSP config.php
 *
 * This file exists only as a template for the CSP settings.
 * It does nothing on its own.
 *
 * Don't edit this file, instead copy it to 'craft/config' as 'csp.php'
 * and make your changes there to override default settings.
 *
 * Once copied to 'craft/config', this file will be multi-environment aware as
 * well, so you can have different settings groups for each environment, just as
 * you do for 'general.php'
 */

return [
  'cspEnabled' => true,
  'cspMode' => 'header', /* header|tag|report */
  'cspOptions' => [ /* enabled options */
    'defaultSrc',
    'scriptSrc',
    'styleSrc',
    'imgSrc',
    'connectSrc'
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
  'styleSrc' => [
    ["'self'"]
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
