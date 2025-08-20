<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $authUser = auth()->user();

        if ($authUser->role === 'ADMIN') {
            return true;
        }

        if ($this->id == $authUser->id) {
            return true;
        }

        $targetUser = \App\Models\User::find($this->id);
        if (!$targetUser) {
            return false;
        }

        if (
            $authUser->role === 'DEPARTMENT_ADMIN' &&
            $authUser->department_id &&
            $authUser->department_id === $targetUser->department_id
        ) {
            return true;
        }

        if (
            $authUser->role === 'DIVISION_ADMIN' &&
            $authUser->division_id &&
            $authUser->division_id === $targetUser->division_id
        ) {
            return true;
        }

        return true;
    }

    public function attributes(): array
    {
        return [
            'name' => __('model.user.name'),
            'email' => __('model.user.email'),
            'phone' => __('model.user.phone'),
            'registration_id' => 'Registration ID',
            'jabatan_id' => 'Jabatan',
            'jabatan_full' => 'Jabatan Lengkap',
            'department_id' => 'Department',
            'division_id' => 'Divisi',
            'directorate_id' => 'Direktorat',
            'superior_registration_id' => 'Atasan (Registration ID)',
            'nik' => 'NIK',
            'address' => 'Alamat',
            'golongan' => 'Golongan',
            'role' => 'Peran',
            'effective_date' => 'Tanggal Efektif', // ✅ Added here
        ];
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->id)],
            'phone' => ['nullable', 'string'],
            'registration_id' => ['required', 'string', Rule::unique('users', 'registration_id')->ignore($this->id)],
            'jabatan_id' => ['nullable', 'exists:jabatans,id'],
            'jabatan_full' => ['nullable', 'string'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'division_id' => ['nullable', 'exists:divisions,id'],
            'directorate_id' => ['nullable', 'exists:directorates,id'],
            'superior_registration_id' => ['nullable', 'exists:users,registration_id'],
            'nik' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'golongan' => ['nullable', 'string'],
            'is_active' => ['nullable'],
            'role' => ['required', Rule::in(['ADMIN', 'DEPARTMENT_ADMIN', 'DIVISION_ADMIN', 'USER'])],
            'profile_picture' => 'nullable|image|mimes:jpg,gif,png|max:800',
            'effective_date' => ['nullable', 'date'], // ✅ Added here
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->filled('superior_registration_id')) {
            $superior = \App\Models\User::where('registration_id', $this->superior_registration_id)->first();
            if ($superior) {
                $this->merge(['superior_id' => $superior->id]);
            }
        }
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated();
        unset($data['superior_registration_id']);
        return $data;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $hasDepartment = $this->input('department_id');
            $hasDirectorate = $this->input('directorate_id');

            if (!$hasDepartment && !$hasDirectorate) {
                $validator->errors()->add('department_id', 'Departemen atau Direktorat harus diisi.');
                $validator->errors()->add('directorate_id', 'Departemen atau Direktorat harus diisi.');
            }
        });
    }
}
