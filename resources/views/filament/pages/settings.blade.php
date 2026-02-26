<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div>
            <form wire:submit.prevent="submit">
                {{ $this->schema }}

                <div class="mt-4">
                    <x-filament::button type="submit">
                        Simpan
                    </x-filament::button>
                </div>
            </form>
        </div>

        <div class="flex flex-col justify-center">
            <h1 class="text-3xl font-bold">
                Custom Page 
            </h1>

            <p class="text-gray-500 mt-2">
                Ini adalah subtitle atau deskripsi halaman.
                Bisa kamu isi dengan informasi tambahan,
                panduan user, atau instruksi pengisian form.
            </p>
        </div>

    </div>
</x-filament-panels::page>
