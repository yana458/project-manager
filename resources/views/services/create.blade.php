<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create service</h2>
            <a href="{{ route('services.index') }}" class="text-center rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">Back</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-xl p-6">
                <form method="POST" action="{{ route('services.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <input
                            name="name"
                            value="{{ old('name') }}"
                            class="mt-1 w-full rounded-xl border-gray-200 bg-white"
                            required
                        >
                        @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Category</label>
                        <input
                            name="category"
                            value="{{ old('category') }}"
                            list="categories-list"
                            class="mt-1 w-full rounded-xl border-gray-200 bg-white"
                            placeholder="Select or type a category"
                        >
                        <datalist id="categories-list">
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}">
                            @endforeach
                        </datalist>
                        @error('category') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Subcategory <span class="text-gray-400 font-normal">(optional)</span>
                        </label>
                        <input
                            name="sub_category"
                            value="{{ old('sub_category') }}"
                            list="subcategories-list"
                            class="mt-1 w-full rounded-xl border-gray-200 bg-white"
                            placeholder="Select or type a subcategory"
                        >
                        <datalist id="subcategories-list">
                            @foreach($subCategories as $sub)
                                <option value="{{ $sub }}">
                            @endforeach
                        </datalist>
                        @error('sub_category') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea
                            name="description"
                            rows="4"
                            class="mt-1 w-full rounded-xl border-gray-200 bg-white"
                            placeholder="Optional description..."
                        >{{ old('description') }}</textarea>
                        @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="pt-2 flex gap-2">
                        <button class="rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                            Create
                        </button>
                        <a href="{{ route('services.index') }}" class="rounded-xl px-4 py-2 text-sm border">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>