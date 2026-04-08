<div class="rounded-2xl border bg-white p-5 shadow-sm">
    <div class="text-sm font-medium text-gray-500">{{ $title }}</div>
    <div class="mt-2 text-3xl font-semibold text-gray-900">{{ $value }}</div>
    @isset($hint)
        <div class="mt-2 text-sm text-gray-500">{{ $hint }}</div>
    @endisset
</div>