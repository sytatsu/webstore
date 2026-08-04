<?php

namespace App\Http\Livewire\Sytatsu\Components;

use App\Filament\Pages\BarBuilderDefaultArrangementPage;
use App\Models\BarBuilderBaseColor;
use App\Models\BarBuilderCapCombo;
use App\Models\BarBuilderIcon;
use App\Models\WebstoreSetting;
use App\Services\CartService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Livewire\Attributes\Renderless;
use Livewire\Component;
use Lunar\Models\Product;

class BarBuilder extends Component
{
    /**
     * How long an in-progress design survives without further edits.
     */
    private const DRAFT_TTL_MINUTES = 60 * 24;

    private CartService $cartService;

    public Product $product;

    public function boot(CartService $cartService): void
    {
        $this->cartService = $cartService;
    }

    public function mount(Product $product): void
    {
        $this->product = $product;
    }

    public function getCatalogProperty(): array
    {
        return [
            'minCaps' => $this->capsCatalog()->min('caps'),
            'maxCaps' => $this->capsCatalog()->max('caps'),
            'caps' => $this->capsCatalog()->values()->all(),
            'baseColors' => BarBuilderBaseColor::query()->ordered()->get()
                ->map(fn (BarBuilderBaseColor $color) => [
                    'id' => $color->id,
                    'name' => $color->translate('name'),
                    'hex' => $color->hex,
                    'available' => $color->enabled,
                ])->all(),
            'capCombos' => BarBuilderCapCombo::query()->ordered()->get()
                ->map(fn (BarBuilderCapCombo $combo) => [
                    'id' => $combo->id,
                    'name' => $combo->translate('name'),
                    'cap' => $combo->cap_hex,
                    'text' => $combo->text_hex,
                    'available' => $combo->enabled,
                ])->all(),
            'icons' => BarBuilderIcon::query()->ordered()->enabled()->get()
                ->map(fn (BarBuilderIcon $icon) => [
                    'id' => $icon->id,
                    'name' => $icon->translate('name'),
                    'cx' => $icon->cx,
                    'cy' => $icon->cy,
                    'scale' => $icon->scale,
                    'path' => $icon->path,
                ])->all(),
            'defaults' => $this->buildDefaults(),
            'draft' => $this->resolveDraft(),
        ];
    }

    /**
     * Persist the customer's in-progress design so it survives a page
     * reload or a return visit. Takes priority over the admin-configured
     * defaults whenever both exist.
     *
     * The design itself is stored server-side via the cache (not the
     * framework session, whose SESSION_LIFETIME defaults to far less than
     * 24 hours), with only a small opaque token kept in a dedicated cookie.
     * A fully-loaded 10-cap design is too large to fit in a cookie's own
     * header once encrypted, so it can't be stored there directly.
     *
     * Renderless: this is a pure background persistence call and changes
     * nothing the customer can see, so it shouldn't trigger a full Livewire
     * re-render — without this, the response morph was disrupting the
     * client-rendered live preview (an x-html SVG Alpine owns, not Blade).
     */
    #[Renderless]
    public function saveDraft(array $meta): void
    {
        $token = request()->cookie($this->draftCookieName()) ?: (string) Str::uuid();

        Cache::put($this->draftCacheKey($token), $meta, now()->addMinutes(self::DRAFT_TTL_MINUTES));

        Cookie::queue(cookie(
            $this->draftCookieName(),
            $token,
            self::DRAFT_TTL_MINUTES,
        ));
    }

    private function draftCookieName(): string
    {
        return 'bar_builder_draft_' . $this->product->id;
    }

    private function draftCacheKey(string $token): string
    {
        return "bar_builder_draft:{$token}";
    }

    private function resolveDraft(): ?array
    {
        $token = request()->cookie($this->draftCookieName());

        if (!$token) {
            return null;
        }

        $meta = Cache::get($this->draftCacheKey($token));

        return is_array($meta) ? $meta : null;
    }

    /**
     * The admin-configured default word/colours/icons the builder should
     * open with, resolved against the currently enabled catalog entries.
     */
    private function buildDefaults(): array
    {
        $stored = WebstoreSetting::getByKey(BarBuilderDefaultArrangementPage::SETTING_KEY, []);

        $baseColor = BarBuilderBaseColor::query()->enabled()
            ->find($stored['base_color_id'] ?? null);

        $caps = collect($stored['caps'] ?? [])
            ->map(function (array $cap) {
                $combo = BarBuilderCapCombo::query()->enabled()
                    ->find($cap['combo_id'] ?? null);

                if (!$combo) {
                    return null;
                }

                $icon = !empty($cap['icon_id'])
                    ? BarBuilderIcon::query()->enabled()->find($cap['icon_id'])
                    : null;

                return [
                    'cap' => $combo->cap_hex,
                    'text' => $combo->text_hex,
                    'icon' => $icon ? [
                        'id' => $icon->id,
                        'name' => $icon->translate('name'),
                        'cx' => $icon->cx,
                        'cy' => $icon->cy,
                        'scale' => $icon->scale,
                        'path' => $icon->path,
                    ] : null,
                ];
            })
            ->filter()
            ->values()
            ->all();

        return [
            'word' => $stored['word'] ?? 'CLICKERZ',
            'baseColorHex' => $baseColor?->hex,
            'caps' => $caps,
        ];
    }

    /**
     * One entry per selectable cap count, backed by a real product variant + price.
     */
    private function capsCatalog(): \Illuminate\Support\Collection
    {
        return $this->product->variants
            ->map(function ($variant) {
                $caps = $variant->values->first()?->meta['caps'] ?? null;

                if (!$caps) {
                    return null;
                }

                $price = $variant->pricing()->get()->matched->price;

                return [
                    'caps' => (int) $caps,
                    'variant_id' => $variant->id,
                    'price' => $price->formatted(),
                    'price_minor' => $price->value,
                ];
            })
            ->filter()
            ->sortBy('caps');
    }

    public function addToCart(array $payload): void
    {
        $meta = $payload['meta'] ?? [];
        $caps = $meta['caps'] ?? [];
        $capsCatalog = $this->capsCatalog();

        $capsEntry = $capsCatalog->firstWhere('caps', count($caps));

        if (!$capsEntry) {
            $this->dispatch('bar-builder-error', message: __('Please choose between :min and :max caps.', [
                'min' => $capsCatalog->min('caps'),
                'max' => $capsCatalog->max('caps'),
            ]));

            return;
        }

        $baseColor = BarBuilderBaseColor::query()->enabled()
            ->where('hex', $meta['base_colour']['hex'] ?? null)
            ->first();

        if (!$baseColor) {
            $this->dispatch('bar-builder-error', message: __('That base colour is not available.'));

            return;
        }

        $enabledCombos = BarBuilderCapCombo::query()->enabled()->get();
        $enabledIconIds = BarBuilderIcon::query()->enabled()->pluck('id');

        foreach ($caps as $cap) {
            $comboMatch = $enabledCombos->first(fn (BarBuilderCapCombo $combo) => $combo->cap_hex === ($cap['colour']['hex'] ?? null)
                && $combo->text_hex === ($cap['text_colour']['hex'] ?? null));

            if (!$comboMatch) {
                $this->dispatch('bar-builder-error', message: __('One of the cap colour combinations is not available.'));

                return;
            }

            if (!empty($cap['icon']) && !$enabledIconIds->contains($cap['icon']['id'] ?? null)) {
                $this->dispatch('bar-builder-error', message: __('One of the selected icons is not available.'));

                return;
            }
        }

        $variant = $this->product->variants->firstWhere('id', $capsEntry['variant_id']);

        $this->cartService->addLine($variant, 1, [
            'bar_builder' => [
                'text' => $meta['text'] ?? '',
                'reference' => $meta['reference'] ?? '',
                'base_colour' => $meta['base_colour'] ?? null,
                'caps' => $caps,
            ],
        ]);

        $this->dispatch('cart-updated');
        $this->dispatch('add-to-cart');
        $this->dispatch('bar-builder-added', reference: $meta['reference'] ?? '');
    }

    public function render(): View|Factory|Application
    {
        return view('sytatsu.components.livewire.bar-builder', [
            'catalog' => $this->catalog,
        ]);
    }
}
