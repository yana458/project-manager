<div class="rounded-2xl border bg-white p-6 shadow-sm">
    <div class="mb-4">
        <h3 class="text-base font-semibold text-gray-900">{{ $title }}</h3>
        @isset($subtitle)
            <p class="mt-1 text-sm text-gray-500">{{ $subtitle }}</p>
        @endisset
    </div>

    {{ $slot }}
</div>