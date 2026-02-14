<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

<div class="space-y-8">
    <livewire:pages::leave.create />
    <livewire:history.container headerTitle="Riwayat Pengajuan Cuti" model="\App\Models\Leave" dateColumn="request_date"
        :select="['id', 'leave_code', 'request_date', 'status', 'description']" lazy />
</div>
