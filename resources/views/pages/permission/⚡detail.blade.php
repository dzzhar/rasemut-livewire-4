<?php

use App\Models\Permission;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public ?Permission $selected = null;

    #[On('show-detail-history')]
    public function showDetail(int $id)
    {
        $this->selected = Permission::select('id', 'permission_date', 'permission_type', 'status', 'description')->where('employee_id', 2)->where('id', $id)->first();

        if ($this->selected) {
            $this->modal('detail-modal-permission')->show();
        }
    }
};
?>


<flux:modal name="detail-modal-permission" class="md:w-96">
    <div class="space-y-6">
        <flux:heading size="lg">Detail Informasi Izin</flux:heading>

        @if ($selected)
            <div>
                <flux:heading>Jenis Izin</flux:heading>
                <flux:text class="mt-2 capitalize">{{ $selected->history_type }}</flux:text>
            </div>
            <div>
                <flux:heading>Tanggal Izin</flux:heading>
                <flux:text class="mt-2">
                    {{ $selected->history_date ? $selected->history_date->translatedFormat('l, d F Y • H:i:s') . ' WIB' : '-' }}
                </flux:text>
            </div>
            <div>
                <flux:heading>Keterangan</flux:heading>
                <flux:text class="mt-2 capitalize">
                    {{ $selected->description ?? '-' }}
                </flux:text>
            </div>
            <div>
                <flux:heading>Status</flux:heading>
                <flux:badge rounded size="sm" color="green" class="mt-2">{{ $selected->status }}</flux:badge>
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
