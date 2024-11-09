<?php

namespace App\Http\Controllers\Warehouse;

use App\Enums\Actions\SaveAndAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Warehouse\BrandStoreRequest;
use App\Http\Requests\Warehouse\BrandUpdateRequest;
use App\Models\Brand;
use App\Services\Warehouse\BrandService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class BrandController extends Controller
{
    public function __construct(
        private readonly BrandService $brandService,
    ) {
        //
    }

    public function list(): Factory|View|Application
    {
        return view(view: "warehouse.brand.brand-list", data: [
            'brands' => $this->brandService->allList(),
        ]);
    }

    public function create(): Factory|View|Application
    {
        return view(view: "warehouse.brand.brand-create");
    }

    public function store(BrandStoreRequest $request): RedirectResponse
    {
        $brand = $this->brandService->store(data: $request->validated());

        return $this->saveAndAction(
            action: $request->get('action'),
            brand: $brand,
        );
    }

    public function show(Brand $brand): Factory|View|Application
    {
        return view(view: 'warehouse.brand.brand-show', data: [
            'brand' => $brand,
        ]);
    }

    public function edit(Request $request, Brand $brand): Factory|View|Application
    {
        return view(view: 'warehouse.brand.brand-edit', data: [
            'brand' => $brand,
            'action' => $request->get('action'),
        ]);
    }

    public function update(BrandUpdateRequest $request): RedirectResponse
    {
        $brand = $this->brandService->findByUuid(uuid: $request->get('uuid'));

        $this->brandService->store(
            data: $request->validated(),
            brand: $brand,
        );

        return $this->saveAndAction(
            action: $request->get('action'),
            brand: $brand,
        );
    }

    public function delete(Request $request): RedirectResponse
    {
        $this->brandService->delete($request->get('uuid'));

        return Redirect::route('warehouse.brands.list');
    }

    private function saveAndAction(?string $action, Brand $brand): RedirectResponse
    {
        $action = SaveAndAction::tryFromName($action);

        return match ($action) {
            SaveAndAction::CREATE_AGAIN => Redirect::route('warehouse.brands.create'),
            SaveAndAction::TO_OVERVIEW => Redirect::route('warehouse.brands.list'),
            SaveAndAction::TO_MODEL => Redirect::route('warehouse.brands.show', $brand),
            default => null
        } ?? Redirect::route('warehouse.brands.show', $brand); // Redirect fallback
    }
}
