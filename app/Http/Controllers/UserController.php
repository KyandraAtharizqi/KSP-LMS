<?php

namespace App\Http\Controllers;

use App\Enums\Config as ConfigEnum;
use App\Enums\Role;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Config;
use App\Models\Jabatan;
use App\Models\Department;
use App\Models\Directorate;
use App\Models\User;
use App\Models\Division;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $auth = auth()->user();
        $roles = collect(Role::cases())->mapWithKeys(fn ($role) => [
            $role->value => $role->label()
        ]);

        $users = User::query()
            ->with(['department.division', 'department.directorate', 'directorate', 'jabatan'])
            ->when($request->search, fn($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->when($request->jabatan_id, fn($q, $id) => $q->where('jabatan_id', $id))
            ->when($request->department_id, fn($q, $id) => $q->where('department_id', $id))
            // Apply scoping based on role
            ->when(
                $auth->role === Role::DEPARTMENT_ADMIN->value,
                fn($q) => $q->where('department_id', $auth->department_id)
            )
            ->when(
                $auth->role === Role::DIVISION_ADMIN->value,
                fn($q) => $q->whereHas('department', fn($d) =>
                    $d->where('division_id', $auth->division_id)
                )
            )
            ->paginate(Config::getValueByCode(ConfigEnum::PAGE_SIZE))
            ->appends($request->query());

        return view('pages.user', [
            'data' => $users,
            'search' => $request->search,
            'jabatans' => Jabatan::all(),
            'departments' => Department::all(),
            'directorates' => Directorate::all(),
            'divisions' => Division::all(),
            'users' => User::all(),
            'roles' => $roles,
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        try {
            $newUser = $request->validated();
            $newUser['password'] = Hash::make(Config::getValueByCode(ConfigEnum::DEFAULT_PASSWORD));
            $newUser['is_active'] = isset($newUser['is_active']);

            if ($request->filled('superior_registration_id')) {
                $superior = User::where('registration_id', $request->superior_registration_id)->first();
                if ($superior) {
                    $newUser['superior_id'] = $superior->id;
                }
            }

            if (!empty($newUser['department_id'])) {
                if (empty($newUser['directorate_id'])) {
                    $newUser['directorate_id'] = Department::find($newUser['department_id'])?->directorate_id;
                }
            } elseif (!empty($newUser['directorate_id'])) {
                $newUser['department_id'] = null;
            } else {
                return back()->with('error', 'Harap isi salah satu: Departemen atau Direktorat.');
            }

            User::create($newUser);
            return back()->with('success', __('menu.general.success'));

        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        try {
            $newUser = $request->validated();
            $newUser['is_active'] = isset($newUser['is_active']);

            // Prevent role changes unless admin
            if (auth()->user()->role !== Role::ADMIN->value) {
                unset($newUser['role']);
            }

            if ($request->filled('superior_registration_id')) {
                $superior = User::where('registration_id', $request->superior_registration_id)->first();
                if ($superior) {
                    $newUser['superior_id'] = $superior->id;
                }
            }

            if (!empty($newUser['department_id'])) {
                if (empty($newUser['directorate_id'])) {
                    $newUser['directorate_id'] = Department::find($newUser['department_id'])?->directorate_id;
                }
            } elseif (!empty($newUser['directorate_id'])) {
                $newUser['department_id'] = null;
            } else {
                return back()->with('error', 'Harap isi salah satu: Departemen atau Direktorat.');
            }

            if ($request->reset_password) {
                $newUser['password'] = Hash::make(Config::getValueByCode(ConfigEnum::DEFAULT_PASSWORD));
            }

            $user->update($newUser);
            return back()->with('success', __('menu.general.success'));

        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    public function destroy(User $user): RedirectResponse
    {
        try {
            $user->delete();
            return back()->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }
}
