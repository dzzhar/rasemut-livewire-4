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

        $this->selected = Attendance::select('id', 'attendance_date', 'check_in', 'check_out', 'status', 'description')->whereBelongsTo($employee)->where('id', $id)->first();

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
                <flux:heading>Tanggal Presensi</flux:heading>
                <flux:text class="mt-2">
                    {{ $selected->history_date ? $selected->history_date->translatedFormat('l, d F Y') : '-' }}
                </flux:text>
            </div>
            <div>
                <flux:heading>Check In</flux:heading>
                <flux:text class="mt-2 capitalize">
                    {{ $selected->check_in ? $selected->check_in . ' WIB' : '-' }}
                </flux:text>
            </div>
            <div>
                <flux:heading>Check Out</flux:heading>
                <flux:text class="mt-2 capitalize">
                    {{ $selected->check_out ? $selected->check_out . ' WIB' : '-' }}
                </flux:text>
            </div>
            <div>
                <flux:heading>Keterangan</flux:heading>
                <flux:text class="mt-2 whitespace-normal wrap-break-word first-letter:uppercase">
                    {{ $selected->description ?? '-' }}
                </flux:text>
            </div>
            <div>
                <flux:heading>Status</flux:heading>
                <flux:badge rounded size="sm" :color="$selected->status->badgeColor()" class="mt-2">
                    {{ $selected->status->badgeLabel() }}
                </flux:badge>
            </div>
        @endif

        <div class="flex">
            <flux:spacer />
            <flux:button variant="primary" size="sm" x-on:click="$flux.modals().close()">Kembali</flux:button>
        </div>
    </div>
</flux:modal>
