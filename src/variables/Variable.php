<?php

namespace mostlyserious\craftmarkerio\variables;

use mostlyserious\craftmarkerio\Plugin;

class Variable
{
    public function project(): string
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
