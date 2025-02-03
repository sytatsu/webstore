<?php

namespace App\Http\Livewire\Stpronk;

use App\Services\LayoutService;
use Livewire\Component;

class StpronkBasePage extends Component
{
    protected LayoutService $layoutService;

    protected ?string $title = 'Web-Magician';
    protected string $appName = 'StPronk';
    protected string $view;
    protected string $layout = 'layouts.stpronk-layout';

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


