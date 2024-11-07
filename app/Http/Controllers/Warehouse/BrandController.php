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
        private BrandService $brandService,
    ) {
        //
    }

    public function list(): Factory|View|Application
    {
        return view(view: "warehouse.brand.brand-list", data: [
            'brands' => Brand::with(relations: 'products')->get()
        ]);
    }

    public function create(): Factory|View|Application
    {
        return view(view: "warehouse.brand.brand-create");
    }

    public function store(BrandStoreRequest $request): RedirectResponse
    {
        $brand = $this->brandService->store(data: $request->validated());

        return match ($request->get('action')) {
            SaveAndAction::CREATE_AGAIN->name => Redirect::route(route: 'warehouse.brands.create'),
            SaveAndAction::TO_OVERVIEW->name => Redirect::route(route: 'warehouse.brands.list'),
            SaveAndAction::TO_MODEL->name => Redirect::route(route: 'warehouse.brands.show', parameters: $brand),
            default => null
        } ?? Redirect::route('warehouse.brands.show', $brand); // Redirect fallback
    }

    public function show(Brand $brand): Factory|View|Application
    {
        return view('warehouse.brand.brand-show', [
            'brand' => $brand,
        ]);
    }

    public function edit(Request $request, Brand $brand): Factory|View|Application
    {
        return view('warehouse.brand.brand-edit', [
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

        return match ($request->get('action')) {
            SaveAndAction::CREATE_AGAIN->name => Redirect::route('warehouse.brands.create'),
            SaveAndAction::TO_OVERVIEW->name => Redirect::route('warehouse.brands.list'),
            SaveAndAction::TO_MODEL->name => Redirect::route('warehouse.brands.show', $brand),
            default => null
        } ?? Redirect::route('warehouse.brands.show', $brand); // Redirect fallback
    }
}
