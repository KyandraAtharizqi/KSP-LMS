<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPositionHistoriesTable extends Migration
{
    public function up(): void
    {
        Schema::create('user_position_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('registration_id')->nullable();

            $table->foreignId('directorate_id')->nullable()->constrained('directorates')->nullOnDelete();
            $table->foreignId('division_id')->nullable()->constrained('divisions')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('jabatan_id')->nullable()->constrained('jabatans')->nullOnDelete();
            $table->string('jabatan_full')->nullable();

            $table->foreignId('superior_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('golongan')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamp('recorded_at')->nullable();      // When the data was logged into the system
            $table->timestamp('effective_date')->nullable();   // When the position started

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_position_histories');
    }
}
