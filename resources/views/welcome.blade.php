<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased font-sans bg-gray-50 text-gray-900">
        <header class="border-b border-gray-200 bg-white">
            <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <x-application-logo class="h-8 w-auto fill-current text-gray-800" />
                    <span class="font-semibold text-gray-800">{{ config('app.name') }}</span>
                </a>

                <livewire:welcome.navigation />
            </div>
        </header>

        <main>
            <section class="max-w-6xl mx-auto px-6 pt-16 pb-10 text-center">
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 max-w-2xl mx-auto">
                    Ordená tu búsqueda de trabajo y no dejes ninguna postulación en el aire
                </h1>
                <p class="mt-4 text-gray-600 max-w-xl mx-auto">
                    Buscá vacantes reales, postulá, y llevá el seguimiento de todo en un Kanban
                    con recordatorios automáticos cuando una postulación se queda sin respuesta.
                </p>
            </section>

            <section class="max-w-6xl mx-auto px-6 pb-20">
                <livewire:empleos.buscador-empleos />
            </section>

            <section class="bg-white border-t border-gray-200">
                <div class="max-w-6xl mx-auto px-6 py-16 grid grid-cols-1 sm:grid-cols-3 gap-8 text-center">
                    <div>
                        <h3 class="font-semibold text-gray-900">Kanban con drag & drop</h3>
                        <p class="mt-2 text-sm text-gray-600">
                            Postulado, Entrevista, Oferta, Rechazado. Arrastrá una tarjeta y el
                            estado se actualiza al instante.
                        </p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Recordatorios automáticos</h3>
                        <p class="mt-2 text-sm text-gray-600">
                            Si una postulación queda sin novedades por varios días, te llega un
                            email para que hagas seguimiento.
                        </p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Exportá tu historial</h3>
                        <p class="mt-2 text-sm text-gray-600">
                            Todas tus postulaciones a CSV o PDF cuando las necesites.
                        </p>
                    </div>
                </div>
            </section>
        </main>

        <footer class="py-8 text-center text-sm text-gray-400">
            {{ config('app.name') }}
        </footer>
    </body>
</html>
