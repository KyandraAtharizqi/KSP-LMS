<?php

namespace App\Http\Controllers;

use App\Models\Config;
use App\Enums\Config as ConfigValues;
use App\Enums\Role;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Jabatan;
use App\Models\Department;
use App\Models\Directorate;
use App\Models\User;
use App\Models\Division;
use App\Models\Notifikasi;
use App\Models\UserPositionHistory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $auth = auth()->user();
        $roles = array_combine(
            Role::cases(),
            array_map(fn($role) => Role::label($role), Role::cases())
        );

        $golongans = User::query()
            ->whereNotNull('golongan')
            ->select('golongan')
            ->distinct()
            ->orderByRaw('CAST(golongan AS UNSIGNED) ASC')
            ->pluck('golongan');

        $users = User::query()
            ->with([
                'department.division',
                'department.directorate',
                'directorate',
                'division',
                'jabatan',
                'positionHistories.jabatan',
                'positionHistories.department',
                'positionHistories.division',
                'positionHistories.directorate'
            ])
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
                $auth && $auth->role === Role::DEPARTMENT_ADMIN,
                fn($q) => $q->where('department_id', $auth->department_id)
            )
            ->when(
                $auth && $auth->role === Role::DIVISION_ADMIN,
                fn($q) => $q->whereHas('department', fn($d) =>
                    $d->where('division_id', $auth->division_id)
                )
            )
            ->paginate((int)Config::getValueByCode(ConfigValues::PAGE_SIZE))
            ->appends($request->query());

        $user = auth()->user();
        $notifikasi = [];
        $unreadCount = 0;
        
        if ($user) {
            $notifikasi = Notifikasi::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            $unreadCount = Notifikasi::where('user_id', $user->id)
                ->where('dibaca', false)
                ->count();
        }

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
            'notifikasi' => $notifikasi,
            'unreadCount' => $unreadCount,
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        try {
            $newUser = $request->validated();
            $newUser['password'] = Hash::make(Config::getValueByCode(ConfigValues::DEFAULT_PASSWORD));
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

            $user = User::create($newUser);

            UserPositionHistory::create([
                'user_id'       => $user->id,
                'jabatan_id'    => $user->jabatan_id,
                'department_id' => $user->department_id,
                'division_id'   => $user->division_id,
                'directorate_id'=> $user->directorate_id,
                'is_active'     => true,
                'effective_date'=> $request->filled('effective_date') ? Carbon::parse($request->effective_date) : Carbon::now(),
            ]);

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

            if (auth()->user()->role !== Role::ADMIN) {
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
                $newUser['password'] = Hash::make(Config::getValueByCode(ConfigValues::DEFAULT_PASSWORD));
            }

            $positionChanged = (
                $user->jabatan_id     !== $newUser['jabatan_id'] ||
                $user->department_id  !== $newUser['department_id'] ||
                $user->division_id    !== $newUser['division_id'] ||
                $user->directorate_id !== $newUser['directorate_id']
            );

            $user->update($newUser);

            if ($positionChanged) {
                UserPositionHistory::where('user_id', $user->id)
                    ->where('is_active', true)
                    ->update(['is_active' => false]);

                UserPositionHistory::create([
                    'user_id'       => $user->id,
                    'jabatan_id'    => $user->jabatan_id,
                    'department_id' => $user->department_id,
                    'division_id'   => $user->division_id,
                    'directorate_id'=> $user->directorate_id,
                    'is_active'     => true,
                    'effective_date'=> $request->filled('effective_date') ? Carbon::parse($request->effective_date) : Carbon::now(),
                ]);
            }

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
            $header = fgetcsv($file);

            while (($row = fgetcsv($file)) !== false) {
                $registration_id = trim($row[0]);
                $name            = trim($row[1]);
                $jabatan_full    = trim($row[2]);
                $golongan        = trim($row[3]);
                $directorateName = trim($row[5]);
                $jabatanName     = trim($row[6]);
                $divisionName    = trim($row[7]);
                $departmentName  = trim($row[8]);
                $address         = trim($row[9]);

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

                $user = User::updateOrCreate(
                    ['registration_id' => $registration_id],
                    [
                        'name'           => $name,
                        'jabatan_full'   => $jabatan_full,
                        'golongan'       => $golongan,
                        'directorate_id' => $department?->directorate_id ?? $directorate?->id,
                        'division_id'    => $department?->division_id ?? $division?->id,
                        'department_id'  => $department?->id,
                        'jabatan_id'     => $jabatan?->id,
                        'email'          => strtolower($registration_id) . '@yourdomain.com',
                        'address'        => $address,
                        'password'       => Hash::make(Config::getValueByCode(ConfigValues::DEFAULT_PASSWORD)),
                        'is_active'      => true,
                        'role'           => Role::STAFF,
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
