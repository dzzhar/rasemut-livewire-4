<?php

use App\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;
use Livewire\Component;
use App\Services\CheckerService;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

new class extends Component {
    use WithFileUploads;

    public $showForm = true;
    public bool $isWorkingDay = true;

    public function mount()
    {
        $this->isWorkingDay = now()->isWeekday();
    }

    public function types()
    {
        return [
            'sakit' => 'Sakit',
            'izin' => 'Izin',
        ];
    }

    #[Validate('required')]
    public $permission_type = '';
    #[Validate('nullable|image|max:5120|mimes:jpg,jpeg,png')]
    public $file_path;
    #[Validate('required_without:file_path')]
    public $description;

    public function removeFile()
    {
        $this->file_path = null;
        $this->dispatch('reset-file-input');
    }

    public function save()
    {
        $this->validate();

        $employee = Auth::user()?->employee->id;
        // $checker = app(CheckerService::class)->setEmployee($employee);

        // // cek apakah telah mengajukan izin hari ini
        // if ($checker->hasPermissionToday(now())) {
        //     $this->dispatch('show-feedback', title: 'Gagal Mengajukan Izin', message: 'Anda telah mengajukan izin hari ini. Jika terjadi kesalahan, silakan hubungi Admin.', type: 'danger');
        //     return;
        // }

        // // cek apakah ada cuti di periode ini
        // if ($checker->hasLeaveToday(now())) {
        //     $this->dispatch('show-feedback', title: 'Gagal Mengajukan Izin', message: 'Anda sedang dalam periode cuti hari ini, sehingga tidak dapat melakukan presensi.', type: 'warning');
        //     return;
        // }

        // // cek apakah telah melakukan presensi hari ini
        // if ($checker->hasAttendanceToday(now())) {
        //     $this->dispatch('show-feedback', title: 'Gagal Mengajukan Izin', message: 'Anda telah melakukan presensi hari ini, sehingga tidak dapat mengajukan izin.', type: 'warning');
        //     return;
        // }

        $path = null;
        if ($this->file_path) {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($this->file_path->getRealPath());

            // resize tetap 1024px
            $image->scale(width: 1024);

            // set nama file
            $extension = $this->file_path->getClientOriginalExtension();
            $uniqueName = 'Bukti_' . Auth::user()?->employee->fullname . '_' . now()->format('dFY') . '_' . uniqid();

            $filename = $uniqueName . '.' . $extension;

            // simpan ke storage/app/public/permissions/
            $path = 'permissions/' . $filename;
            $image->toJpeg(100)->save(storage_path('app/public/' . $path));
        }

        // jika belum izin hari ini
        DB::transaction(function () use ($employee, $path) {
            Permission::create([
                'employee_id' => $employee,
                'permission_date' => now(),
                'file_path' => $path,
                'permission_type' => $this->permission_type,
                'description' => $this->description,
            ]);
        });

        $this->dispatch('show-feedback', title: 'Izin Diajukan!', message: 'Pengajuan izin Anda hari ini berhasil dilakukan.');

        $this->reset(['permission_type', 'file_path', 'description']);
        $this->dispatch('refresh-history');
    }
};
?>


<div>
    @if ($isWorkingDay)
        <flux:card size="md" class="bg-white dark:bg-zinc-900" x-cloak>
            <div x-on:click="$wire.showForm = !$wire.showForm" class="cursor-pointer">
                <flux:fieldset class="flex items-center justify-between">
                    <flux:text size="xl" level="2" class="text-zinc-800 dark:text-white font-medium">
                        Formulir Perizinan
                    </flux:text>

                    <flux:button size="sm" icon="chevron-down" variant="ghost" />
                </flux:fieldset>
                <flux:separator variant="subtle" class="mt-6" wire:show="showForm" x-transition.duration.500ms />
            </div>

            <form class="space-y-6 mt-4" wire:show="showForm" x-transition.duration.500ms wire:submit.prevent="save"
                enctype="multipart/form-data">
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Select -->
                        <div class="{{ $file_path ? 'md:col-span-2' : '' }}">
                            <flux:select label="Jenis Izin" wire:model="permission_type"
                                placeholder="Pilih jenis izin...">
                                @foreach ($this->types() as $value => $label)
                                    <flux:select.option value="{{ $value }}">
                                        {{ $label }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                        </div>

                        <!-- Input File -->
                        <flux:input wire:model="file_path" type="file" label="Bukti Izin/Sakit"
                            accept="image/png,image/jpeg,image/jpg" />

                        <!-- Preview -->
                        @if ($file_path)
                            <div class="flex justify-start md:justify-end w-full mt-2">
                                <div class="relative w-full md:max-w-56">
                                    <img src="{{ $file_path->temporaryUrl() }}"
                                        class="rounded-lg w-full h-auto object-cover">

                                    <flux:button type="button" size="xs" icon="x-mark" variant="danger"
                                        class="absolute! top-2 right-2" wire:click="removeFile" />
                                </div>
                            </div>
                        @endif

                    </div>

                </div>

                <flux:textarea wire:model="description" label="Keterangan" placeholder="Keterangan izin anda..."
                    description="Wajib diisi jika tidak melampirkan bukti izin." resize="none" />


                <flux:button variant="primary" color="blue" class="w-full" type="submit" wire:loading.attr="disabled"
                    wire:target="file_path">Kirim
                </flux:button>
            </form>
        </flux:card>
    @endif
</div>
