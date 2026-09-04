<div>
    @if ($mostrar)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            x-data
            x-on:keydown.escape.window="$wire.cerrar()"
        >
            <div class="fixed inset-0 bg-gray-500/75" wire:click="cerrar"></div>

            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    {{ $postulacionId ? __('Editar postulación') : __('Nueva postulación') }}
                </h2>

                <form wire:submit="guardar" class="space-y-4">
                    <div>
                        <x-input-label for="empresa" value="{{ __('Empresa') }}" />
                        <x-text-input id="empresa" type="text" class="mt-1 block w-full" wire:model="empresa" autofocus />
                        <x-input-error :messages="$errors->get('empresa')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="cargo" value="{{ __('Cargo') }}" />
                        <x-text-input id="cargo" type="text" class="mt-1 block w-full" wire:model="cargo" />
                        <x-input-error :messages="$errors->get('cargo')" class="mt-1" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="fecha_postulacion" value="{{ __('Fecha de postulación') }}" />
                            <x-text-input id="fecha_postulacion" type="date" class="mt-1 block w-full" wire:model="fecha_postulacion" />
                            <x-input-error :messages="$errors->get('fecha_postulacion')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label for="estado" value="{{ __('Estado') }}" />
                            <select
                                id="estado"
                                wire:model="estado"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            >
                                @foreach ($estados as $opcion)
                                    <option value="{{ $opcion->value }}">{{ $opcion->value }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('estado')" class="mt-1" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="link_vacante" value="{{ __('Link de la vacante') }}" />
                        <x-text-input id="link_vacante" type="url" placeholder="https://..." class="mt-1 block w-full" wire:model="link_vacante" />
                        <x-input-error :messages="$errors->get('link_vacante')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="notas" value="{{ __('Notas') }}" />
                        <textarea
                            id="notas"
                            rows="3"
                            wire:model="notas"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        ></textarea>
                        <x-input-error :messages="$errors->get('notas')" class="mt-1" />
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <div>
                            @if ($postulacionId)
                                <x-danger-button
                                    type="button"
                                    wire:click="eliminar"
                                    wire:confirm="{{ __('¿Eliminar esta postulación? Esta acción no se puede deshacer.') }}"
                                >
                                    {{ __('Eliminar') }}
                                </x-danger-button>
                            @endif
                        </div>

                        <div class="flex items-center gap-3">
                            <x-secondary-button type="button" wire:click="cerrar">
                                {{ __('Cancelar') }}
                            </x-secondary-button>
                            <x-primary-button type="submit" wire:loading.attr="disabled">
                                {{ __('Guardar') }}
                            </x-primary-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
