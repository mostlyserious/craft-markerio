<?php

namespace mostlyserious\craftmarker;

use Craft;
use craft\web\View;
use yii\base\Event;
use craft\base\Model;
use craft\helpers\App;
use craft\base\Plugin as BasePlugin;
use craft\web\twig\variables\CraftVariable;
use mostlyserious\craftmarker\models\Settings;
use mostlyserious\craftmarker\variables\MarkerVariable;

/**
 * Marker plugin
 *
 * @method static Plugin   getInstance()
 * @method        Settings getSettings()
 * @author Mostly Serious
 * @copyright Mostly Serious
 * @license https://craftcms.github.io/license/ Craft License
 */
class Plugin extends BasePlugin
{
    public string $schemaVersion = '1.0.0';
    public bool $hasCpSettings = true;

    public array $markerConfig = [];
    public string $markerConfigScript = '';

    public const WIDGET_SHIM = '!function(e,r,a){if(!e.__Marker){e.__Marker={};var t=[],n={__cs:t};["show","hide","isVisible","capture","cancelCapture","unload","reload","isExtensionInstalled","setReporter","setCustomData","on","off"].forEach(function(e){n[e]=function(){var r=Array.prototype.slice.call(arguments);r.unshift(e),t.push(r)}}),e.Marker=n;var s=r.createElement("script");s.async=1,s.src="https://edge.marker.io/latest/shim.js";var i=r.getElementsByTagName("script")[0];i.parentNode.insertBefore(s,i)}}(window,document);';

    public static function config(): array
    {
        return [
            'components' => [],
        ];
    }

    public function init(): void
    {
        parent::init();
        
        $this->markerConfig = $this->createMarkerConfig();
        $this->markerConfigScript = $this->createMarkerConfigScript($this->markerConfig);

        Craft::$app->onInit(function () {
            $this->attachEventHandlers();
        });
    }

    protected function createSettingsModel(): ?Model
    {
        return Craft::createObject(Settings::class);
    }

    protected function settingsHtml(): ?string
    {
        $overrides = Craft::$app->getConfig()->getConfigFromFile(strtolower($this->handle));

        return Craft::$app->view->renderTemplate('marker/_settings.twig', [
            'plugin' => $this,
            'settings' => $this->getSettings(),
            'overrides' => array_keys($overrides),
        ]);
    }

    private function attachEventHandlers(): void
    {
        /** @var \mostlyserious\craftmarker\models\Settings $settings */
        $settings = $this->getSettings();

        if (
            (
                !Craft::$app->request->getIsCpRequest()
                && $settings->enableWidget
            ) || (
                $settings->enableWidgetCp
                && Craft::$app->request->getIsCpRequest()
            )
        ) {
            Event::on(
                View::class,
                View::EVENT_BEFORE_RENDER_TEMPLATE,
                function (): void {
                    Craft::$app->view->registerLinkTag([
                        'rel'=>'preload',
                        'href'=>'https://edge.marker.io/latest/shim.js',
                        'as' => 'script',
                    ]);
                    Craft::$app->view->registerJs($this->markerConfigScript, View::POS_HEAD);
                    Craft::$app->view->registerJs($this::WIDGET_SHIM, View::POS_HEAD);
                }
            );
        }

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('marker', MarkerVariable::class);
            }
        );
    }

    private function createMarkerConfig(): array
    {
        /** @var \mostlyserious\craftmarker\models\Settings $settings */
        $settings = $this->getSettings();

        return [
            'project' => (string) $settings->getProject(),
            'source' => 'snippet',
            'silent' => (bool) $settings->silent,
            'renderDelay' => (int) $settings->renderDelay,
            'keyboardShortcuts' => (bool) $settings->keyboardShortcuts,
            'useNativeScreenshot' => (bool) $settings->useNativeScreenshot,
            'extension' => (bool) $settings->extension
        ];
    }

    private function createMarkerConfigScript($config): ?string
    {
        $encoded = json_encode($config);

        if ($encoded === false) {
            $errorMessage = Craft::t('marker', 'There is an error with your Marker.io configuration. Please check the values.');

            if (App::devMode()) {
                throw new \Exception($errorMessage);
            }

            Craft::error($errorMessage, __METHOD__);

            return null;
        }

        return 'window.markerConfig = ' . json_encode($config) . ';';
    }
}
