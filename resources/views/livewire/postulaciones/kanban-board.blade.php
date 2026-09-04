<div>
    <div class="flex justify-end mb-4">
        <button
            type="button"
            wire:click="abrirFormularioCreacion"
            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition"
        >
            {{ __('Nueva postulación') }}
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        @foreach ($columnas as $estado => $postulaciones)
            <div class="bg-gray-100 rounded-lg flex flex-col">
                <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="font-semibold text-sm text-gray-700 uppercase tracking-wide">
                        {{ $estado }}
                    </h3>
                    <span class="inline-flex items-center justify-center w-6 h-6 text-xs font-semibold text-gray-600 bg-gray-200 rounded-full">
                        {{ $postulaciones->count() }}
                    </span>
                </div>

                <div
                    class="kanban-columna flex-1 p-3 space-y-3 min-h-[200px]"
                    data-estado="{{ $estado }}"
                    x-data
                    x-init="
                        Sortable.create($el, {
                            group: 'kanban',
                            animation: 150,
                            ghostClass: 'opacity-40',
                            onEnd: (evt) => {
                                $wire.moverPostulacion(evt.item.dataset.id, evt.to.dataset.estado);
                            },
                        });
                    "
                >
                    @foreach ($postulaciones as $postulacion)
                        <div data-id="{{ $postulacion->id }}" wire:key="postulacion-card-wrapper-{{ $postulacion->id }}">
                            @livewire('postulaciones.postulacion-card', ['postulacion' => $postulacion], key('postulacion-card-'.$postulacion->id))
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    @livewire('postulaciones.postulacion-form')
</div>
