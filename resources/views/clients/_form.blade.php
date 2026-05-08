@php
    $clientServices = isset($client) && $client ? $client->services : collect();
    $social = $client->social_links ?? [];

    $selectedServiceIds = old(
        'service_ids',
        $clientServices->pluck('id')->toArray()
    );

    $customSocials = old(
        'custom_socials',
        collect($social)
            ->except(['instagram', 'facebook', 'linkedin', 'tiktok', 'x'])
            ->map(fn ($url, $name) => ['name' => $name, 'url' => $url])
            ->values()
            ->toArray()
    );

    $customRows = max(3, count($customSocials));
@endphp

<div>
    <label class="block text-sm font-medium text-gray-700">Nombre</label>
    <input
        name="name"
        value="{{ old('name', $client->name ?? '') }}"
        class="mt-1 w-full rounded-xl border-gray-200 bg-white"
        required
    >
    @error('name')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div>
    <label class="block text-sm font-medium text-gray-700">Empresa</label>
    <input
        name="company"
        value="{{ old('company', $client->company ?? '') }}"
        class="mt-1 w-full rounded-xl border-gray-200 bg-white"
    >
    @error('company')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
        <label class="block text-sm font-medium text-gray-700">Teléfono</label>
        <input
            name="phone"
            value="{{ old('phone', $client->phone ?? '') }}"
            class="mt-1 w-full rounded-xl border-gray-200 bg-white"
        >
        @error('phone')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Correo electrónico</label>
        <input
            type="email"
            name="email"
            value="{{ old('email', $client->email ?? '') }}"
            class="mt-1 w-full rounded-xl border-gray-200 bg-white"
        >
        @error('email')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<div>
    <label class="block text-sm font-medium text-gray-700">Dirección</label>
    <input
        name="address"
        value="{{ old('address', $client->address ?? '') }}"
        class="mt-1 w-full rounded-xl border-gray-200 bg-white"
    >
    @error('address')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
        <label class="block text-sm font-medium text-gray-700">NIF / CIF</label>
        <input
            name="tax_id"
            value="{{ old('tax_id', $client->tax_id ?? '') }}"
            class="mt-1 w-full rounded-xl border-gray-200 bg-white"
            required
        >
        @error('tax_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">WhatsApp</label>
        <input
            name="whatsapp"
            value="{{ old('whatsapp', $client->whatsapp ?? '') }}"
            class="mt-1 w-full rounded-xl border-gray-200 bg-white"
        >
        @error('whatsapp')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<div>
    <label class="block text-sm font-medium text-gray-700">Web</label>
    <input
        name="website_url"
        value="{{ old('website_url', $client->website_url ?? '') }}"
        class="mt-1 w-full rounded-xl border-gray-200 bg-white"
        placeholder="https://..."
    >
    @error('website_url')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div class="border-t pt-4">
    <div class="mb-3">
        <h3 class="text-base font-semibold text-gray-900">Servicios contratados</h3>
        <p class="mt-1 text-sm text-gray-500">
            Selecciona los servicios que tiene contratados este cliente.
        </p>
    </div>

    <select
        name="service_ids[]"
        multiple
        class="mt-1 w-full min-h-[180px] rounded-xl border-gray-200 bg-white"
    >
        @foreach($servicesCatalog as $service)
            <option value="{{ $service->id }}" @selected(in_array($service->id, $selectedServiceIds))>
                {{ $service->name }}
                @if($service->category)
                    - {{ $service->category }}
                @endif
                @if($service->sub_category)
                    - {{ $service->sub_category }}
                @endif
            </option>
        @endforeach
    </select>

    <p class="mt-2 text-xs text-gray-500">
        Puedes seleccionar varios servicios manteniendo pulsada la tecla Ctrl o Cmd.
    </p>

    @error('service_ids')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror

    @error('service_ids.*')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div class="border-t pt-4">
    <div class="mb-3">
        <h3 class="text-base font-semibold text-gray-900">Redes sociales</h3>
        <p class="mt-1 text-sm text-gray-500">
            Añade las redes principales y, si hace falta, otras redes sociales personalizadas.
        </p>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-gray-700">Instagram</label>
            <input
                name="instagram"
                value="{{ old('instagram', $social['instagram'] ?? '') }}"
                class="mt-1 w-full rounded-xl border-gray-200 bg-white"
                placeholder="https://instagram.com/..."
            >
            @error('instagram')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Facebook</label>
            <input
                name="facebook"
                value="{{ old('facebook', $social['facebook'] ?? '') }}"
                class="mt-1 w-full rounded-xl border-gray-200 bg-white"
                placeholder="https://facebook.com/..."
            >
            @error('facebook')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">LinkedIn</label>
            <input
                name="linkedin"
                value="{{ old('linkedin', $social['linkedin'] ?? '') }}"
                class="mt-1 w-full rounded-xl border-gray-200 bg-white"
                placeholder="https://linkedin.com/..."
            >
            @error('linkedin')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">TikTok</label>
            <input
                name="tiktok"
                value="{{ old('tiktok', $social['tiktok'] ?? '') }}"
                class="mt-1 w-full rounded-xl border-gray-200 bg-white"
                placeholder="https://tiktok.com/..."
            >
            @error('tiktok')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700">X / Twitter</label>
            <input
                name="x"
                value="{{ old('x', $social['x'] ?? '') }}"
                class="mt-1 w-full rounded-xl border-gray-200 bg-white"
                placeholder="https://x.com/..."
            >
            @error('x')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="mt-4 rounded-xl bg-gray-50 p-4">
        <h4 class="text-sm font-semibold text-gray-900">Otras redes sociales</h4>
        <p class="mt-1 text-xs text-gray-500">
            Puedes rellenar una o varias si el cliente usa redes que no aparecen arriba.
        </p>

        <div class="mt-3 space-y-3">
            @for($i = 0; $i < $customRows; $i++)
                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombre de la red</label>
                        <input
                            name="custom_socials[{{ $i }}][name]"
                            value="{{ $customSocials[$i]['name'] ?? '' }}"
                            class="mt-1 w-full rounded-xl border-gray-200 bg-white"
                            placeholder="Ej. YouTube"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">URL</label>
                        <input
                            name="custom_socials[{{ $i }}][url]"
                            value="{{ $customSocials[$i]['url'] ?? '' }}"
                            class="mt-1 w-full rounded-xl border-gray-200 bg-white"
                            placeholder="https://..."
                        >
                    </div>
                </div>
            @endfor
        </div>

        @error('custom_socials')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
        @error('custom_socials.*.name')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
        @error('custom_socials.*.url')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<div>
    <label class="block text-sm font-medium text-gray-700">Notas</label>
    <textarea
        name="notes"
        rows="4"
        class="mt-1 w-full rounded-xl border-gray-200 bg-white"
    >{{ old('notes', $client->notes ?? '') }}</textarea>
    @error('notes')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>