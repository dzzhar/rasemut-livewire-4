<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->timestamp('request_date')->index();
            $table->string('leave_code', 256)->index();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->text('description')->nullable();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leaves');
    }
};
