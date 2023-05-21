<?php
namespace cloudgrayau\csp\twigextensions;

use cloudgrayau\csp\Csp;

use craft\helpers\ArrayHelper;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

class CspTwigExtension extends AbstractExtension {
    
    // Public Methods
    // =========================================================================
    
    public function getName() {
        return 'Content Security Policy';
    }

    public function getFunctions() {
        return [
            new TwigFunction('csp', [$this, 'registerNonce']),
        ];
    }

    public function registerNonce(string $type = 'script-src') {
        return Csp::$plugin->policy->registerNonce($type);
    }
    
}
