<?php
namespace cloudgrayau\csp\services;

use cloudgrayau\csp\Csp;

use Craft;
use craft\base\Component;
use craft\helpers\StringHelper;
use craft\helpers\UrlHelper;

class PolicyService extends Component {
    
    private array $cspData = [];
    private array $nonceData = [];
    
    // Public Methods
    // =========================================================================
    
    public function applyHeaders() {
        $settings = Csp::$plugin->settings;
        if ($settings->protectionEnabled){
            foreach($settings->headerProtection as $header){
                if ((!empty($header[0])) && (!empty($header[1]))){
                    Craft::$app->getResponse()->getHeaders()->set(trim($header[0]), trim($header[1]));
                }
            }
        }
    }
    
    public function cspLogic() {
        $settings = Csp::$plugin->settings;
        $cspOptions = $settings->cspOptions;
        if ($settings->cspMode == 'tag'){ /* disable unsupported meta tag options */
            $cspOptions = array();
            foreach($settings->cspOptions as $option){
                switch($option){
                    case 'reportUri':
                    case 'frameAncestors':
                    case 'sandbox':
                        break;
                    default:
                        $cspOptions[] = $option;
                        break;
                }
            }
        }
        
        /* deal with settings */
        foreach($cspOptions as $option){
            $cspkey = $settings->policyOptions[$option];
            foreach($settings[$option] as $row){
                if (isset($row[0])){
                    if (!empty(trim($row[0]))){
                        $data = explode(' ', trim($row[0]));
                        foreach($data as $csp){
                            $this->cspData[$cspkey][] = $csp;
                        }
                    }
                }
            }
        }
        
        /* deal with created nonces */
        foreach($this->nonceData as $cspkey => $nonces){
            foreach($nonces as $nonce){
                $csp = "'nonce-".trim($nonce)."'";
                if (!in_array($csp, $this->cspData[$cspkey])){
                    $this->cspData[$cspkey][] = $csp;
                }
            }
        }
        
        /* deal with SEOMatic */
        if (class_exists('\nystudio107\seomatic\helpers\DynamicMeta')){
            $cspNonces = \nystudio107\seomatic\helpers\DynamicMeta::getCspNonces();
            foreach($cspNonces as $row){
                if (!empty(trim($row))){
                    $data = explode(' ', trim($row));
                    foreach($data as $csp){  
                        if (isset($this->cspData['script-src'])){
                            if (!in_array($csp, $this->cspData['script-src'])){
                                $this->cspData['script-src'][] = $csp;
                            }
                        } else {
                            $this->cspData['script-src'][] = $csp;
                        }
                    }
                }
            }
        }
    }
    
    public function renderCsp() {
        $settings = Csp::$plugin->settings;
        $name = ($settings->cspMode == 'report') ? 'Content-Security-Policy-Report-Only' : 'Content-Security-Policy';
        $value = '';
        foreach ($this->cspData as $csp => $data){
            $value .= $csp.' '.implode(' ', $data).'; ';
        }
        if ($settings->cspMode == 'tag'){
            Craft::$app->getView()->registerMetaTag([
                'content' => trim($value),
                'http-equiv' => $name
            ]);
        } else {
            Craft::$app->getResponse()->getHeaders()->add($name, trim($value));
        }
    }
    
    public function registerNonce(string $type = 'script-src') {
        $settings = Csp::$plugin->settings;
        $nonce = base64_encode(StringHelper::randomString());
        if (in_array($type, $settings->policyOptions)){
            $this->nonceData[$type][] = $nonce;
        }
        return $nonce;
    }

}
