<?php

use App\Models\Attendance;
use Livewire\Component;

new class extends Component {
    public ?Attendance $selected = null;

    public function showDetail($id)
    {
        $this->selected = Attendance::find($id);
        $this->js('$modal(\'detail-modal\').show()');
    }
};
?>


<div>
    @if ($selected)
    <flux:modal name="detail-modal" class="md:w-96">
        <div class="space-y-6">
            <flux:heading size="lg">Detail Presensi</flux:heading>

            <div>
                <flux:heading>Jenis Presensi</flux:heading>
                <flux:text class="mt-2">{{ $selected->history_type }}</flux:text>
            </div>
            <div>
                <flux:heading>Tanggal Presensi</flux:heading>
                <flux:text class="mt-2">
                    {{ $selected->history_date ? $selected->history_date->translatedFormat('l, d F Y • H:i:s') . ' WIB' : '-' }}
                </flux:text>
            </div>
            <div>
                <flux:heading>Keterangan</flux:heading>
                <flux:text class="mt-2">{{ $selected->description ?? '-' }}</flux:text>
            </div>
            <div>
                <flux:heading>Status</flux:heading>
                <flux:badge rounded size="sm" :color="$selected->badge_color"> {{ $selected->status }}
                </flux:badge>
            </div>

            <div class="flex">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="primary" size="sm">Kembali</flux:button>
                </flux:modal.close>
            </div>
        </div>
    </flux:modal>
    @endif
</div>