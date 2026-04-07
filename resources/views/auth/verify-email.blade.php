<x-guest-layout>
    <div class="mb-4 rounded-xl bg-gray-50 p-4 text-sm text-gray-600">
        Gracias por registrarte. Antes de empezar, necesitamos que verifiques tu dirección de correo electrónico haciendo clic en el enlace que te acabamos de enviar. Si no lo has recibido, puedes solicitar otro sin problema.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 rounded-xl bg-green-50 p-4 text-sm font-medium text-green-700">
            Se ha enviado un nuevo enlace de verificación al correo electrónico que indicaste durante el registro.
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between gap-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    Reenviar correo de verificación
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button
                type="submit"
                class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
                Cerrar sesión
            </button>
        </form>
    </div>
</x-guest-layout>