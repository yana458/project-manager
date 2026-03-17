<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Clients</h2>

            @can('clients.create')
                <a href="{{ route('clients.create') }}" class="rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                    Create client
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 rounded-xl bg-green-50 p-4 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 rounded-xl bg-red-50 p-4 text-sm text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white shadow rounded-xl p-4 mb-4">
                <form method="GET" action="{{ route('clients.index') }}" class="flex gap-2">
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Search by name, company, email, tax ID or phone"
                        class="w-full rounded-xl border-gray-200 bg-white"
                    >

                    <button class="rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                        Search
                    </button>
                </form>
            </div>

            <div class="bg-white shadow rounded-xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="text-left p-3">Name</th>
                            <th class="text-left p-3">Company</th>
                            <th class="text-left p-3">Email</th>
                            <th class="text-left p-3">Tax ID</th>
                            <th class="text-left p-3">Status</th>
                            <th class="text-right p-3">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($clients as $client)
                            <tr class="border-t">
                                <td class="p-3">{{ $client->name }}</td>
                                <td class="p-3">{{ $client->company ?: '-' }}</td>
                                <td class="p-3">{{ $client->email ?: '-' }}</td>
                                <td class="p-3">{{ $client->tax_id }}</td>
                                <td class="p-3">
                                    @if($client->is_active)
                                        <span class="rounded-full bg-green-100 px-2 py-1 text-xs">active</span>
                                    @else
                                        <span class="rounded-full bg-gray-100 px-2 py-1 text-xs">inactive</span>
                                    @endif
                                </td>
                                <td class="p-3 text-right space-x-2">
                                    @can('clients.view')
                                        <a class="underline" href="{{ route('clients.show', $client) }}">View</a>
                                    @endcan

                                    @can('clients.edit')
                                        <a class="underline" href="{{ route('clients.edit', $client) }}">Edit</a>
                                    @endcan

                                    @can('clients.deactivate')
                                        @if($client->is_active)
                                            <form class="inline" method="POST" action="{{ route('clients.deactivate', $client) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button class="underline text-red-700"
                                                        onclick="return confirm('Deactivate this client?')">
                                                    Deactivate
                                                </button>
                                            </form>
                                        @else
                                            <form class="inline" method="POST" action="{{ route('clients.activate', $client) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button class="underline text-green-700"
                                                        onclick="return confirm('Activate this client?')">
                                                    Activate
                                                </button>
                                            </form>
                                        @endif
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-6 text-center text-gray-500">
                                    No clients found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="p-4">
                    {{ $clients->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>