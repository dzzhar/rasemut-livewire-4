<?php

use App\Models\Attendance;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public ?Attendance $selected = null;

    #[On('show-detail-history')]
    public function showDetail(int $id)
    {
        $employee = Auth::user()?->employee;
        if (!$employee) {
            return collect();
        }

        $this->selected = Attendance::select('id', 'attendance_date', 'attendance_type', 'status', 'description')->whereBelongsTo($employee)->where('id', $id)->first();

        if ($this->selected) {
            $this->modal('detail-modal-attendance')->show();
        }
    }
};
?>


<flux:modal name="detail-modal-attendance" class="md:w-96">
    <div class="space-y-6">
        <flux:heading size="lg">Detail Informasi Presensi</flux:heading>

        @if ($selected)
            <div>
                <flux:heading>Jenis Presensi</flux:heading>
                <flux:text class="mt-2 capitalize">{{ $selected->history_type }}</flux:text>
            </div>
            <div>
                <flux:heading>Tanggal Presensi</flux:heading>
                <flux:text class="mt-2">
                    {{ $selected->history_date ? $selected->history_date->translatedFormat('l, d F Y • H:i:s') . ' WIB' : '-' }}
                </flux:text>
            </div>
            <div>
                <flux:heading>Keterangan</flux:heading>
                <flux:text class="mt-2">
                    {{ $selected->description ?? '-' }}
                </flux:text>
            </div>
            <div>
                <flux:heading>Status</flux:heading>
                <flux:badge rounded size="sm" :color="$selected->status->badgeColor()" class="mt-2">
                    {{ $selected->status }}
                </flux:badge>
            </div>
        @endif

        <div class="flex">
            <flux:spacer />
            <flux:modal.close>
                <flux:button variant="primary" size="sm">Kembali</flux:button>
            </flux:modal.close>
        </div>
    </div>
</flux:modal>
