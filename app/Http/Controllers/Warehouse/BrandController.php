<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

class BrandController extends Controller
{

    public function list(): Factory|View|Application
    {
        return view("warehouse.brand.brand-list");
    }

    public function create(): Factory|View|Application
    {
        return view("warehouse.brand.brand-create");
    }
}
