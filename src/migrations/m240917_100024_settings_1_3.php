<?php

namespace cloudgrayau\csp\migrations;

use Craft;
use craft\db\Migration;

/**
 * m240917_100024_settings_1_3 migration.
 */
class m240917_100024_settings_1_3 extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
      
        $schemaVersion = Craft::$app->getProjectConfig()->get('plugins.csp.schemaVersion', true);
        if (version_compare($schemaVersion, '1.0.1', '<')) {
          
          $packedArray = \craft\helpers\ProjectConfig::packAssociativeArray([
            'defaultSrc' => 'default-src',
            'scriptSrc' => 'script-src',
            'scriptSrcAttr' => 'script-src-attr',
            'scriptSrcElem' => 'script-src-elem',
            'styleSrc' => 'style-src',
            'styleSrcAttr' => 'style-src-attr',
            'styleSrcElem' => 'style-src-elem',
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
          ]);
          Craft::$app->getProjectConfig()->set('plugins.csp.settings.policyOptions', $packedArray);
          
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        echo "m240917_100024_settings_1_3 cannot be reverted.\n";
        return false;
    }
}
