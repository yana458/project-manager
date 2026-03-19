<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Service detail</h2>
            <a href="{{ route('services.index') }}"
               class="text-center rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

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

                    {{-- Main data --}}
                    <div class="space-y-2">
                        <div><strong>Name:</strong> {{ $service->name }}</div>

                        <div>
                            <strong>Category:</strong>
                            {{ $service->category ?? '—' }}
                        </div>

                        <div>
                            <strong>Subcategory:</strong>
                            {{ $service->sub_category ?? '—' }}
                        </div>

                        <div class="pt-2">
                            <strong>Status:</strong>
                            @if($service->is_active)
                                <span class="ml-2 rounded-full bg-green-100 px-2 py-1 text-xs">Active</span>
                            @else
                                <span class="ml-2 rounded-full bg-gray-100 px-2 py-1 text-xs">Inactive</span>
                            @endif
                        </div>

                        <div class="pt-1 text-xs text-gray-400">
                            Created: {{ $service->created_at->format('d/m/Y H:i') }}
                            &middot;
                            Updated: {{ $service->updated_at->diffForHumans() }}
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-col gap-2 min-w-[180px]">
                        @can('update', $service)
                            <a href="{{ route('services.edit', $service) }}"
                               class="text-center rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                                Edit
                            </a>
                        @endcan

                        @can('delete', $service)
                            @if($service->is_active)
                                <form method="POST" action="{{ route('services.destroy', $service) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="w-full rounded-xl border border-red-200 px-4 py-2 text-sm text-red-700"
                                            onclick="return confirm('Deactivate this service?')">
                                        Deactivate
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('services.restore', $service) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="w-full rounded-xl border border-green-200 px-4 py-2 text-sm text-green-700"
                                            onclick="return confirm('Activate this service?')">
                                        Activate
                                    </button>
                                </form>
                            @endif
                        @endcan
                    </div>
                </div>

                {{-- Description --}}
                <div class="mt-6 border-t pt-4">
                    <div class="text-sm font-semibold mb-2">Description</div>
                    @if($service->description)
                        <p class="text-sm text-gray-700 whitespace-pre-line">{{ $service->description }}</p>
                    @else
                        <p class="text-sm text-gray-400 italic">No description recorded.</p>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>