<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Services</h2>

            @can('services.create')
                <a href="{{ route('services.create') }}" class="rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                    Create service
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

            {{-- Filters --}}
            <form method="GET" action="{{ route('services.index') }}" class="mb-4 flex flex-wrap gap-2">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search by name..."
                    class="rounded-xl border-gray-200 text-sm px-3 py-2"
                >

                <select name="category" class="rounded-xl border-gray-200 text-sm px-3 py-2">
                    <option value="">All categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" @selected(request('category') === $cat)>
                            {{ $cat }}
                        </option>
                    @endforeach
                </select>

                <select name="sub_category" class="rounded-xl border-gray-200 text-sm px-3 py-2">
                    <option value="">All subcategories</option>
                    @foreach($subCategories as $sub)
                        <option value="{{ $sub }}" @selected(request('sub_category') === $sub)>
                            {{ $sub }}
                        </option>
                    @endforeach
                </select>

                <select name="is_active" class="rounded-xl border-gray-200 text-sm px-3 py-2">
                    <option value="">All statuses</option>
                    <option value="1" @selected(request('is_active') === '1')>Active</option>
                    <option value="0" @selected(request('is_active') === '0')>Inactive</option>
                </select>

                <button type="submit" class="rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                    Filter
                </button>

                @if(request()->hasAny(['search', 'category', 'sub_category', 'is_active']))
                    <a href="{{ route('services.index') }}" class="rounded-xl border border-gray-200 px-4 py-2 text-sm text-gray-600">
                        Clear
                    </a>
                @endif
            </form>

            <div class="bg-white shadow rounded-xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="text-left p-3">Name</th>
                            <th class="text-left p-3">Category</th>
                            <th class="text-left p-3">Subcategory</th>
                            <th class="text-left p-3">Status</th>
                            <th class="text-right p-3">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($services as $service)
                            <tr class="border-t">
                                <td class="p-3">{{ $service->name }}</td>
                                <td class="p-3">{{ $service->category ?? '—' }}</td>
                                <td class="p-3">{{ $service->sub_category ?? '—' }}</td>
                                <td class="p-3">
                                    @if($service->is_active)
                                        <span class="rounded-full bg-green-100 px-2 py-1 text-xs">Active</span>
                                    @else
                                        <span class="rounded-full bg-gray-100 px-2 py-1 text-xs">Inactive</span>
                                    @endif
                                </td>

                                <td class="p-3 text-right space-x-2">
                                    <a class="underline" href="{{ route('services.show', $service) }}">View</a>

                                    @can('services.edit')
                                        <a class="underline" href="{{ route('services.edit', $service) }}">Edit</a>
                                    @endcan

                                    @can('services.deactivate')
                                        @if($service->is_active)
                                            <form class="inline" method="POST" action="{{ route('services.destroy', $service) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    class="underline text-red-700"
                                                    onclick="return confirm('Deactivate this service?')">
                                                    Deactivate
                                                </button>
                                            </form>
                                        @else
                                            <form class="inline" method="POST" action="{{ route('services.restore', $service) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button
                                                    class="underline text-green-700"
                                                    onclick="return confirm('Activate this service?')">
                                                    Activate
                                                </button>
                                            </form>
                                        @endif
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-6 text-center text-gray-400 text-sm">
                                    No services found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="p-4">
                    {{ $services->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>