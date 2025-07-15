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

        $golongans = User::query()
            ->whereNotNull('golongan')
            ->select('golongan')
            ->distinct()
            ->orderByRaw('CAST(golongan AS UNSIGNED) ASC')
            ->pluck('golongan');


        $users = User::query()
            ->with(['department.division', 'department.directorate', 'directorate', 'division', 'jabatan'])
            ->when($request->search, fn($q, $search) => $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('registration_id', 'like', "%{$search}%")
                  ->orWhere('jabatan_full', 'like', "%{$search}%");
            }))
            ->when($request->jabatan_id, fn($q, $id) => $q->where('jabatan_id', $id))
            ->when($request->department_id, fn($q, $id) => $q->where('department_id', $id))
            ->when($request->directorate_id, fn($q, $id) =>
                $q->where(function ($query) use ($id) {
                    $query->where('directorate_id', $id)
                          ->orWhereHas('department', fn($d) => $d->where('directorate_id', $id));
                })
            )
            ->when($request->division_id, fn($q, $id) =>
                $q->where(function ($query) use ($id) {
                    $query->where('division_id', $id)
                          ->orWhereHas('department', fn($d) => $d->where('division_id', $id));
                })
            )
            ->when($request->golongan, fn($q, $gol) => $q->where('golongan', $gol))
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
            'golongans' => $golongans,
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

            if (!empty($newUser['department_id']) && empty($newUser['directorate_id'])) {
                $newUser['directorate_id'] = Department::find($newUser['department_id'])?->directorate_id;
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

            if (auth()->user()->role !== Role::ADMIN->value) {
                unset($newUser['role']);
            }

            if ($request->filled('superior_registration_id')) {
                $superior = User::where('registration_id', $request->superior_registration_id)->first();
                if ($superior) {
                    $newUser['superior_id'] = $superior->id;
                }
            }

            if (!empty($newUser['department_id']) && empty($newUser['directorate_id'])) {
                $newUser['directorate_id'] = Department::find($newUser['department_id'])?->directorate_id;
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

    public function importCsv(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt'
        ]);

        try {
            $path = $request->file('csv_file')->getRealPath();
            $file = fopen($path, 'r');

            $header = fgetcsv($file); // Skip header row

            while (($row = fgetcsv($file)) !== false) {
                $registration_id = trim($row[0]); // Registration_ID
                $name            = trim($row[1]); // Nama
                $jabatan_full    = trim($row[2]); // Jabatan_Full
                $golongan        = trim($row[3]); // Golongan
                $masa_golongan   = trim($row[4]); // Masa Golongan (ignored)
                $directorateName = trim($row[5]); // Direktorat
                $jabatanName     = trim($row[6]); // Jabatan
                $divisionName    = trim($row[7]); // Divisi
                $departmentName  = trim($row[8]); // Departemen
                $address         = trim($row[9]); // Alamat

                if (!$registration_id || !$name) continue;

                $directorate = Directorate::where('name', $directorateName)->first();
                $division    = Division::where('name', $divisionName)->first();
                $department  = Department::where('name', $departmentName)->first();
                $jabatan     = Jabatan::where('name', $jabatanName)->first();

                if ($department && !$department->directorate_id && $directorate) {
                    $department->directorate_id = $directorate->id;
                    $department->save();
                }

                if ($department && !$department->division_id && $division) {
                    $department->division_id = $division->id;
                    $department->save();
                }

                User::updateOrCreate(
                    ['registration_id' => $registration_id],
                    [
                        'name'           => $name,
                        'jabatan_full'   => $jabatan_full,
                        'golongan'       => $golongan,
                        'directorate_id' => $department?->directorate_id ?? $directorate?->id,
                        'division_id'    => $department?->division_id ?? $division?->id,
                        'department_id'  => $department?->id,
                        'jabatan_id'     => $jabatan?->id,
                        'email'          => strtolower($registration_id).'@yourdomain.com',
                        'address'        => $address,
                        'password'       => Hash::make(Config::getValueByCode(ConfigEnum::DEFAULT_PASSWORD)),
                        'is_active'      => true,
                        'role'           => Role::STAFF->value,
                    ]
                );
            }

            fclose($file);
            return redirect()->route('user.index')->with('success', 'CSV berhasil diimport.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }
}
