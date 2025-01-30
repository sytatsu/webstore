<?php

namespace App\Http\Livewire\Sytatsu;

use App\Services\LayoutService;
use Livewire\Component;

class SytatsuBasePage extends Component
{
    protected LayoutService $layoutService;

    protected ?string $title = null;
    protected string $appName = 'Sytatsu';
    protected string $view;
    protected string $layout = 'layouts.sytatsu-layout';

    protected array $viewAttributes = [];
    protected array $layoutAttributes = [];

    public function mount(LayoutService $layoutService): void
    {
        $this->layoutService = $layoutService;
    }

    protected function getViewAttributes(): array
    {
        return $this->viewAttributes;
    }

    protected function getLayoutAttributes(): array
    {
        return array_merge([
            'title' => $this->title,
            'appName' => $this->appName,
            'center' => true,
        ], $this->layoutAttributes);
    }

    public function render()
    {
        return $this->layoutService->render(
            view: $this->view,
            layout: $this->layout,
            viewAttributes: $this->getViewAttributes(),
            layoutAttributes: $this->getLayoutAttributes(),
        );
    }
}


