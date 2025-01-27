<?php

namespace App\Http\Livewire\Sytatsu\Components;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\App;
use Livewire\Component;

class LocaleSwitcher extends Component
{
    const IMAGE_PATH = '/images/countries/rounded';

    public array $supportedLocales = [];
    public array $activeLocale = [];

    public function mount(): void
    {
        $this->supportedLocales = $this->getLocales();
        $this->activeLocale = $this->supportedLocales[App::currentLocale()];
        $this->activeLocale['locale'] = App::currentLocale();
    }

    public function switchLocale(string $locale): Redirector
    {
        session(['locale' => $locale]);
        return redirect(request()->header('Referer'));
    }

    private function getLocales(): array
    {
        $localeSwitcherConfig = config('locale-switcher.sytatsu');

        return collect($localeSwitcherConfig)->mapWithKeys(function ($array) {
            return [$array['locale'] => [
                'image' => self::IMAGE_PATH . "/{$array['image']}",
                'tooltip' => $array['tooltip']
            ]];
        })->toArray();
    }

    public function render(): Application|Factory|View
    {
        return view('sytatsu.components.locale-switcher', [
            'supportedLocales' => $this->supportedLocales,
            'activeLocale' => $this->activeLocale
        ]);
    }
}
