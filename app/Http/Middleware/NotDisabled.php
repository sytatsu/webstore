<?php

namespace App\Http\Middleware;

use App\Exceptions\AccountDisabledException;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;


class NotDisabled
{

    /**
     * @throws \App\Exceptions\AccountDisabledException
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()->disabled_at) {
            return Redirect::route('account-disabled');

//            $reason = $request->user()->disabled_reason;
//            Auth::logout();
//
//            // @TODO Might want to make a separate view for this with only a working logout button
//            throw new AccountDisabledException("Your account has been disabled with the reason `{$request->user()->disabled_reason}`. please contact support if you think this a mistake.");
        }

        return $next($request);
    }
}
