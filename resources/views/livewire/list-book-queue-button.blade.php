<div>
    <x-filament::modal alignment="center" class="" slide-over icon="heroicon-c-rectangle-stack">
        <x-slot name="trigger">
            <x-filament::link icon="heroicon-c-rectangle-stack">

            </x-filament::link>
        </x-slot>
        @if ($record->bookqueues->count() > 0)
        <x-slot name="heading" class="">
            {{ $record->book_name }}
        </x-slot>

        <x-slot name="description">
            <strong>{{$record->bookqueues->count()}}</strong> queued
        </x-slot>
        <div>

            @foreach ($record->bookqueues as $queue)
            <div class="w-full flex justify-between gap-3 my-2 p-3 items-center">
                    <div class="flex gap-2 content-center">
                        <x-filament::avatar
                        class="self-center"
                        size="w-11 h-11"
                        src="{{ asset('storage/'.$queue->user->avatar) }}"
                        alt="{{ $queue->user->user_name }}'s avatar"/>
                    <div class="flex-row self-center">
                        <span class>{{ $queue->user->user_name }}</span>
                        <x-filament::badge >
                            {{ $queue->user->student->student_number }}
                        </x-filament::badge>
                    </div>
                    </div>
                    <x-filament::badge color="info">#{{ $queue->position }}</x-filament::badge>
            </div>


            @endforeach
        </div>




        @else
        <div class="flex justify-center items-center h-full">
           <div class="flex-col">
            <div class="flex justify-center items-center mb-3">
                <x-heroicon-o-x-circle class="h-12 w-12 text-gray-600 dark:text-gray-300 self-center"/>
            </div>
                <span class="text-2xl font-bold">No queues</span>
           </div>
        </div>


       @endif
    </x-filament::modal>
</div>
