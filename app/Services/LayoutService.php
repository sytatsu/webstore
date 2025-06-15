<?php

namespace App\Services;

class LayoutService
{
    public function __construct()
    {
        //
    }

    protected function formatTitle(?string $title, ?string $appName): string
    {
        if ($appName === null) {
            $appName = config('app.name');
        }

        if ($title === null) {
            return $appName;
        }

        return "{$appName} | " . __($title);
    }

    protected function formatSeoTitle(?string $title): string
    {
        return $title;
    }

    public function render(string $view, string $layout, array $viewAttributes = [], array $layoutAttributes = [])
    {
        $layoutAttributes['title'] = $this->formatTitle(
            title: $layoutAttributes['title'] ?? null,
            appName: $layoutAttributes['appName'] ?? null,
        );

        $layoutAttributes['seoTitle'] = $this->formatSeoTitle(
            title: $layoutAttributes['title'] ?? null,
        );

        return view($view, $viewAttributes)
            ->layout($layout, $layoutAttributes);
    }
}
