<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {
        return redirect('warehouse/products');
    }

    public function brands(): Factory|View|Application
    {
        return view("warehouse.brand.brand-list");
    }

    public function categories(): Factory|View|Application
    {
        return view("warehouse.category.category-list");
    }

    public function variants(): Factory|View|Application
    {
        return view("warehouse.variant.variant-list");
    }
}
