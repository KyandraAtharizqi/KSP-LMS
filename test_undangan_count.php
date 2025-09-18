<?php

require_once 'vendor/autoload.php';

use App\Models\PengajuanKnowledge;
use App\Models\User;

// Set up Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing Undangan Count Logic\n";
echo "==============================\n\n";

// Get all approved knowledge sharing
$approvedKnowledge = PengajuanKnowledge::where('status', 'approved')->get();
echo "Total approved knowledge sharing: " . $approvedKnowledge->count() . "\n\n";

// Test for different user roles
$users = User::whereIn('role', ['admin', 'staff', 'department_admin'])->limit(3)->get();

foreach ($users as $user) {
    echo "User: {$user->name} (Role: {$user->role})\n";
    
    if ($user->role === 'admin') {
        $totalUndangan = $approvedKnowledge->count();
        echo "  Admin sees all: {$totalUndangan} undangan\n";
    } else {
        $filteredUndangan = $approvedKnowledge->filter(function ($u) use ($user) {
            $participantIds = collect($u->peserta ?? [])->pluck('id')->all();
            return $user->id === $u->created_by  // pengaju
                || $user->name === $u->kepada   // approver
                || in_array($user->id, $participantIds); // peserta
        });
        $totalUndangan = $filteredUndangan->count();
        echo "  Filtered for user: {$totalUndangan} undangan\n";
    }
    echo "\n";
}

echo "Test completed successfully!\n";
