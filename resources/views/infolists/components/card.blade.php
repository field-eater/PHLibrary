
    {{-- Content --}}
    <div class="grid grid-cols-2 gap-y-2">

        <div class="grid grid-flow-row-dense place-items-center gap-x-1">
            <span class="text-lg font-bold truncate col-span"> {{ $getRecord()->first_name }} {{ $getRecord()->last_name }}</span>
            <span class="inline-flex text-xs items-center text-gray-400 ">
                <x-heroicon-c-at-symbol class="h-4 w-4 "/>{{ $getRecord()->user_name }}
            </span>
        </div>

        @if (isset($getRecord()->student->student_number) )
        <x-filament::badge icon="heroicon-c-identification" size="lg" class="w-1/2">

        {{ $getRecord()->student->student_number }}


        </x-filament::badge>
        @endif


    </div>



    {{-- <div class="gap-2">
        {{ $getChildComponentContainer()}}
    </div> --}}

