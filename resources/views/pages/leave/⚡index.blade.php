<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app', ['title' => 'Cuti'])] class extends Component {
    //
};
?>

<div class="space-y-8">
    <livewire:pages::leave.create />
    <livewire:history-card headerTitle="Riwayat Pengajuan Cuti" model="\App\Models\Leave" dateColumn="request_date"
        :select="['id', 'leave_code', 'request_date', 'status', 'description']" lazy />
    <livewire:pages::leave.detail />
</div>
