<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeactivateInactiveUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:deactivate-inactive-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nonaktifkan user jika tidak aktif selama 5 hari kerja';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now();

        User::where('is_active', true)
            ->whereNotNull('last_activity')
            ->get()
            ->each(function ($user) use ($today) {
                if (Carbon::parse($user->last_activity)->diffInWeekdays($today) >= 5) {
                    $user->update(['is_active' => false]);
                }
            });

        $this->info("Proses selesai.");
        return Command::SUCCESS;
    }
}
