<?php
namespace cloudgrayau\csp;

use cloudgrayau\csp\middleware\{PolicyMiddleware, ProtectionMiddleware};
use cloudgrayau\csp\models\Settings;
use cloudgrayau\csp\services\PolicyService;
use cloudgrayau\csp\twigextensions\CspTwigExtension;
use cloudgrayau\csp\variables\CspVariable;
use cloudgrayau\utils\UtilityHelper;

use CraftCms\Cms\Ui\{Ui, UiContext};
use CraftCms\Cms\Ui\Controls\{Choice, Lightswitch, Table, Text};
use CraftCms\Cms\Ui\Nodes\{Field, Separator, Tab};
use CraftCms\Cms\Plugin\{Plugin, PluginSettings};
use CraftCms\Cms\Support\Facades\Twig;
use CraftCms\Cms\Twig\Variables\CraftVariable;

use function CraftCms\Cms\t;

class Csp extends Plugin {

  public bool $hasCpSettings = true;
  public bool $hasCpSection = false;

  public function boot(): void {
    $this->app->scoped(PolicyService::class); // one instance per request, shared by templates and middleware
    $router = $this->app['router'];
    $router->prependMiddlewareToGroup('craft', ProtectionMiddleware::class);   // VERIFY group name
    $router->prependMiddlewareToGroup('craft.web', PolicyMiddleware::class);   // VERIFY covers entry/template requests
    Twig::registerExtension(new CspTwigExtension());
    CraftVariable::macro('csp', fn () => new CspVariable());
  }

  #[\Override]
  protected static function createSettings(): PluginSettings {
    return new Settings();
  }

  public function settingsUi(UiContext $context = new UiContext): ?Ui {
    $settings = $this->getSettings();
    $cspEnabled = (bool) $this->current($context, 'cspEnabled', $settings->cspEnabled);
    $protectionEnabled = (bool) $this->current($context, 'protectionEnabled', $settings->protectionEnabled);
    $options = (array) $this->current($context, 'cspOptions', $settings->cspOptions);
  
    $tabs = [];
    
    $tabs[] = Tab::make('settings', t('Settings', category: 'csp'), [
      Field::make(t('Enable Content Security Policy', category: 'csp'), Lightswitch::make('cspEnabled')->reactive())
        ->instructions(t('A [Content Security Policy](https://content-security-policy.com/) helps you reduce XSS risks on modern browsers by declaring which dynamic resources are allowed to load.', category: 'csp'))
        ->warning(UtilityHelper::configWarning($this->handle, 'cspEnabled')),
      Field::make(t('Enable Header Protection', category: 'csp'), Lightswitch::make('protectionEnabled')->reactive())
        ->instructions(t('Additional HTTP Response Headers that provide security.', category: 'csp'))
        ->warning(UtilityHelper::configWarning($this->handle, 'protectionEnabled')),
      Separator::make('csp-separator'),
      Field::make(t('CSP Mode', category: 'csp'), Choice::make('cspMode')->options(array_map(
        fn ($value, $label) => ['value' => $value, 'label' => $label],
        array_keys(Settings::MODE_OPTIONS), Settings::MODE_OPTIONS
      )))
        ->required()
        ->warning(UtilityHelper::configWarning($this->handle, 'cspMode')),
      Field::make(t('CSP Options', category: 'csp'), Choice::make('cspOptions')->multiple()->reactive()->options(array_map(
        fn ($value, $label) => ['value' => $value, 'label' => $label],
        array_keys(Settings::POLICY_OPTIONS), Settings::POLICY_OPTIONS
      )))
        ->warning(UtilityHelper::configWarning($this->handle, 'cspOptions')),
      UtilityHelper::footer(),
    ]);
    
    if ($cspEnabled) {
      $tabs[] = Tab::make('policy', t('Policy', category: 'csp'), [
        ...$this->policyNodes($options),
        UtilityHelper::footer('cloudgray-footer-policy'),
      ]);
    }
    if ($protectionEnabled) {
      $tabs[] = Tab::make('protection', t('Protection', category: 'csp'), [
        Field::make(t('HTTP Response Headers', category: 'csp'), Table::make('headerProtection')
          ->columns([
            0 => ['heading' => t('Header', category: 'csp'), 'width' => '25%', 'control' => Text::make('0')->monospace()],
            1 => ['heading' => t('Value', category: 'csp'), 'width' => '75%', 'control' => Text::make('1')->monospace()],
          ])
          ->allowAdd()->allowDelete()->allowReorder()
          ->addRowLabel(t('Add a header', category: 'csp')))
          ->warning(UtilityHelper::configWarning($this->handle, 'headerProtection')),
          UtilityHelper::footer('cloudgray-footer-protection'),
      ]);
    }
    return Ui::make($tabs);
  }
  
  private function policyNodes(array $options): array {
    $nodes = [];
    foreach ($options as $option) {
      if (!isset(Settings::POLICY_OPTIONS[$option])) {
        continue;
      }
      $nodes[] = Field::make(Settings::POLICY_OPTIONS[$option], Table::make($option)
        ->columns([0 => ['heading' => '', 'width' => '100%', 'control' => Text::make('0')->monospace()]])
        ->allowAdd()->allowDelete()->allowReorder()
        ->addRowLabel(t('Add a policy', category: 'csp')))
        ->warning(UtilityHelper::configWarning($this->handle, $option));
    }
    return $nodes;
  }
  
  /** In-flight form value when the form is refreshing, otherwise the saved setting. */
  private function current(UiContext $context, string $key, mixed $saved): mixed {
    return $context->values['settings'][$key] ?? $context->values[$key] ?? $saved;
  }

}