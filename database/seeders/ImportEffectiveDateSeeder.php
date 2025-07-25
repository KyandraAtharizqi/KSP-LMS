<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserPositionHistory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ImportEffectiveDateSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/effective_dates.csv');

        $csvEffectiveDates = [];

        // Load CSV data if file exists
        if (file_exists($path)) {
            $rows = array_map('str_getcsv', file($path));
            $header = array_map('trim', array_shift($rows));

            foreach ($rows as $row) {
                $data = array_combine($header, $row);
                $registrationId = trim($data['registration_id']);
                $effectiveDate = Carbon::parse(trim($data['effective_date']));
                $csvEffectiveDates[$registrationId] = $effectiveDate;
            }
        } else {
            $this->command->warn("CSV file not found: $path. Proceeding with default effective date.");
        }

        $defaultDate = Carbon::create(2025, 1, 1);
        $updated = 0;
        $skipped = 0;

        foreach (User::all() as $user) {
            $effectiveDate = $csvEffectiveDates[$user->registration_id] ?? $defaultDate;

            $uph = UserPositionHistory::where('user_id', $user->id)
                ->where('is_active', true)
                ->first();

            if (!$uph) {
                $this->command->warn("No active position history for user: $user->registration_id");
                $skipped++;
                continue;
            }

            $uph->effective_date = $effectiveDate;
            $uph->save();

            $updated++;
        }

        $this->command->info("Seeder complete. Updated: $updated users. Skipped: $skipped (no active position history).");
    }
}
