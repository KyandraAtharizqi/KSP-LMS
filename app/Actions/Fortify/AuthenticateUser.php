<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;

class AuthenticateUser
{
    public function authenticate($request)
    {
        $user = User::where('registration_id', $request->registration_id)
                    ->orWhere('email', $request->registration_id)
                    ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                Fortify::username() => ['The provided credentials are incorrect.'],
            ]);
        }

        return $user;
    }
}
