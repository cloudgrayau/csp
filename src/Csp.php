<?php
namespace cloudgrayau\csp;

use cloudgrayau\csp\models\Settings;
use cloudgrayau\csp\controllers\SettingsController;
use cloudgrayau\csp\services\PolicyService;
use cloudgrayau\csp\variables\CspVariable;
use cloudgrayau\csp\twigextensions\CspTwigExtension;
use cloudgrayau\utils\UtilityHelper;

use Craft;
use craft\base\Plugin;
use craft\elements\User;
use craft\events\RegisterUrlRulesEvent;
use craft\helpers\UrlHelper;
use craft\web\UrlManager;
use craft\web\Application;
use craft\web\View;
use craft\web\twig\variables\CraftVariable;

use yii\base\Event;

class Csp extends Plugin {

   public static $plugin;
   public string $schemaVersion = '1.0.0';
   public bool $hasCpSettings = true;
   public bool $hasCpSection = false;

   // Public Methods
   // =========================================================================

   public function init(): void {
      parent::init();
      self::$plugin = $this;
         
      $this->_registerComponents();
      $this->_registerVariables();
      $this->_registerTwigExtensions();
      $this->_registerSettings();
         
      if (Craft::$app->getRequest()->getIsConsoleRequest()) {
         return;
      }
      $this->policy->applyHeaders();
      if (Craft::$app->getRequest()->getIsSiteRequest()) {
         $this->_registerCSP();
      } elseif (Craft::$app->getRequest()->getIsCpRequest()) {
         $this->_registerCpUrlRules();
      }
   }
   
   public function getSettingsResponse(): mixed {
      return Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('csp/settings'));
   }
   
   public static function config(): array {
      return [
         'components' => [
            'policy' => ['class' => PolicyService::class]
         ],
      ];
    }
   
   // Private Methods
   // =========================================================================
   
   private function _registerComponents(): void {
      UtilityHelper::registerModule();
   }
   
   private function _registerVariables(): void {
      Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function(Event $e) {
         $variable = $e->sender;
         $variable->set('csp', CspVariable::class);
      });
   }
   
   private function _registerTwigExtensions(): void {
      Craft::$app->getView()->registerTwigExtension(new CspTwigExtension());
   }
   
   private function _registerCSP(): void {
      $user = Craft::$app->getUser()->getIdentity(); /* Disable CSP when debugToolBar is enabled */
      if ($user && $user->getPreference('enableDebugToolbarForSite')) {
         Event::on(View::class, View::EVENT_AFTER_RENDER_PAGE_TEMPLATE, function (Event $e) {
            Craft::$app->getResponse()->getHeaders()->remove('Content-Security-Policy');
            Craft::$app->getResponse()->getHeaders()->remove('X-Content-Security-Policy');
            Craft::$app->getResponse()->getHeaders()->remove('X-Webkit-Csp');
         });
         return;
      }
      if ($this->settings->cspEnabled){
         Event::on(View::class, View::EVENT_END_PAGE, function (Event $e) {
            $this->policy->parseCsp();
            if ($this->settings->cspMode == 'tag'){
               $this->policy->renderCsp();
            }
         });
         Event::on(View::class, View::EVENT_AFTER_RENDER_PAGE_TEMPLATE, function (Event $e) {
            Craft::$app->getResponse()->getHeaders()->remove('Content-Security-Policy'); /* Remove all existing CSP headers */
            Craft::$app->getResponse()->getHeaders()->remove('X-Content-Security-Policy');
            Craft::$app->getResponse()->getHeaders()->remove('X-Webkit-Csp');
            if ($this->settings->cspMode == 'header' || $this->settings->cspMode == 'report'){
               $this->policy->renderCsp();
            }
         });
      }
   }
   
   private function _registerCpUrlRules(): void {
      Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function(RegisterUrlRulesEvent $event) {
         $event->rules += [
            'csp/settings' => 'csp/settings/settings'
         ];
      });
   }
   
  private function _registerSettings(): void {
    $cspOptions = $this->settings->cspOptions;
    foreach($cspOptions as $option){
      $settings = Craft::$app->config->getConfigFromFile('csp');
      if (isset($settings[$option])){
        $policy = array();
        foreach($settings[$option] as $row){
          $row = (array)$row;
          if (isset($row[0])){
            if (!empty(trim($row[0]))){
              $policy[] = array(trim($row[0]));
            }
          }
        }
        $this->settings[$option] = $policy;
      }
    }
  }

   // Protected Methods
   // =========================================================================

   protected function createSettingsModel(): Settings {
      return new Settings();
   }
   
}
