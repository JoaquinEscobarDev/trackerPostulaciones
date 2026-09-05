<nav class="-mx-3 flex flex-1 justify-end items-center gap-1">
    @auth
        <a
            href="{{ route('postulaciones') }}"
            class="rounded-md px-3 py-2 text-sm font-semibold text-white bg-gray-900 ring-1 ring-transparent transition hover:bg-gray-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900"
        >
            Mis postulaciones
        </a>
    @else
        <a
            href="{{ route('login') }}"
            class="rounded-md px-3 py-2 text-sm text-gray-700 ring-1 ring-transparent transition hover:text-gray-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900"
        >
            Iniciar sesión
        </a>

        @if (Route::has('register'))
            <a
                href="{{ route('register') }}"
                class="rounded-md px-3 py-2 text-sm font-semibold text-white bg-gray-900 ring-1 ring-transparent transition hover:bg-gray-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900"
            >
                Registrarme
            </a>
        @endif
    @endauth
</nav>
