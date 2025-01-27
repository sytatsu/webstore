<?php

namespace App\Http\Controllers\Stpronk;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class WebController extends Controller
{
    /**
     * Show the application dashboard.
     */
    public function index(): Renderable
    {
        return view('stpronk.index-backup');
    }

    /**
     * Redirect to login page
     */
    public function login(): RedirectResponse
    {
        return Redirect::route('login');
    }
}
