<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Crear servicio</h2>
            <a href="{{ route('services.index') }}" class="text-center rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-xl p-6">
                <div class="mb-6 rounded-xl bg-gray-50 p-4 text-sm text-gray-600">
                    Añade un nuevo servicio al catálogo general. Podrás asignarlo después a clientes y proyectos.
                </div>

                <form method="POST" action="{{ route('services.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombre</label>
                        <input
                            name="name"
                            value="{{ old('name') }}"
                            class="mt-1 w-full rounded-xl border-gray-200 bg-white"
                            required
                        >
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Categoría</label>
                        <select
                            name="category"
                            class="mt-1 w-full rounded-xl border-gray-200 bg-white"
                        >
                            <option value="">Selecciona una categoría</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" @selected(old('category') === $cat)>
                                    {{ $cat }}
                                </option>
                            @endforeach
                        </select>
                        @error('category')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Subcategoría <span class="text-gray-400 font-normal">(opcional)</span>
                        </label>
                        <select
                            name="sub_category"
                            class="mt-1 w-full rounded-xl border-gray-200 bg-white"
                        >
                            <option value="">Selecciona una subcategoría</option>
                            @foreach($subCategories as $sub)
                                <option value="{{ $sub }}" @selected(old('sub_category') === $sub)>
                                    {{ $sub }}
                                </option>
                            @endforeach
                        </select>
                        @error('sub_category')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Descripción</label>
                        <textarea
                            name="description"
                            rows="4"
                            class="mt-1 w-full rounded-xl border-gray-200 bg-white"
                            placeholder="Descripción opcional..."
                        >{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-2 flex gap-2">
                        <button class="rounded-xl bg-gray-900 px-4 py-2 text-sm text-white">
                            Crear servicio
                        </button>
                        <a href="{{ route('services.index') }}" class="rounded-xl px-4 py-2 text-sm border">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>