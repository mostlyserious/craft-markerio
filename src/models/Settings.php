<?php

namespace mostlyserious\craftmarker\models;

use craft\base\Model;
use craft\helpers\App;

/**
 * Marker settings
 */
class Settings extends Model
{
    public $enableWidget = true;
    public $enableWidgetCp = false;
    public $project = '';
    public $silent = false;
    public $renderDelay = 0;
    public $keyboardShortcuts = true;
    public $useNativeScreenshot = true;
    public $extension = false;

    public function defineRules(): array
    {
        return [
            [['project'], 'required'],
            // ...
        ];
    }

    public function getProject(): string
    {
        return App::parseEnv($this->project);
    }
}
