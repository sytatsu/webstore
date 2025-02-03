<?php

namespace App\Http\Livewire;

use App\Services\LayoutService;
use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class BasePage extends Component
{
    protected LayoutService $layoutService;

    protected ?string $title = null;
    protected string $appName;
    protected string $view;
    protected string $layout;

    protected array $viewAttributes = [];
    protected array $layoutAttributes = [];

    /**
     * Livewire function
     *
     * @param \App\Services\LayoutService $layoutService
     *
     * @return void
     */
    public function mount(LayoutService $layoutService): void
    {
        $this->layoutService = $layoutService;

        $this->addLayoutAttribute(value: $this->title, key: 'title')
            ->addLayoutAttribute(value: $this->appName, key: 'appName');
    }

    /**
     * Livewire function
     *
     * @return \Closure|\Illuminate\Contracts\Support\Htmlable|\Illuminate\Contracts\View\View|string
     */
    public function render(): View|Htmlable|Closure|string
    {
        return $this->layoutService->render(
            view: $this->view,
            layout: $this->layout,
            viewAttributes: $this->getViewAttributes(),
            layoutAttributes: $this->getLayoutAttributes(),
        );
    }

    /**
     * @param array $attributes
     *
     * @return $this
     */
    protected function setViewAttributes(array $attributes): self
    {
        $this->viewAttributes = $attributes;
        return $this;
    }

    protected function getViewAttributes(): array
    {
        return $this->viewAttributes;
    }

    protected function addViewAttribute(mixed $value, ?string $key): self
    {
        if ($key !== null) {
            $this->viewAttributes[$key] = $value;
            return $this;
        }

        $this->viewAttributes[] = $value;
        return $this;
    }

    protected function removeViewAttribute(string $key): self
    {
        unset($this->viewAttributes[$key]);
        return $this;
    }

    protected function setLayoutAttributes(array $attributes): self
    {
        $this->layoutAttributes = $attributes;
        return $this;
    }

    protected function getLayoutAttributes(): array
    {
        return $this->layoutAttributes;
    }

    protected function addLayoutAttribute(mixed $value, ?string $key): self
    {
        if ($key !== null) {
            $this->layoutAttributes[$key] = $value; return $this;
        }

        $this->layoutAttributes[] = $value; return $this;
    }

    protected function removeLayoutAttribute(string $key): self
    {
        unset($this->layoutAttributes[$key]);
        return $this;
    }
}
