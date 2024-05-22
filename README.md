# Marker.io Plugin

Easily integrate Marker.io with CraftCMS.

Marker.io is the preferred QA tool of the [Mostly Serious](https://www.mostlyserious.io/) dev team. This plugin allows you to add their reporting widget to both the front and back end of a Craft site, making it easy for users to report issues.

## Requirements

This plugin requires Craft CMS 4.9.0 or later, and PHP 8.0.2 or later.

## Installation

You can install this plugin from the Plugin Store or with Composer.

#### From the Plugin Store

Go to the Plugin Store in your project’s Control Panel and search for “Marker”. Then press “Install”.

#### With Composer

Open your terminal and run the following commands:

```bash
# go to the project directory
cd /path/to/my-project.test

# tell Composer to load the plugin
composer require mostly-serious/craft-marker

# tell Craft to install the plugin
./craft plugin/install marker
```

## Usage

- Sign up for an account at [Marker.io](https://marker.io/) and copy your Project ID.
- Add your Project ID to the plugin settings.

That's it! You can configure the widget further using the settings provided. All config settings can be overridden with a `marker.php` in your `config` folder.

```php
<?php

use craft\helpers\App;

/* Example /config/marker.php */

return [
    'enableWidget' => true,
    'enableWidgetCp' => true,
    'project' => App::env('YOUR_ENV_VAR_HERE'),
    'silent' => false,
    'renderDelay' => 1200,
    'keyboardShortcuts' => false,
    'useNativeScreenshot' => false,
    'extension' => false,
];
```

The plugin automatically add the Marker.io widget in the `<head>` for you.

## Variables and Advanced Configuration

The following variables are available for you to use in Twig templates:
```
{{ craft.marker.project }}

{{ craft.marker.markerConfigScript }}

{{ craft.marker.widgetShim }}
```

If you need advanced configuration for your widget such as a Reporter or Custom Data, you can use these variables to create script tags manually.