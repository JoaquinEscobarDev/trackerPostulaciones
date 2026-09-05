<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BuscadorEmpleosGetOnBrd
{
    protected string $baseUrl = 'https://www.getonbrd.com/api/v0/';

    /**
     * Busca empleos por palabra clave en la API pública de Get on Board.
     *
     * @return array{empleos: \Illuminate\Support\Collection, totalPaginas: int}
     */
    public function buscar(string $query, int $pagina = 1): array
    {
        // La API espera "expand[]=x&expand[]=y" (array literal, estilo Rails).
        // http_build_query() serializa arrays como "expand[0]=x&expand[1]=y",
        // que esta API rechaza con 422, así que se arma la query a mano.
        $queryString = http_build_query([
            'query' => $query,
            'page' => $pagina,
            'per_page' => 12,
            'lang' => 'es',
        ]).'&expand[]=company&expand[]=seniority';

        $respuesta = Http::baseUrl($this->baseUrl)
            ->timeout(6)
            ->get('search/jobs?'.$queryString);

        if ($respuesta->failed()) {
            Log::warning('Get on Board search failed', ['status' => $respuesta->status()]);

            return ['empleos' => collect(), 'totalPaginas' => 0];
        }

        $json = $respuesta->json();

        $empleos = collect($json['data'] ?? [])->map(function (array $job) {
            $attrs = $job['attributes'];
            $empresa = $attrs['company']['data']['attributes'] ?? null;

            return [
                'id' => $job['id'],
                'titulo' => $attrs['title'],
                'empresa' => $empresa['name'] ?? 'Empresa confidencial',
                'logo' => $empresa['logo'] ?? null,
                'remoto' => (bool) ($attrs['remote'] ?? false),
                'paises' => $attrs['countries'] ?? [],
                'seniority' => $attrs['seniority']['data']['attributes']['name'] ?? null,
                'salario_min' => $attrs['min_salary'] ?? null,
                'salario_max' => $attrs['max_salary'] ?? null,
                'publicado_en' => isset($attrs['published_at'])
                    ? Carbon::createFromTimestamp($attrs['published_at'])
                    : null,
                'url' => $job['links']['public_url'] ?? null,
            ];
        });

        return [
            'empleos' => $empleos,
            'totalPaginas' => $json['meta']['total_pages'] ?? 0,
        ];
    }
}
