<?php

use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Perizinan')] class extends Component {
    //
};
?>

<div class="space-y-8">
    <livewire:pages::permission.create />
    <livewire:history-card headerTitle="Riwayat Izin" model="\App\Models\Permission" dateColumn="permission_date"
        :select="['id', 'permission_type', 'permission_date', 'status', 'description']" lazy />
    <livewire:pages::permission.detail />
</div>
