<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class WarehouseController extends Controller
{
    const INDEX_REDIRECT = 'warehouse.products.list';

    public function index(): RedirectResponse
    {
        return redirect()->route(self::INDEX_REDIRECT);
    }
}
