<div class="flex flex-wrap gap-3">
    @foreach($actions as $action)
        <a href="{{ $action['url'] }}"
           class="rounded-xl border px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
            {{ $action['label'] }}
        </a>
    @endforeach
</div>