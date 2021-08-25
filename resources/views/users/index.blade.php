<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-10">
                <a href="{{ route('users.create') }}" class="bg-gray-500 hover:bg-gray-700 font-bold py-2 px-4 rounded">
                    + Create User
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
