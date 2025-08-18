<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImportUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the SQL content from file
        $sql = file_get_contents(base_path() . '/database/seeders/users_data.sql');

        // Extract values from INSERT statements
        preg_match_all('/INSERT INTO `users`.*VALUES\s*(\([^;]+\));/s', $sql, $matches);

        if (isset($matches[1])) {
            foreach ($matches[1] as $values) {
                // Split multiple rows if any
                $rows = explode('),(', trim($values, '()'));
                
                foreach ($rows as $row) {
                    // Break row into fields and clean up values
                    preg_match_all("/'([^']*)'|NULL/", $row, $fieldMatches);
                    $fields = $fieldMatches[0];
                    
                    // Get clean values from matches
                    $fields = $fieldMatches[1];
                    
                    // Handle numeric fields (id, is_active, etc)
                    $values = explode(',', $row);
                    $fields[0] = trim($values[0]); // id 
                    $fields[13] = trim($values[13]); // is_active

                    // Map the values to fields, matching the SQL file structure
                    $user = [
                        'id' => $fields[0],
                        'registration_id' => $fields[1],
                        'name' => $fields[2],
                        'email' => $fields[3],
                        'email_verified_at' => $fields[4],
                        'password' => $fields[5],
                        'two_factor_secret' => $fields[6],
                        'two_factor_recovery_codes' => $fields[7],
                        'phone' => $fields[8],
                        'address' => $fields[9],
                        'signature_path' => $fields[10],
                        'paraf_path' => $fields[11],
                        'role' => $fields[12],
                        'is_active' => $fields[13],
                        'profile_picture' => $fields[14],
                        'remember_token' => $fields[15],
                        'created_at' => $fields[16],
                        'updated_at' => $fields[17],
                        'jabatan_id' => isset($fields[18]) ? $fields[18] : null,
                        'jabatan_full' => isset($fields[19]) ? $fields[19] : null,
                        'department_id' => isset($fields[20]) ? $fields[20] : null,
                        'directorate_id' => isset($fields[21]) ? $fields[21] : null,
                        'division_id' => isset($fields[22]) ? $fields[22] : null,
                        'superior_id' => isset($fields[23]) ? $fields[23] : null,
                        'golongan' => isset($fields[24]) ? $fields[24] : null,
                        'nik' => isset($fields[25]) ? $fields[25] : null,
                    ];
                    
                    try {
                        DB::table('users')->insert($user);
                    } catch (\Exception $e) {
                        echo "Error inserting user {$user['name']}: " . $e->getMessage() . "\n";
                    }
                }
            }
        }
    }
}
