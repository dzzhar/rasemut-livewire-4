<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->timestamp('attendance_date')->index();
            $table->enum('attendance_type', ['in', 'out']);
            $table->enum('status', ['tepat waktu', 'terlambat', 'akhir shift', 'pulang cepat', 'lembur', 'tidak absen']);
            $table->text('description')->nullable();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
