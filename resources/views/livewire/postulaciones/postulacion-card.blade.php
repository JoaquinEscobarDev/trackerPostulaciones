<div class="bg-white rounded-md shadow-sm border border-gray-200 p-3 cursor-move hover:shadow-md transition group">
    <div class="flex items-start justify-between gap-2">
        <div class="min-w-0">
            <p class="font-semibold text-sm text-gray-900 truncate">{{ $postulacion->empresa }}</p>
            <p class="text-sm text-gray-600 truncate">{{ $postulacion->cargo }}</p>
        </div>

        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition shrink-0">
            <button
                type="button"
                wire:click="editar"
                title="{{ __('Editar / ver detalle') }}"
                class="p-1 text-gray-400 hover:text-gray-700"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                </svg>
            </button>
            <button
                type="button"
                wire:click="eliminar"
                wire:confirm="{{ __('¿Eliminar esta postulación? Esta acción no se puede deshacer.') }}"
                title="{{ __('Eliminar') }}"
                class="p-1 text-gray-400 hover:text-rose-600"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm4-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    </div>

    <div class="mt-2 flex items-center justify-between text-xs text-gray-500">
        <span>{{ $postulacion->fecha_postulacion->translatedFormat('d M Y') }}</span>

        @if ($postulacion->link_vacante)
            <a
                href="{{ $postulacion->link_vacante }}"
                target="_blank"
                rel="noopener noreferrer"
                wire:click.stop
                class="text-indigo-600 hover:text-indigo-800 font-medium"
            >
                {{ __('Ver vacante') }} &rarr;
            </a>
        @endif
    </div>
</div>
