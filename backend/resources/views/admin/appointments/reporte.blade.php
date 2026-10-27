<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Citas - BookIt</title>
    <style>
        @page {
            margin: 0cm 0cm;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }

        .top-bar {
            background-color: #4f46e5;
            height: 8px;
            width: 100%;
        }

        .container {
            padding: 50px 60px;
        }

        .header {
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 20px;
            color: #0f172a;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: -0.5px;
        }

        .header p {
            font-size: 12px;
            color: #64748b;
            margin: 5px 0 0 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 11px;
            text-transform: uppercase;
            padding: 12px;
            border-bottom: 2px solid #e2e8f0;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 12px;
            text-align: center;
        }

        .status-confirmed {
            color: #15803d;
            font-weight: bold;
            background: #dcfce7;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 10px;
        }

        .status-cancelled {
            color: #991b1b;
            font-weight: bold;
            background: #fee2e2;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 10px;
        }

        .footer {
            position: fixed;
            bottom: 40px;
            left: 60px;
            right: 60px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 20px;
            font-size: 10px;
            color: #94a3b8;
        }
    </style>
</head>

<body>
    <div class="top-bar"></div>

    <div class="container">
        <div class="header">
            <h1>Reporte de Citas: {{ ucfirst($periodo) }}</h1>
            <p>Empresa: <strong>{{ $negocio }}</strong> | Generado el: {{ date('d/m/Y H:i') }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Fecha/Hora</th>
                    <th>Servicio</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($citas as $c)
                    <tr>
                        <td style="color: #64748b;">#{{ $c->id }}</td>
                        <td style="font-weight: 600;">{{ $c->client_name ?? ($c->user->name ?? 'Cliente Externo') }}</td>
                        <td style="font-family: monospace;">
                            {{ \Carbon\Carbon::parse($c->appointment_time)->format('d/m/Y H:i') }}</td>
                        <td>{{ $c->service->name ?? 'N/A' }}</td>
                        <td>
                            <span class="{{ $c->status === 'confirmed' ? 'status-confirmed' : 'status-cancelled' }}">
                                {{ strtoupper($c->status) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            <p>Reporte oficial generado automáticamente por BookIt SaaS - 2026.</p>
        </div>
    </div>
</body>

</html>
