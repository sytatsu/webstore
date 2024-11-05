<?php

namespace App\Http\Controllers\Warehouse;

use App\Enums\Actions\SaveAndAction;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class BrandController extends Controller
{

    public function list(): Factory|View|Application
    {
        return view("warehouse.brand.brand-list", [
            'brands' => Brand::with('products')->get()
        ]);
    }

    public function create(Request $request): Factory|View|Application
    {
        return view("warehouse.brand.brand-create");
    }

    public function store(Request $request): RedirectResponse
    {
        $brand = new Brand();
        $brand->name = $request->get('name');
        $brand->description = $request->get('description');
        $brand->save();

        return match ($request->get('action')) {
            SaveAndAction::CREATE_AGAIN => Redirect::route('warehouse.brands.create'),
            SaveAndAction::TO_OVERVIEW => Redirect::route('warehouse.brands.list'),
            SaveAndAction::TO_CREATED => Redirect::route('warehouse.brands.show', $brand),
            default => null
        } ?? Redirect::route('warehouse.brands.show', $brand); // Redirect fallback
    }

    public function show(Brand $brand): Factory|View|Application
    {
        return view('warehouse.brand.brand-show', [
            'brand' => $brand
        ]);
    }

    public function edit(Brand $brand): Factory|View|Application
    {
        return view('warehouse.brand.brand-edit', [
            'brand' => $brand
        ]);
    }

    public function update(Request $request, Brand $brand): RedirectResponse
    {
        $brand->name = $request->get('name');
        $brand->description = $request->get('description');
        $brand->save();

        return match ($request->get('action')) {
            SaveAndAction::CREATE_AGAIN => Redirect::route('warehouse.brands.create'),
            SaveAndAction::TO_OVERVIEW => Redirect::route('warehouse.brands.list'),
            SaveAndAction::TO_CREATED => Redirect::route('warehouse.brands.show', $brand),
            default => null
        } ?? Redirect::route('warehouse.brands.show', $brand); // Redirect fallback
    }
}
