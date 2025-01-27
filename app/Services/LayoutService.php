<?php

namespace App\Services;

class LayoutService
{
    public function __construct()
    {
        //
    }

    public function getAttributes(string $layout, ?string $title = null, ?string $appName = null, array $customAttributes = []): array
    {
        $metaAttributes = $this->getMetaAttributes(title: $title, appName: $appName);
        $layoutAttributes = $this->getDefaultAttributesForLayout(layout: $layout);

        return array_merge($metaAttributes, $layoutAttributes, $customAttributes);
    }

    protected function getDefaultAttributesForLayout(string $layout): array
    {
        return match ($layout) {
            'app' => $this->getAppLayoutDefaultAttributes(),
            'minimal' => $this->getMinimalLayoutDefaultAttributes(),
            'storefront' => $this->getStoreFrontLayoutDefaultAttributes(),
            default => abort(500, "Layout attributes for layout [{$layout}] not found")
        };
    }

    protected function getAppLayoutDefaultAttributes(): array
    {
        return [];
    }

    protected function getMinimalLayoutDefaultAttributes(): array
    {
        return [];
    }

    protected function getStoreFrontLayoutDefaultAttributes(): array
    {
        return [];
    }

    protected function getMetaAttributes(?string $title, ?string $appName): array
    {
        return [
            'title' => $this->formatTitle(title: $title, appName: $appName),
        ];
    }

    protected function formatTitle(?string $title, ?string $appName): string
    {
        if ($appName === null) {
            $appName = config('app.name');
        }

        if ($title === null) {
            return $appName;
        }

        return "{$appName} | {$title}";
    }
}
