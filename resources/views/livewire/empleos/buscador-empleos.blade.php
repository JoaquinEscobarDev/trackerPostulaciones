<div>
    <form wire:submit="buscar" class="flex flex-col sm:flex-row gap-3 max-w-2xl mx-auto">
        <input
            type="text"
            wire:model="query"
            placeholder="{{ __('Ej: programador web junior, diseñador UX...') }}"
            class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base px-4 py-3"
        />
        <button
            type="submit"
            class="inline-flex items-center justify-center px-6 py-3 bg-gray-900 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition"
        >
            <span wire:loading.remove wire:target="buscar,irAPagina">{{ __('Buscar') }}</span>
            <span wire:loading wire:target="buscar,irAPagina">{{ __('Buscando...') }}</span>
        </button>
    </form>

    <p class="text-center text-xs text-gray-400 mt-2">
        {{ __('Empleos reales de Chile, todos los rubros, vía') }}
        <a href="https://www.opcionempleo.cl" target="_blank" rel="noopener noreferrer" class="underline">Careerjet</a>
    </p>

    <div class="mt-10 max-w-4xl mx-auto" wire:loading.class="opacity-50" wire:target="buscar,irAPagina">
        @if ($query !== '' && $empleos->isEmpty())
            <p class="text-center text-gray-500 py-10">
                {{ __('No encontramos empleos para ":query". Probá con otra palabra clave.', ['query' => $query]) }}
            </p>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($empleos as $empleo)
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition flex flex-col">
                    <div class="flex items-start gap-3">
                        @if ($empleo['logo'])
                            <img src="{{ $empleo['logo'] }}" alt="" class="w-10 h-10 rounded object-contain border border-gray-100 shrink-0" loading="lazy">
                        @endif
                        <div class="min-w-0">
                            <h3 class="font-semibold text-gray-900 truncate">{{ $empleo['titulo'] }}</h3>
                            <p class="text-sm text-gray-600 truncate">{{ $empleo['empresa'] }}</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-1.5 mt-3">
                        @if ($empleo['remoto'])
                            <span class="px-2 py-0.5 text-xs font-medium bg-emerald-50 text-emerald-700 rounded-full">{{ __('Remoto') }}</span>
                        @endif
                        @if ($empleo['seniority'])
                            <span class="px-2 py-0.5 text-xs font-medium bg-indigo-50 text-indigo-700 rounded-full">{{ $empleo['seniority'] }}</span>
                        @endif
                        @foreach ($empleo['paises'] as $pais)
                            <span class="px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">{{ $pais }}</span>
                        @endforeach
                    </div>

                    @if ($empleo['salario_min'] || $empleo['salario_max'])
                        <p class="text-sm text-gray-500 mt-2">
                            ${{ number_format($empleo['salario_min'] ?? 0) }}
                            @if ($empleo['salario_max'])
                                &ndash; ${{ number_format($empleo['salario_max']) }}
                            @endif
                        </p>
                    @endif

                    <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between gap-2">
                        <a
                            href="{{ $empleo['url'] }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-sm text-gray-500 hover:text-gray-700"
                        >
                            {{ __('Ver detalle') }} &rarr;
                        </a>

                        <a
                            href="{{ route('empleos.postular', [
                                'empresa' => $empleo['empresa'],
                                'cargo' => $empleo['titulo'],
                                'link_vacante' => $empleo['url'],
                            ]) }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-900 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition"
                        >
                            {{ __('Postular') }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($totalPaginas > 1)
            <div class="flex items-center justify-center gap-4 mt-8">
                <button
                    type="button"
                    wire:click="irAPagina({{ $pagina - 1 }})"
                    @disabled($pagina <= 1)
                    class="px-3 py-1.5 text-sm border border-gray-300 rounded-md disabled:opacity-40 hover:bg-gray-50"
                >
                    {{ __('Anterior') }}
                </button>
                <span class="text-sm text-gray-500">{{ __('Página :actual de :total', ['actual' => $pagina, 'total' => $totalPaginas]) }}</span>
                <button
                    type="button"
                    wire:click="irAPagina({{ $pagina + 1 }})"
                    @disabled($pagina >= $totalPaginas)
                    class="px-3 py-1.5 text-sm border border-gray-300 rounded-md disabled:opacity-40 hover:bg-gray-50"
                >
                    {{ __('Siguiente') }}
                </button>
            </div>
        @endif
    </div>
</div>
