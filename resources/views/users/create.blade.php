<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {!! __('User &raquo; Create') !!}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div>
                @if($errors->any())
                <div class="bg-red-500 text-white font-bold rounded-t px-4 py-2">
                    There's something wrong
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
