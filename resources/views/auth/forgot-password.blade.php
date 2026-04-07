<x-guest-layout>
    <div class="mb-4 rounded-xl bg-gray-50 p-4 text-sm text-gray-600">
        ¿Has olvidado tu contraseña? No pasa nada. Indica tu dirección de correo electrónico y te enviaremos un enlace para restablecerla y poder elegir una nueva.
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="email" value="Correo electrónico" />
            <x-text-input
                id="email"
                class="mt-1 block w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end">
            <x-primary-button>
                Enviar enlace de recuperación
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>