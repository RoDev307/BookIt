<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Comprobante de Reserva - DATABOX</title>
    <style>
        @page {
            margin: 0cm 0cm;
        }

        body {
            font-family: 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif;
            color: #1e293b;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            line-height: 1.5;
        }


        .top-bar {
            background: linear-gradient(to right, #4f46e5, #06b6d4);
            background-color: #0f172a;
            height: 8px;
            width: 100%;
        }

        .container {
            padding: 50px 60px;
        }


        .invoice-header {
            width: 100%;
            margin-bottom: 40px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 30px;
        }

        .logo-container {
            width: 50%;
            float: left;
        }

        .logo-img {
            height: 55px;

            width: auto;
        }

        .header-meta {
            width: 50%;
            float: right;
            text-align: right;
        }

        .header-meta h1 {
            font-size: 18px;
            color: #0f172a;
            margin: 0 0 5px 0;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .header-meta p {
            font-size: 12px;
            color: #64748b;
            margin: 0;
        }

        .clear {
            clear: both;
        }


        .ticket-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
        }

        .ticket-title {
            font-size: 14px;
            font-weight: 700;
            color: #4f46e5;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
        }

        .info-table tr:last-child td {
            border-bottom: none;
        }

        .label {
            color: #64748b;
            font-weight: 500;
            width: 35%;
        }

        .value {
            color: #0f172a;
            font-weight: 600;
            text-align: right;
        }

        .badge {
            background-color: #dcfce7;
            color: #15803d;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 700;
            display: inline-block;
        }

        .notice {
            background-color: #f0fdfa;
            border-left: 4px solid #0d9488;
            padding: 15px;
            border-radius: 0 8px 8px 0;
            margin-top: 20px;
        }

        .notice p {
            font-size: 12px;
            color: #115e59;
            margin: 0;
        }


        .footer {
            position: absolute;
            bottom: 40px;
            left: 60px;
            right: 60px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 20px;
            font-size: 11px;
            color: #94a3b8;
        }
    </style>
</head>

<body>

    <div class="top-bar"></div>

    <div class="container">
        <div class="invoice-header">
            <div class="logo-container">
                @if(file_exists(public_path('images/logo.png')))
                <img src="{{ public_path('images/logo.png') }}" class="logo-img">
                @else
                <span style="font-size: 24px; font-weight: bold; color: #0f172a;">DATABOX</span>
                @endif
            </div>
            <div class="header-meta">
                <h1>COMPROBANTE DE CITA</h1>
                <p>Código: <span style="color: #0f172a; font-weight: bold;">DB-{{ rand(100000, 999999) }}</span></p>
                <p>Fecha Emisión: {{ date('Y-m-d') }}</p>
            </div>
            <div class="clear"></div>
        </div>

        <div class="ticket-card">
            <div class="ticket-title">Detalles de la Planificación</div>
            <table class="info-table">
                <tbody>
                    <tr>
                        <td class="label">Estado de la Cita</td>
                        <td class="value"><span class="badge">CONFIRMADA</span></td>
                    </tr>
                    <tr>
                        <td class="label">Fecha Reservada</td>
                        <td class="value">{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Hora de Atención</td>
                        <td class="value" style="color: #4f46e5; font-size: 15px;">{{ $hora }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="notice">
            <p><strong>Nota de Asistencia:</strong> Por favor, preséntese 5 minutos antes de la hora estipulada. Este documento digital sirve como comprobante de cupo bloqueado en el nodo de infraestructura centralizado de DATABOX.</p>
        </div>

        <div class="footer">
            <p>Este es un documento generado de forma automática por la plataforma DATABOX - 2026.</p>
            <p>Escuela de Ingeniería en Computación | Prácticas Profesionales</p>
        </div>
    </div>

</body>

</html>