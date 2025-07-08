<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function create(array $input)
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'email', 'max:255', Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
            'registration_id' => ['required', 'string', 'unique:users,registration_id'],
            'phone' => ['nullable', 'string'],
            'jabatan_id' => ['nullable', 'exists:jabatans,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'superior_registration_id' => ['nullable', 'exists:users,registration_id'],
            'nik' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
        ])->validate();

        $superiorId = null;
        if (!empty($input['superior_registration_id'])) {
            $superior = User::where('registration_id', $input['superior_registration_id'])->first();
            if ($superior) {
                $superiorId = $superior->id;
            }
        }

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'registration_id' => $input['registration_id'],
            'phone' => $input['phone'] ?? null,
            'jabatan_id' => $input['jabatan_id'] ?? null,
            'department_id' => $input['department_id'] ?? null,
            'superior_id' => $superiorId,
            'nik' => $input['nik'] ?? null,
            'address' => $input['address'] ?? null,
        ]);
    }
}
