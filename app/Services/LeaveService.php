<?php

namespace App\Services;

use App\Models\Leave;
use App\Enums\LeaveStatus;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class LeaveService
{
    public static function updateStatus(Leave $leave, LeaveStatus $newStatus): array
    {
        return DB::transaction(function () use ($leave, $newStatus) {
            // load employee relationship
            $leave->loadMissing('employee');

            // lock row employee for avoiding race condition
            $employee = $leave->employee()->lockForUpdate()->first();
            if (!$employee) {
                return [
                    'success' => false,
                    'message' => 'Karyawan tidak ditemukan'
                ];
            }

            // calculate working days in leave period except weekend
            $period = CarbonPeriod::create($leave->start_date, $leave->end_date);

            $workingDays = 0;
            foreach ($period as $date) {
                if (!$date->isWeekend()) {
                    $workingDays++;
                }
            }

            // old status before updated
            $oldStatus = $leave->status;

            // status not approved, new status approved, reduce quota
            if ($oldStatus !== LeaveStatus::Disetujui && $newStatus === LeaveStatus::Disetujui) {
                // leave quota not enough, send failed response
                if ($employee->leave_remaining < $workingDays) {
                    return [
                        'success' => false,
                        'message' => 'Data gagal diperbarui',
                        'body' => 'Sisa kuota cuti karyawan tidak mencukupi untuk menyetujui cuti ini.'
                    ];
                }
                $employee->leave_remaining -= $workingDays;
            }

            // status approved, new status not approved, increase quota
            if ($oldStatus === LeaveStatus::Disetujui && $newStatus !== LeaveStatus::Disetujui) {
                $employee->leave_remaining += $workingDays;
            }

            $employee->save();

            // set time when status updated for the first time
            if ($oldStatus === LeaveStatus::Pending && $leave->first_status_updated_at === null) {
                $leave->first_status_updated_at = now();
            }

            $leave->status = $newStatus;
            $leave->save();

            return [
                'success' => true,
                'message' => 'Data berhasil diperbarui'
            ];
        });
    }
}
