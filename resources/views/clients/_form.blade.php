<div>
    <label class="block text-sm font-medium text-gray-700">Name</label>
    <input name="name" value="{{ old('name', $client->name ?? '') }}" class="mt-1 w-full rounded-xl border-gray-200 bg-white" required>
    @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm font-medium text-gray-700">Company</label>
    <input name="company" value="{{ old('company', $client->company ?? '') }}" class="mt-1 w-full rounded-xl border-gray-200 bg-white">
    @error('company') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700">Phone</label>
        <input name="phone" value="{{ old('phone', $client->phone ?? '') }}" class="mt-1 w-full rounded-xl border-gray-200 bg-white">
        @error('phone') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Email</label>
        <input type="email" name="email" value="{{ old('email', $client->email ?? '') }}" class="mt-1 w-full rounded-xl border-gray-200 bg-white">
        @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

<div>
    <label class="block text-sm font-medium text-gray-700">Address</label>
    <input name="address" value="{{ old('address', $client->address ?? '') }}" class="mt-1 w-full rounded-xl border-gray-200 bg-white">
    @error('address') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700">Tax ID</label>
        <input name="tax_id" value="{{ old('tax_id', $client->tax_id ?? '') }}" class="mt-1 w-full rounded-xl border-gray-200 bg-white" required>
        @error('tax_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">WhatsApp</label>
        <input name="whatsapp" value="{{ old('whatsapp', $client->whatsapp ?? '') }}" class="mt-1 w-full rounded-xl border-gray-200 bg-white">
        @error('whatsapp') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

<div>
    <label class="block text-sm font-medium text-gray-700">Website URL</label>
    <input name="website_url" value="{{ old('website_url', $client->website_url ?? '') }}" class="mt-1 w-full rounded-xl border-gray-200 bg-white">
    @error('website_url') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
</div>

@php
    $social = old('social_links', $client->social_links ?? []);
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700">Instagram</label>
        <input name="instagram" value="{{ old('instagram', $social['instagram'] ?? '') }}" class="mt-1 w-full rounded-xl border-gray-200 bg-white">
        @error('instagram') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Facebook</label>
        <input name="facebook" value="{{ old('facebook', $social['facebook'] ?? '') }}" class="mt-1 w-full rounded-xl border-gray-200 bg-white">
        @error('facebook') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">LinkedIn</label>
        <input name="linkedin" value="{{ old('linkedin', $social['linkedin'] ?? '') }}" class="mt-1 w-full rounded-xl border-gray-200 bg-white">
        @error('linkedin') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">TikTok</label>
        <input name="tiktok" value="{{ old('tiktok', $social['tiktok'] ?? '') }}" class="mt-1 w-full rounded-xl border-gray-200 bg-white">
        @error('tiktok') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">X / Twitter</label>
        <input name="x" value="{{ old('x', $social['x'] ?? '') }}" class="mt-1 w-full rounded-xl border-gray-200 bg-white">
        @error('x') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

<div>
    <label class="block text-sm font-medium text-gray-700">Notes</label>
    <textarea name="notes" rows="4" class="mt-1 w-full rounded-xl border-gray-200 bg-white">{{ old('notes', $client->notes ?? '') }}</textarea>
    @error('notes') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
</div>