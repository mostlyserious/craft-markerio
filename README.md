# Marker.io Plugin

Easily integrate Marker.io with CraftCMS.

Marker.io is the preferred QA tool of the [Mostly Serious](https://www.mostlyserious.io/) dev team. This plugin allows you to add their reporting widget to both the front and back end of a Craft site, making it easy for users to report issues.

## Requirements

This plugin requires Craft CMS 4.9.0 or later, and PHP 8.0.2 or later.

## Installation

You can install this plugin from the Plugin Store or with Composer.

#### From the Plugin Store

Go to the Plugin Store in your project’s Control Panel and search for “Marker.io”. Then press “Install”.

#### With Composer

Open your terminal and run the following commands:

```bash
# go to the project directory
cd /path/to/my-project.test

# tell Composer to load the plugin
composer require mostly-serious/craft-markerio

# tell Craft to install the plugin
./craft plugin/install markerio
```

## Usage

- Sign up for an account at [Marker.io](https://marker.io/) and copy your Project ID.
- Add your Project ID to the plugin settings.

That's it! You can configure the widget further using the settings provided. All config settings can be overridden with a `marker.php` in your `config` folder.

```php
<?php

use craft\helpers\App;

/* Example /config/markerio.php */

return [
    'enableWidgetFe' => true,
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

If you require advanced features such as Reporter Identification or Custom Metadata, or if you prefer to add the Marker.io widget to your templates manually, you may use the following variables in Twig templates:

```twig
{{ craft.markerio.project }}
{# The project id, ex: "369ajJu************" #}

{{ craft.markerio.markerConfig }}
{# The configuration data as an associative array, ex. ' [ 'project' => ..., '' => ... ] ' #}

{{ craft.markerio.markerConfigScript|raw }}
{# The configuration script, ex 'window.markerConfig = { ... }' including your config settings (no script tag). #}

{{ craft.markerio.widgetShim|raw }}
{# The Marker.io width shim, ex '!function(...' with no script tag. #}
```

When rendering the widget manually, be sure to disable the widget on the front end in your config settings.
```php

return [
    'enableWidgetFe' => false,
    // ...
];
```

You will also need to manage your own `requireLogin` logic when rendering the widget manually.