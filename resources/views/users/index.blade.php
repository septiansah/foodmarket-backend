<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-10">
                <a href="{{ route('users.create') }}" class="bg-green-500 hover:bg-green-700 font-bold py-2 px-4 rounded">
                    + Create User
                </a>
            </div>
            <div class="bg-white">
                <table class="table-auto w-full">
                    <thead>
                        <tr>
                            <td class="border px-6 py-4">ID</td>
                            <td class="border px-6 py-4">Name</td>
                            <td class="border px-6 py-4">Email</td>
                            <td class="border px-6 py-4">Roles</td>
                            <td class="border px-6 py-4">Action</td>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($user as $item)
                        <tr>
                            <td class="border px-6 py-4">{{ $item->id }}</td>
                            <td class="border px-6 py-4">{{ $item->name }}</td>
                            <td class="border px-6 py-4">{{ $item->email }}</td>
                            <td class="border px-6 py-4">{{ $item->roles }}</td>
                            <td class="border px-6 py-4 text-center">
                                <a href="{{ route('users.edit', $item->id) }}" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 mx-2 rounded">
                                Edit
                                </a>
                            </td>
                        </tr>
                        @empty

                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</x-app-layout>
