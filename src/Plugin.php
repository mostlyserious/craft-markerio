<?php

namespace mostlyserious\craftmarkerio;

use Craft;
use craft\web\View;
use yii\base\Event;
use craft\base\Model;
use craft\helpers\App;
use craft\base\Plugin as BasePlugin;
use craft\web\twig\variables\CraftVariable;
use mostlyserious\craftmarkerio\models\Settings;
use mostlyserious\craftmarkerio\variables\Variable;

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

    public array $widgetConfig = [];
    public string $widgetConfigScript = '';
    public string $project = '';

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

        $this->project = $this->settings->getProject();
        $this->widgetConfig = $this->createWidgetConfig();
        $this->widgetConfigScript = $this->createWidgetConfigScript($this->widgetConfig);

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

        return Craft::$app->view->renderTemplate('markerio/_settings.twig', [
            'plugin' => $this,
            'settings' => $this->settings,
            'overrides' => array_keys($overrides),
        ]);
    }

    private function attachEventHandlers(): void
    {
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('markerio', Variable::class);
            }
        );

        /** A project is required to add the widget script to the page. */
        if ($this->project === '') {
            Craft::info('No Marker.io project configured.', __METHOD__);
            return;
        }

        /** @var \craft\elements\User|null $currentUser */
        $currentUser = Craft::$app->getUser()->getIdentity();

        if (!$currentUser && $this->settings->requireLogin) {
            return;
        }

        if (
            (
                !Craft::$app->request->getIsCpRequest()
                && $this->settings->enableWidgetFe
            ) || (
                Craft::$app->request->getIsCpRequest()
                && $this->settings->enableWidgetCp
            )
        ) {
            Event::on(
                View::class,
                View::EVENT_BEFORE_RENDER_TEMPLATE,
                function (): void {
                    Craft::$app->view->registerLinkTag([
                        'rel' => 'preload',
                        'href' => 'https://edge.marker.io/latest/shim.js',
                        'as' => 'script',
                    ]);
                    Craft::$app->view->registerJs($this->widgetConfigScript, View::POS_HEAD);
                    Craft::$app->view->registerJs($this::WIDGET_SHIM, View::POS_HEAD);
                }
            );
        }
    }

    private function createWidgetConfig(): array
    {
        return [
            'project' => (string) $this->project,
            'source' => 'snippet',
            'silent' => (bool) $this->settings->silent,
            'renderDelay' => (int) $this->settings->renderDelay,
            'keyboardShortcuts' => (bool) $this->settings->keyboardShortcuts,
            'useNativeScreenshot' => (bool) $this->settings->useNativeScreenshot,
            'extension' => (bool) $this->settings->extension,
        ];
    }

    private function createWidgetConfigScript($config): ?string
    {
        $encoded = json_encode($config);

        if ($encoded === false) {
            $errorMessage = Craft::t('markerio', 'There is an error with your Marker.io configuration. Please check the values.');

            if (App::devMode()) {
                throw new \Exception($errorMessage);
            }

            Craft::error($errorMessage, __METHOD__);

            return null;
        }

        return 'window.markerConfig = ' . json_encode($config) . ';';
    }
}
