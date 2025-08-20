<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->role === 'ADMIN';
    }

    public function attributes(): array
    {
        return [
            'registration_id' => __('model.user.registration_id'),
            'name' => __('model.user.name'),
            'email' => __('model.user.email'),
            'phone' => __('model.user.phone'),
            'jabatan_id' => 'Jabatan',
            'jabatan_full' => 'Jabatan Lengkap',
            'department_id' => 'Departemen',
            'division_id' => 'Divisi',
            'directorate_id' => 'Direktorat',
            'superior_registration_id' => 'Atasan',
            'nik' => 'NIK',
            'address' => 'Alamat',
            'golongan' => 'Golongan',
            'role' => 'Peran',
            'effective_date' => 'Tanggal Efektif',
        ];
    }

    public function rules(): array
    {
        return [
            'registration_id' => ['required', Rule::unique('users', 'registration_id')],
            'name' => ['required'],
            'email' => ['required', Rule::unique('users', 'email')],
            'phone' => ['nullable'],
            'jabatan_id' => ['nullable', 'exists:jabatans,id'],
            'jabatan_full' => ['nullable', 'string'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'division_id' => ['nullable', 'exists:divisions,id'],
            'directorate_id' => ['nullable', 'exists:directorates,id'],
            'superior_registration_id' => ['nullable', 'string'],
            'nik' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'golongan' => ['nullable', 'string'],
            'is_active' => ['nullable'],
            'role' => ['required', Rule::in(['ADMIN', 'DEPARTMENT_ADMIN', 'DIVISION_ADMIN', 'USER'])],
            'effective_date' => ['required', 'date'], // ✅ Added validation rule
        ];
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
