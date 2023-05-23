<?php
namespace cloudgrayau\csp\twigextensions;

use cloudgrayau\csp\Csp;

use craft\helpers\ArrayHelper;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

class CspTwigExtension extends AbstractExtension {
    
    // Public Methods
    // =========================================================================
    
    public function getName(): string {
        return 'Content Security Policy';
    }

    public function getFunctions(): array {
        return [
            new TwigFunction('csp', [$this, 'registerNonce']),
        ];
    }

    public function registerNonce(string $type = 'script-src'): string {
        return Csp::$plugin->policy->registerNonce($type);
    }
    
}
