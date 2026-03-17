<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Client detail</h2>
            <a href="{{ route('clients.index') }}" class="text-center rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">Back</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

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

            <div class="bg-white shadow rounded-xl p-6">
                <div class="flex items-start justify-between gap-4">
                    <div class="space-y-2">
                        <div><strong>Name:</strong> {{ $client->name }}</div>
                        <div><strong>Company:</strong> {{ $client->company ?: '-' }}</div>
                        <div><strong>Phone:</strong> {{ $client->phone ?: '-' }}</div>
                        <div><strong>Email:</strong> {{ $client->email ?: '-' }}</div>
                        <div><strong>Address:</strong> {{ $client->address ?: '-' }}</div>
                        <div><strong>Tax ID:</strong> {{ $client->tax_id }}</div>
                        <div><strong>WhatsApp:</strong> {{ $client->whatsapp ?: '-' }}</div>

                        <div>
                            <strong>Website:</strong>
                            @if($client->website_url)
                                <a href="{{ $client->website_url }}" target="_blank" class="underline text-blue-600">
                                    {{ $client->website_url }}
                                </a>
                            @else
                                -
                            @endif
                        </div>

                        <div class="pt-2">
                            <strong>Status:</strong>
                            @if($client->is_active)
                                <span class="ml-2 rounded-full bg-green-100 px-2 py-1 text-xs">active</span>
                            @else
                                <span class="ml-2 rounded-full bg-gray-100 px-2 py-1 text-xs">inactive</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-col gap-2 min-w-[180px]">
                        @can('clients.edit')
                            <a href="{{ route('clients.edit', $client) }}"
                               class="text-center rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                                Edit
                            </a>
                        @endcan

                        @can('clients.deactivate')
                            @if($client->is_active)
                                <form method="POST" action="{{ route('clients.deactivate', $client) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="w-full rounded-xl border border-red-200 px-4 py-2 text-sm text-red-700"
                                            onclick="return confirm('Deactivate this client?')">
                                        Deactivate
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('clients.activate', $client) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="w-full rounded-xl border border-green-200 px-4 py-2 text-sm text-green-700"
                                            onclick="return confirm('Activate this client?')">
                                        Activate
                                    </button>
                                </form>
                            @endif
                        @endcan
                    </div>
                </div>

                <div class="mt-6 border-t pt-4">
                    <h3 class="text-base font-semibold text-gray-900">Social links</h3>

                    @php
                        $social = $client->social_links ?? [];
                    @endphp

                    @if(!empty($social))
                        <div class="mt-3 space-y-2">
                            @foreach($social as $platform => $url)
                                <div>
                                    <strong>{{ ucfirst($platform) }}:</strong>
                                    <a href="{{ $url }}" target="_blank" class="underline text-blue-600">
                                        {{ $url }}
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="mt-2 text-sm text-gray-500">No social links registered.</p>
                    @endif
                </div>

                <div class="mt-6 border-t pt-4">
                    <h3 class="text-base font-semibold text-gray-900">Notes</h3>
                    <p class="mt-2 text-sm text-gray-700 whitespace-pre-line">
                        {{ $client->notes ?: 'No notes.' }}
                    </p>
                </div>

                <div class="mt-6 border-t pt-4">
                    <h3 class="text-base font-semibold text-gray-900">Projects</h3>

                    {{-- @if($client->projects->count()) --}}
                        {{-- <ul class="mt-3 space-y-2">
                            @foreach($client->projects as $project)
                                <li class="rounded-xl border p-3">
                                    {{ $project->name }}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="mt-2 text-sm text-gray-500">No associated projects yet.</p>
                    @endif --}}
                    <p class="mt-2 text-sm text-gray-500">
                        This section will be added in the projects integration.
                    </p>
                </div>

                <div class="mt-6 border-t pt-4">
                    <h3 class="text-base font-semibold text-gray-900">Contracted services</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        This section will be added in the client_services integration.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>