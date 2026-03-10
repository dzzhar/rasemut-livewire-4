<flux:modal name="feedback-modal" class="w-52 md:w-96">
    <div class="space-y-6">
        <flux:icon
            :name="$type === 'success' ? 'check-circle' : ($type === 'warning' ? 'exclamation-circle' : 'x-circle')"
            variant="solid" @class([
                'rounded-full size-16 flex items-center justify-center mx-auto',
                'text-green-800 bg-green-400/20 dark:text-green-200 dark:bg-green-400/40' =>
                    $type === 'success',
                'text-yellow-700 bg-yellow-400/20 dark:text-yellow-200 dark:bg-yellow-400/40' =>
                    $type === 'warning',
                'text-red-700 bg-red-400/20 dark:text-red-200 dark:bg-red-400/40' =>
                    $type === 'error',
            ]) />

        <div class="text-center">
            <flux:heading size="lg">{{ $title }}</flux:heading>
            <flux:text class="mt-2">{{ $message }}</flux:text>
        </div>

        <flux:button variant="primary" class="w-full" x-on:click="$flux.modals().close()">
            Tutup
        </flux:button>
    </div>
</flux:modal>
