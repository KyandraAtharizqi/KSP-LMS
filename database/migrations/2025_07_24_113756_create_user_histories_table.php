<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Snapshot of user organizational position
            $table->foreignId('jabatan_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('division_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('directorate_id')->nullable()->constrained()->nullOnDelete();

            // The actual date the change is effective from
            $table->date('effective_date');

            // Who changed this history (optional, for audit)
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps(); // includes created_at (recorded_at) & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_histories');
    }
};
