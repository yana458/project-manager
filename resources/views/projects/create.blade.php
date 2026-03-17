<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create project</h2>
            <a href="{{ route('projects.index') }}" class="text-center rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-xl p-6">
                <form method="POST" action="{{ route('projects.store') }}" class="space-y-4">
                    @csrf
                    @include('projects._form', ['project' => null])

                    <div class="pt-2 flex gap-2">
                        <button class="rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                            Create
                        </button>
                        <a href="{{ route('projects.index') }}" class="rounded-xl px-4 py-2 text-sm border">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>