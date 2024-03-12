<x-filament-widgets::widget>
    <x-filament::section class="py-1">
        <div class="flex justify-between content-center text-sm  font-bold my-1">
            <span wire:poll.visible.1s="refreshTime" class="font-technology text-3xl">{{ $currentTime }}</span>
            <div class="grid">
                <span wire:poll.visible.86400s="refreshDate" class="font-beba-neue self-center">{{ $currentDate }}</span>
                <span wire:poll.visible.86400s="refreshDay" class="font-beba-neue self-center font-thin text-xs text-gray-400">{{ $currentDay }}</span>
            </div>
        </div>
    </x-filament::section>
    <script>
        setInterval(function() {
            Livewire.emit('refreshTime');
        }, 1000); // Update every second
    </script>
</x-filament-widgets::widget>




