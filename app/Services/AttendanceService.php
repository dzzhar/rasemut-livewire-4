<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendanceSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    protected int $employeeId;
    protected AttendanceSetting $setting;
    protected CheckerService $checker;

    public function __construct(int $employeeId)
    {
        $this->employeeId = $employeeId;
        $this->setting = AttendanceSetting::firstOrFail();
        $this->checker = app(CheckerService::class)->setEmployee($employeeId);
    }

    // untuk mendapatkan status presensi hari ini
    public function getTodayState(): array
    {
        return $this->getAttendanceByDate(now());
    }

    // handle presensi utama
    public function handleAttendance(): array
    {
        return DB::transaction(function () {
            $now = now();

            // kalo misal belum absen kemarin, maka otomatis dianggap tidak absen
            $this->handleYesterday();

            // cek cuti hari ini
            if ($this->checker->hasLeaveToday($now)) {
                return [
                    'title' => 'Presensi Gagal',
                    'message' => 'Anda sedang dalam periode cuti hari ini, sehingga tidak dapat melakukan presensi.',
                    'type' => 'warning',
                ];
            }

            // cek izin hari ini
            if ($this->checker->hasPermissionToday($now)) {
                return [
                    'title' => 'Presensi Gagal',
                    'message' => 'Anda telah mengajukan izin hari ini, sehingga tidak dapat melakukan presensi.',
                    'type' => 'warning',
                ];
            }

            // cek presensi hari ini
            $today = $this->getAttendanceByDate($now);

            if (!$today['in']) {
                $this->doCheckIn($now);
            } elseif (!$today['out']) {
                $this->doCheckOut($now);
            }

            return [
                'title' => 'Presensi Berhasil!',
                'message' => 'Presensi Anda hari ini berhasil dilakukan.',
                'type' => 'success',
            ];
        });
    }

    // aksi untuk presensi masuk (check-in)
    protected function doCheckIn(Carbon $now)
    {
        // handle waktu check-in
        $timeCheckIn = Carbon::today()
            ->setTimeFromTimeString($this->setting->check_in_setting);

        // handle status terlambat atau tepat waktu
        if ($now->gt($timeCheckIn)) {
            $diff = $timeCheckIn->diff($now);
            $status = 'terlambat';
            $desc = sprintf("Terlambat %02d jam %02d menit %02d detik", $diff->h, $diff->i, $diff->s);
        } else {
            $status = 'tepat waktu';
            $desc = null;
        }

        // simpan data presensi masuk
        Attendance::create([
            'employee_id' => $this->employeeId,
            'attendance_date' => $now,
            'attendance_type' => 'in',
            'status' => $status,
            'description' => $desc
        ]);
    }

    // aksi untuk presensi pulang (check-out)
    protected function doCheckOut(Carbon $now)
    {
        // handle waktu check-out
        $timeCheckOut = Carbon::today()
            ->setTimeFromTimeString($this->setting->check_out_setting);

        // handle toleransi lembur
        $overtime = $timeCheckOut
            ->copy()
            ->addMinutes($this->setting->overtime_tolerance);

        // handle status pulang cepat, akhir shift, atau lembur
        $status = match (true) {
            // kurang dari waktu check-out → pulang cepat
            $now->lt($timeCheckOut) => 'pulang cepat',
            // antara waktu check-out sampai akhir toleransi lembur → akhir shift
            $now->between($timeCheckOut, $overtime) => 'akhir shift',
            // lebih dari waktu toleransi lembur → lembur
            $now->gt($overtime) => 'lembur',
        };

        $desc = null;

        // handle deskripsi waktu lembur
        if ($status === 'lembur') {
            $diff = $overtime->diff($now);
            $desc = sprintf("Lembur %02d jam %02d menit %02d detik", $diff->h, $diff->i, $diff->s);
        }

        // simpan data presensi pulang
        Attendance::create([
            'employee_id' => $this->employeeId,
            'attendance_date' => $now,
            'attendance_type' => 'out',
            'status' => $status,
            'description' => $desc
        ]);
    }

    // handle data presensi yang tidak melakukan aksi kemarin
    protected function handleYesterday(): void
    {
        $yesterday = now()->subDay()->startOfDay();

        // kalo kemarin bukan hari kerja → stop
        if (!$this->isWorkingDay($yesterday)) {
            return;
        }

        // kalo iya hari kerja, cek data presensi kemarin
        $data = $this->getAttendanceByDate($yesterday);


        // Tidak klik sama sekali
        if (!$data['in'] && !$data['out']) {
            // simpan data presensi masuk dengan status tidak absen
            Attendance::create([
                'employee_id' => $this->employeeId,
                'attendance_date' => $yesterday,
                'attendance_type' => 'out',
                'status' => 'tidak absen',
                'description' => 'Tidak melakukan presensi pulang'
            ]);

            // simpan data presensi pulang dengan status tidak absen
            Attendance::create([
                'employee_id' => $this->employeeId,
                'attendance_date' => $yesterday,
                'attendance_type' => 'in',
                'status' => 'tidak absen',
                'description' => 'Tidak melakukan presensi masuk'
            ]);
        }

        // check-in tetapi tidak check-out
        elseif ($data['in'] && !$data['out']) {
            // simpan data presensi pulang dengan status tidak absen
            Attendance::create([
                'employee_id' => $this->employeeId,
                'attendance_date' => $yesterday,
                'attendance_type' => 'out',
                'status' => 'tidak absen',
                'description' => 'Tidak melakukan presensi pulang'
            ]);
        }
    }

    // fungsi untuk cek hari kerja (senin-jumat)
    public function isWorkingDay(Carbon $date): bool
    {
        return $date->isWeekday();
    }

    // ambil data presensi berdasarkan tanggal dan tipe presensi
    protected function getAttendanceByDate(Carbon $date): array
    {
        $data = Attendance::where('employee_id', $this->employeeId)
            ->whereDate('attendance_date', $date)
            ->orderByRaw("FIELD(attendance_type, 'out', 'in')")
            ->get()
            ->keyBy('attendance_type');

        return [
            'in' => $data->get('in'),
            'out' => $data->get('out'),
        ];
    }
}
