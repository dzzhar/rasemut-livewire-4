<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetLeaveQuota extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-leave-quota';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset leave quota for all employees';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $quota = DB::table('attendance_settings')->value('leave_quota') ?? 12;

        DB::table('employees')->update([
            'leave_remaining' => $quota,
            'updated_at' => now(),
        ]);
    }
}
