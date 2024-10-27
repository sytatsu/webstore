<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class AccountDisabledController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function index(Request $request): RedirectResponse|View
    {
        if (!$request->user()->disabled_at) {
            return Redirect::route('dashboard');
        }

        return view('auth.account-disabled', [
            'user' => $request->user(),
        ]);
    }
}
