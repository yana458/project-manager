<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-semibold text-gray-900">Restablecer contraseña</h1>
        <p class="mt-2 text-sm text-gray-600">
            Introduce tu correo electrónico y define una nueva contraseña para acceder de nuevo a tu cuenta.
        </p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <x-input-label for="email" value="Correo electrónico" />
            <x-text-input
                id="email"
                class="mt-1 block w-full"
                type="email"
                name="email"
                :value="old('email', $request->email)"
                required
                autofocus
                autocomplete="username"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Nueva contraseña" />
            <x-text-input
                id="password"
                class="mt-1 block w-full"
                type="password"
                name="password"
                required
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Confirmar nueva contraseña" />
            <x-text-input
                id="password_confirmation"
                class="mt-1 block w-full"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end pt-2">
            <x-primary-button>
                Restablecer contraseña
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>