<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class CatalogController extends Controller
{
    const INDEX_REDIRECT = 'catalog.products.list';

    public function index(): RedirectResponse
    {
        return redirect()->route(self::INDEX_REDIRECT);
    }
}
