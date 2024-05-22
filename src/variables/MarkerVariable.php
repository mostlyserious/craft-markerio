<?php

namespace mostlyserious\craftmarker\variables;

use mostlyserious\craftmarker\Plugin;

class MarkerVariable
{
    public function project():string
    {
        return Plugin::getInstance()->getSettings()->getProject();
    }

    public function markerConfig(): array
    {
        return Plugin::getInstance()->markerConfig;
    }

    public function markerConfigScript(): ?string
    {
        return Plugin::getInstance()->markerConfigScript;
    }

    public function widgetShim(): string
    {
        return Plugin::WIDGET_SHIM;
    }
}
