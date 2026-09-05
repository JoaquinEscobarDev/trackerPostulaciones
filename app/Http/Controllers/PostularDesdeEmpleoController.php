<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PostularDesdeEmpleoController extends Controller
{
    /**
     * Recibe los datos de una vacante encontrada en el buscador público y,
     * ya autenticado (esta ruta está protegida por el middleware "auth",
     * así que un invitado pasa primero por el login gracias al mecanismo
     * de "intended URL" de Laravel), los deja en sesión para que el
     * KanbanBoard abra el formulario de nueva postulación precargado.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'empresa' => ['required', 'string', 'max:255'],
            'cargo' => ['required', 'string', 'max:255'],
            'link_vacante' => ['nullable', 'url', 'max:2048'],
        ]);

        session()->flash('empleo_prefill', $datos);

        return redirect()->route('postulaciones');
    }
}
