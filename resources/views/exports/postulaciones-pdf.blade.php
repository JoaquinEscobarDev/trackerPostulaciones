<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Postulaciones</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #1f2937; }
        h1 { font-size: 16px; margin-bottom: 2px; }
        p.subtitulo { color: #6b7280; margin-top: 0; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 6px 8px; text-align: left; vertical-align: top; }
        th { background-color: #f3f4f6; }
        .estado { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 10px; }
    </style>
</head>
<body>
    <h1>Historial de postulaciones</h1>
    <p class="subtitulo">{{ $usuario->name }} &middot; generado el {{ now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Empresa</th>
                <th>Cargo</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Notas</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($postulaciones as $postulacion)
                <tr>
                    <td>{{ $postulacion->empresa }}</td>
                    <td>{{ $postulacion->cargo }}</td>
                    <td>{{ $postulacion->fecha_postulacion->format('d/m/Y') }}</td>
                    <td>{{ $postulacion->estado->value }}</td>
                    <td>{{ $postulacion->notas }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Sin postulaciones registradas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
