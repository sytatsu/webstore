<?php

namespace App\Services;

use App\Mail\UserCreated;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class ProfileService
{
    public function create(array $attributes): void
    {
        $password = Str::password(
            length: 32,
            letters: true,
            numbers: true,
            symbols: false,
            spaces: false,
        );

        $user = User::create([
            'name' => $attributes['name'],
            'email' => $attributes['email'],
            'password' => Hash::make($password),
            'email_verified_at' => Carbon::now(),
        ]);

        Mail::to($user)->send(new UserCreated($user, $password));
    }

    public function update(User $user, array $attributes): void
    {
        $user->fill($attributes);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($user->isDirty('password')) {
            $user->password_updated_at = Carbon::now();
        }

        $user->save();
    }

    public function destroy(User $user, Session $session): void
    {
        if ($user->isAdmin()) {
            throw new UnprocessableEntityHttpException('Admin account can\'t be deleted');
        }

        if ($user->id === Auth::id()) {
            Auth::logout();

            $session->invalidate();
            $session->regenerateToken();
        }

        $user->delete();
    }

    public function disable(User $user, ?string $reason): void
    {
        if ($user->isAdmin()) {
            throw new UnprocessableEntityHttpException('Admin account can\'t be disabled');
        }

        $user->disabled_reason = $reason;
        $user->disabled_at = Carbon::now();
        $user->save();
    }

    public function enable(User $user): void
    {
        $user->disabled_at = null;
        $user->save();
    }

    public function resetPassword(User $user, ?string $password): void
    {
        if ($password) {
            $user->password = $password;
        }

        $user->password_updated_at = null;
        $user->save();
    }
}
