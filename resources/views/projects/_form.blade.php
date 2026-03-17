<div>
    <label class="block text-sm font-medium text-gray-700">Client</label>
    <select name="client_id" class="mt-1 w-full rounded-xl border-gray-200 bg-white" required>
        <option value="">Select a client</option>
        @foreach($clients as $clientOption)
            <option value="{{ $clientOption->id }}" @selected(old('client_id', $project->client_id ?? '') == $clientOption->id)>
                {{ $clientOption->company }}{{ $clientOption->name ? ' - '.$clientOption->name : '' }}
            </option>
        @endforeach
    </select>
    @error('client_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm font-medium text-gray-700">Name</label>
    <input name="name" value="{{ old('name', $project->name ?? '') }}" class="mt-1 w-full rounded-xl border-gray-200 bg-white" required>
    @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm font-medium text-gray-700">Description</label>
    <textarea name="description" rows="4" class="mt-1 w-full rounded-xl border-gray-200 bg-white">{{ old('description', $project->description ?? '') }}</textarea>
    @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700">Status</label>
        <select name="status" class="mt-1 w-full rounded-xl border-gray-200 bg-white" required>
            @foreach($statuses as $projectStatus)
                <option value="{{ $projectStatus }}" @selected(old('status', $project->status ?? 'active') === $projectStatus)>
                    {{ ucfirst($projectStatus) }}
                </option>
            @endforeach
        </select>
        @error('status') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Start date</label>
        <input
            type="date"
            name="start_date"
            value="{{ old('start_date', isset($project) && $project->start_date ? $project->start_date->format('Y-m-d') : '') }}"
            class="mt-1 w-full rounded-xl border-gray-200 bg-white"
        >
        @error('start_date') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">End date</label>
        <input
            type="date"
            name="end_date"
            value="{{ old('end_date', isset($project) && $project->end_date ? $project->end_date->format('Y-m-d') : '') }}"
            class="mt-1 w-full rounded-xl border-gray-200 bg-white"
        >
        @error('end_date') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700">Repository URL</label>
        <input name="repo_url" value="{{ old('repo_url', $project->repo_url ?? '') }}" class="mt-1 w-full rounded-xl border-gray-200 bg-white">
        @error('repo_url') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Staging URL</label>
        <input name="staging_url" value="{{ old('staging_url', $project->staging_url ?? '') }}" class="mt-1 w-full rounded-xl border-gray-200 bg-white">
        @error('staging_url') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Production URL</label>
        <input name="production_url" value="{{ old('production_url', $project->production_url ?? '') }}" class="mt-1 w-full rounded-xl border-gray-200 bg-white">
        @error('production_url') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Docs URL</label>
        <input name="docs_url" value="{{ old('docs_url', $project->docs_url ?? '') }}" class="mt-1 w-full rounded-xl border-gray-200 bg-white">
        @error('docs_url') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
</div>