<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;

class UserPositionHistorySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $users = User::all();

        foreach ($users as $user) {
            DB::table('user_position_histories')->insert([
                'user_id'        => $user->id,
                'registration_id'=> $user->registration_id,
                'directorate_id' => $user->directorate_id,
                'division_id'    => $user->division_id,
                'department_id'  => $user->department_id,
                'jabatan_id'     => $user->jabatan_id,
                'jabatan_full'   => $user->jabatan_full,
                'superior_id'    => $user->superior_id,
                'golongan'       => $user->golongan,
                'is_active'      => $user->is_active,
                'recorded_at'    => $user->created_at ?? $now,
                'effective_date' => null,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);
        }
    }
}
