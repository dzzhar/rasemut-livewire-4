@placeholder
    <flux:skeleton class="rounded-lg h-64 w-full" animate="shimmer" />
@endplaceholder

<flux:card size="md" class="bg-white dark:bg-zinc-900 antialiased">
    <div class="flex items-start justify-between mb-6">
        <div>
            <flux:heading size="lg" class="font-semibold">Ringkasan Kehadiran</flux:heading>
            <flux:text> {{ $this->getTestDate()->translatedFormat('F Y') }} - {{ $this->totalDays }}
                hari kerja
            </flux:text>
        </div>
        <div class="text-right">
            <div class="text-3xl leading-none font-semibold {{ $this->scoreColor['class'] }}">
                {{ $this->scorePercent }}%
            </div>
            <flux:text class="text-xs mt-0.5">Tingkat Kehadiran</flux:text>
        </div>
    </div>

    <flux:progress value="{{ $this->scorePercent }}" color="{{ $this->scoreColor['color'] }}" class="my-6 h-3" />

    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div
            class="rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-100 dark:border-emerald-900 px-4 py-3">
            <div class="flex items-center gap-1.5 mb-2">
                <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                <span class="text-[11px] font-medium text-emerald-700 dark:text-emerald-400">Hadir</span>
            </div>
            <div class="text-2xl font-bold text-emerald-700 dark:text-emerald-300 leading-none">
                {{ $this->totalAttendance }}
            </div>
            <div class="text-[10px] text-emerald-600/70 dark:text-emerald-500 mt-1">
                {{ $this->hadirPercent }}% dari total
            </div>
        </div>

        <div
            class="rounded-xl bg-orange-50 dark:bg-orange-950/40 border border-orange-100 dark:border-orange-900 px-4 py-3">
            <div class="flex items-center gap-1.5 mb-2">
                <div class="w-2 h-2 rounded-full bg-orange-400"></div>
                <span class="text-[11px] font-medium text-orange-700 dark:text-orange-400">Tidak Lengkap</span>
            </div>
            <div class="text-2xl font-bold text-orange-700 dark:text-orange-300 leading-none">
                {{ $this->totalPartial }}
            </div>
            <div class="text-[10px] text-orange-600/70 dark:text-orange-500 mt-1">
                {{ $this->tidakLengkapPercent }}% dari total
            </div>
        </div>

        <div class="rounded-xl bg-red-50 dark:bg-red-950/40 border border-red-100 dark:border-red-900 px-4 py-3">
            <div class="flex items-center gap-1.5 mb-2">
                <div class="w-2 h-2 rounded-full bg-red-400"></div>
                <span class="text-[11px] font-medium text-red-700 dark:text-red-400">Tidak Hadir</span>
            </div>
            <div class="text-2xl font-bold text-red-700 dark:text-red-300 leading-none">
                {{ $this->totalAbsent }}
            </div>
            <div class="text-[10px] text-red-600/70 dark:text-red-500 mt-1">
                {{ $this->tidakHadirPercent }}% dari total
            </div>
        </div>

        <div class="rounded-xl bg-blue-50 dark:bg-blue-950/40 border border-blue-100 dark:border-blue-900 px-4 py-3">
            <div class="flex items-center gap-1.5 mb-2">
                <div class="w-2 h-2 rounded-full bg-blue-400"></div>
                <span class="text-[11px] font-medium text-blue-700 dark:text-blue-400">Izin / Cuti</span>
            </div>
            <div class="text-2xl font-bold text-blue-700 dark:text-blue-300 leading-none">
                {{ $this->totalIzinCuti }}
            </div>
            <div class="text-[10px] text-blue-600/70 dark:text-blue-500 mt-1">
                {{ $this->izinCutiPercent }}% dari total
            </div>
        </div>
    </div>
</flux:card>
