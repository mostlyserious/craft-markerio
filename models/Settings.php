<?php

namespace mostlyserious\marker\models;

use craft\base\Model;

class Settings extends Model
{
    // MARKERIO_DESTINATION
    // MARKERIO_ENABLED
    // MARKERIO_REQUIRE_AUTH

    public $enabled = false;
    public $requireAuth = false; // shows the widget for logged in users only
    public $destination = '';

    public function rules()
    {
        return [
            [['enabled', 'requireAuth'], 'boolean'],
            [['destination'], 'string'],
        ];
    }
}
