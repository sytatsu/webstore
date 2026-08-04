<?php

namespace App\Http\Livewire;

use App\Services\LayoutService;
use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class BasePage extends Component
{
    protected ?string $title = null;
    protected ?string $description = null;
    protected ?string $image = null;
    protected string $appName;
    protected string $view;
    protected string $layout;

    protected array $viewAttributes = [];
    protected array $layoutAttributes = [];

    /**
     * Livewire function
     *
     * @return \Closure|\Illuminate\Contracts\Support\Htmlable|\Illuminate\Contracts\View\View|string
     */
    public function render(): View|Htmlable|Closure|string
    {
        $this->addLayoutAttribute(value: $this->getTitle(), key: 'title')
            ->addLayoutAttribute(value: $this->getAppName(), key: 'appName')
            ->addLayoutAttribute(value: $this->getDescription() !== null ? __($this->getDescription()) : null, key: 'description')
            ->addLayoutAttribute(value: $this->getImage(), key: 'image');

        return app(LayoutService::class)->render(
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

    protected function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    protected function getTitle(): ?string
    {
        return $this->title;
    }

    protected function setDescription(?string $description): self
    {
        $this->description = $description ?: null;
        return $this;
    }

    protected function getDescription(): ?string
    {
        return $this->description;
    }

    protected function setImage(?string $image): self
    {
        $this->image = $image ?: null;
        return $this;
    }

    protected function getImage(): ?string
    {
        return $this->image;
    }

    protected function setAppName(string $appName): self
    {
        $this->appName = $appName;
        return $this;
    }

    protected function getAppName(): ?string
    {
        return $this->appName;
    }
}
