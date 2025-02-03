<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class RedirectController extends Controller
{
    /**
     * Redirect to login page
     */
    public function login(): RedirectResponse
    {
        return Redirect::route('login');
    }
}
