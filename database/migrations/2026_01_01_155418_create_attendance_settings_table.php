<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_settings', function (Blueprint $table) {
            $table->id();
            $table->text('location')->nullable();
            $table->time('check_in_setting');
            $table->time('check_out_setting');
            $table->integer('leave_quota');
            $table->integer('overtime_tolerance');
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->unsignedInteger('radius_attendance');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_settings');
    }
};
