<?php

namespace App\Http\Controllers\Catalog;

use App\Enums\Actions\SaveAndAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\Variant\VariantDeleteRequest;
use App\Http\Requests\Catalog\Variant\VariantStoreRequest;
use App\Http\Requests\Catalog\Variant\VariantUpdateRequest;
use App\Models\Variant;
use App\Services\Catalog\VariantService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class VariantController extends Controller
{
    public function __construct(
        private readonly VariantService $variantService,
    ) {
        //
    }

    public function list(): Factory|View|Application
    {
        return view(view: "catalog.variant.variant-list", data: [
            'variants' => $this->variantService->getVariants(),
        ]);
    }

    public function create(): Factory|View|Application
    {
        return view(view: "catalog.variant.variant-create");
    }

    public function store(VariantStoreRequest $request): RedirectResponse
    {
        $variant = $this->variantService->store(data: $request->validated());

        return $this->saveAndAction(
            action: $request->get('action'),
            variant: $variant,
        );
    }

    public function show(Variant $variant): Factory|View|Application
    {
        return view(view: 'catalog.variant.variant-show', data: [
            'variant' => $variant,
        ]);
    }

    public function edit(Request $request, Variant $variant): Factory|View|Application
    {
        return view(view: 'catalog.variant.variant-edit', data: [
            'variant' => $variant,
            'action' => $request->get('action'),
        ]);
    }

    public function update(VariantUpdateRequest $request): RedirectResponse
    {
        $variant = $this->variantService->findByUuid(uuid: $request->get('uuid'));

        $this->variantService->store(
            data: $request->validated(),
            variant: $variant,
        );

        return $this->saveAndAction(
            action: $request->get('action'),
            variant: $variant,
        );
    }

    public function delete(VariantDeleteRequest $request): RedirectResponse
    {
        $this->variantService->delete($request->get('uuid'));

        return Redirect::route('catalog.variants.list');
    }

    private function saveAndAction(?string $action, Variant $variant): RedirectResponse
    {
        if ($action) {
            $action = SaveAndAction::tryFromName($action);
        }

        return match ($action) {
            SaveAndAction::CREATE_AGAIN => Redirect::route('catalog.variants.create'),
            SaveAndAction::TO_OVERVIEW => Redirect::route('catalog.variants.list'),
            SaveAndAction::TO_MODEL => Redirect::route('catalog.variants.show', $variant),
            default => null
        } ?? Redirect::route('catalog.variants.show', $variant); // Redirect fallback
    }
}
