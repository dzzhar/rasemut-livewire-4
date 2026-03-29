<?php

use App\Models\Leave;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public ?Leave $selected = null;

    #[On('show-detail-history')]
    public function showDetail(int $id)
    {
        $employee = Auth::user()?->employee;
        if (!$employee) {
            return collect();
        }

        $this->selected = Leave::select('id', 'request_date', 'leave_code', 'start_date', 'end_date', 'status', 'description')->whereBelongsTo($employee)->where('id', $id)->first();

        if ($this->selected) {
            $this->modal('detail-modal-leave')->show();
        }
    }
};
?>


<flux:modal name="detail-modal-leave" class="md:w-96">
    <div class="space-y-6">
        <flux:heading size="lg">Detail Pengajuan Cuti</flux:heading>

        @if ($selected)
            <div>
                <flux:heading>Kode Cuti</flux:heading>
                <flux:text class="mt-2">{{ $selected->leave_code }}</flux:text>
            </div>

            <div>
                <flux:heading>Tanggal Pengajuan</flux:heading>
                <flux:text class="mt-2">{{ $selected->request_date->translatedFormat('l, d F Y') }}</flux:text>
            </div>
            <div>
                <flux:heading>Periode Cuti</flux:heading>
                <flux:text class="mt-2">
                    {{ $selected->start_date->translatedFormat('d M Y') }} -
                    {{ $selected->end_date->translatedFormat('d M Y') }}
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
                    {{ $selected->status }}
                </flux:badge>
            </div>
        @endif

        <div class="flex">
            <flux:spacer />
            <flux:button variant="primary" size="sm" x-on:click="$flux.modals().close()">Kembali</flux:button>
        </div>
    </div>
</flux:modal>
