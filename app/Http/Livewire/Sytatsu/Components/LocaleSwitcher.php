<?php

namespace App\Http\Livewire\Sytatsu\Components;

use App\Support\LocaleAwareUrlGenerator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\App;
use Livewire\Component;

class LocaleSwitcher extends Component
{
    const IMAGE_PATH = 'resources/images/countries/rounded';

    public array $supportedLocales = [];
    public array $activeLocale = [];

    public function mount(): void
    {
        $this->supportedLocales = $this->getLocales();
        $this->activeLocale = $this->supportedLocales[App::currentLocale()];
        $this->activeLocale['locale'] = App::currentLocale();
    }

    /**
     * Switching locale isn't just re-rendering the same URL: nl and en are separate,
     * prefixed routes (see routes/sytatsu.php), so we resolve the page the visitor is
     * currently on (from the Referer header, since this Livewire call itself hits the
     * "/livewire/update" endpoint, not the page) and regenerate its URL for the target
     * locale. Falls back to a plain redirect if that can't be resolved for any reason.
     */
    public function switchLocale(string $locale): Redirector
    {
        session(['locale' => $locale]);

        return redirect($this->resolveTargetUrl($locale) ?? request()->header('Referer') ?? '/');
    }

    protected function resolveTargetUrl(string $locale): ?string
    {
        $referer = request()->header('Referer');

        if (! $referer) {
            return null;
        }

        $path = parse_url($referer, PHP_URL_PATH) ?: '/';

        try {
            $route = app('router')->getRoutes()->match(Request::create($path, 'GET'));
        } catch (\Throwable) {
            return null;
        }

        $routeName = $route->getName();

        if (! $routeName) {
            return null;
        }

        $baseName = LocaleAwareUrlGenerator::baseRouteName($routeName);
        $targetName = $locale === 'en' ? LocaleAwareUrlGenerator::englishRouteName($baseName) : $baseName;

        if (! app('router')->getRoutes()->hasNamedRoute($targetName)) {
            return null;
        }

        $previousLocale = App::getLocale();
        App::setLocale($locale);
        $url = route($targetName, $route->parameters());
        App::setLocale($previousLocale);

        return $url;
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
        return view('sytatsu.components.livewire.locale-switcher', [
            'supportedLocales' => $this->supportedLocales,
            'activeLocale' => $this->activeLocale
        ]);
    }
}
