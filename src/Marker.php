<?php
/**
 * Marker plugin for Craft CMS 4.x
 *
 * Adds a Marker.io feedback widget to the environments you specify.
 *
 * @link      https://www.mostlyserious.io/
 * @copyright Copyright (c) 2022 Mostly Serious
 */

namespace mostlyserious\marker;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\elements\User;
use mostlyserious\marker\models\Settings;

use yii\base\Event;

/**
 * Class Marker
 *
 * @author    Mostly Serious
 * @package   Marker
 * @since     1.0.0
 *
 */
class Marker extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var Marker
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    /**
     * @var bool
     */
    public $hasCpSettings = false;

    /**
     * @var bool
     */
    public $hasCpSection = false;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        $app = Craft::$app;
        $info = $app->getInfo();
        $settings = $this->getSettings();

        if (!$this->isInstalled || !$settings->enabled) return;

        if (Craft::$app->request->isSiteRequest && $settings->enabled) {

            if ($settings->destination == '') {
                Craft::error('Please configure a Marker.io destination for this site. By default, the plugin looks for MARKERIO_DESTINATION to be defined in .env.', $this->handle);
                return;
            }
            
            if ($settings->requireAuth) {

                return;
            }
    

            Event::on(
                View::class,
                View::EVENT_BEFORE_RENDER_TEMPLATE,
                function (TemplateEvent $event) {
                    $settings = $this->getSettings();
                    $view = Craft::$app->getView();

                    $isDevMode = Craft::$app->config->general->devMode ? 'true' : 'false';
                    
                    $view->registerScript(".......", View::POS_HEAD, []);
                }
            );
        }
    }

    // Protected Methods
    // =========================================================================
    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }

}
