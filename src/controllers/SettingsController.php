<?php
namespace cloudgrayau\csp\controllers;

use cloudgrayau\csp\Csp;
use cloudgrayau\csp\models\Settings;

use Craft;
use craft\web\Controller;

use yii\web\Response;

class SettingsController extends Controller {
    
    // Public Methods
    // =========================================================================

    public function actionSettings(): Response {
        $settings = Csp::$plugin->getSettings();
        return $this->renderTemplate('csp/settings', [
            'settings' => $settings,
        ]);
    }

}