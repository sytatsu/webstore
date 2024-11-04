<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class StpronkController extends Controller
{
    /**
     * Show the application dashboard.
     */
    public function index(): Renderable
    {
        return view('welcome');
    }

    /**
     * Redirect to login page
     */
    public function login(): RedirectResponse
    {
        return Redirect::route('login');
    }
}
