<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Busca empleos en Careerjet (API v4): agregador general que cubre
 * todos los rubros, no solo tecnología, con foco en Chile (locale es_CL).
 *
 * Se eligió sobre Get on Board (que quedó descartado) porque ese
 * segundo solo indexa vacantes de tecnología; para un buscador de
 * empleo "universal" hacía falta un agregador de rubros generales.
 */
class BuscadorEmpleosCareerjet
{
    protected string $endpoint = 'https://search.api.careerjet.net/v4/query';

    /**
     * @return array{empleos: \Illuminate\Support\Collection, totalPaginas: int}
     */
    public function buscar(string $query, int $pagina = 1, ?string $ip = null, ?string $userAgent = null): array
    {
        $apiKey = config('services.careerjet.key');

        if (! $apiKey) {
            Log::warning('Careerjet: falta CAREERJET_API_KEY en .env');

            return ['empleos' => collect(), 'totalPaginas' => 0];
        }

        $respuesta = Http::withBasicAuth($apiKey, '')
            ->withHeaders([
                'Referer' => config('app.url'),
            ])
            ->timeout(6)
            ->get($this->endpoint, [
                'locale_code' => config('services.careerjet.locale', 'es_CL'),
                'keywords' => $query,
                'page' => $pagina,
                'page_size' => 12,
                'sort' => 'date',
                'user_ip' => $ip ?: '127.0.0.1',
                'user_agent' => $userAgent ?: 'TrackerPostulaciones/1.0',
            ]);

        if ($respuesta->failed()) {
            Log::warning('Careerjet search failed', ['status' => $respuesta->status(), 'body' => $respuesta->body()]);

            return ['empleos' => collect(), 'totalPaginas' => 0];
        }

        $json = $respuesta->json();

        if (($json['type'] ?? null) !== 'JOBS') {
            return ['empleos' => collect(), 'totalPaginas' => 0];
        }

        $empleos = collect($json['jobs'] ?? [])->map(fn (array $job) => [
            'id' => md5($job['url'] ?? $job['title']),
            'titulo' => $job['title'],
            'empresa' => $job['company'] ?: 'Empresa confidencial',
            'logo' => null,
            'remoto' => str_contains(mb_strtolower($job['title'].' '.($job['locations'] ?? '')), 'remoto') || str_contains(mb_strtolower($job['title'].' '.($job['locations'] ?? '')), 'remote'),
            'paises' => array_filter([$job['locations'] ?? null]),
            'seniority' => null,
            'salario_min' => $job['salary_min'] ?? null,
            'salario_max' => $job['salary_max'] ?? null,
            'publicado_en' => ! empty($job['date']) ? Carbon::parse($job['date']) : null,
            'url' => $job['url'] ?? null,
        ]);

        return [
            'empleos' => $empleos,
            'totalPaginas' => $json['pages'] ?? 0,
        ];
    }
}
