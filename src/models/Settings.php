<?php

namespace mostlyserious\craftmarkerio\models;

use craft\base\Model;
use craft\helpers\App;

/**
 * Marker settings
 */
class Settings extends Model
{
    public $enableWidgetFe = true;
    public $enableWidgetCp = false;
    public $requireLogin = false;
    public $project = '';
    public $silent = false;
    public $renderDelay = 0;
    public $keyboardShortcuts = true;
    public $useNativeScreenshot = false;
    public $extension = false;

    public function defineRules(): array
    {
        return [
            [['project'], 'required'],
        ];
    }

    public function getProject(): string
    {
        return strval(App::parseEnv($this->project));
    }
}
