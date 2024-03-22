<?php
namespace cloudgrayau\csp\variables;

use cloudgrayau\csp\Csp;

class CspVariable {
    
    // Public Methods
    // =========================================================================
    
    public function registerNonce(string $type = 'script-src'): string {
      return Csp::$plugin->policy->registerNonce($type);
    }
    
    public function config(array $config = []): void {
      foreach($config as $obj => $value){
        if (isset(Csp::$plugin->settings[$obj])){
          Csp::$plugin->settings[$obj] = $value;
        }
      }
    }
    
}
