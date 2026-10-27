<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Comprobante de Cita - BookIt</title>
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
            background-color: #4f46e5;
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
            width: 40%;
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
            font-size: 11px;
            font-weight: 700;
            display: inline-block;
        }

        .badge-cancelled {
            background-color: #fee2e2;
            color: #991b1b;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 11px;
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
                <span
                    style="font-size: 26px; font-black font-weight: bold; color: #4f46e5; letter-spacing: -1px;">BookIt</span>
                <p style="font-size: 11px; color: #64748b; margin: 3px 0 0 0;">
                    {{ $appointment->business->name ?? 'Establecimiento Comercial' }}</p>
            </div>
            <div class="header-meta">
                <h1>COMPROBANTE DE CITA</h1>
                <p>Código: <span
                        style="color: #0f172a; font-weight: bold;">BK-{{ str_pad($appointment->id, 6, '0', STR_PAD_LEFT) }}</span>
                </p>
                <p>Fecha Emisión: {{ date('Y-m-d H:i') }}</p>
            </div>
            <div class="clear"></div>
        </div>

        <div class="ticket-card">
            <div class="ticket-title">Detalles de la Reserva Operativa</div>
            <table class="info-table">
                <tbody>
                    <tr>
                        <td class="label">Estado de la Gestión</td>
                        <td class="value">
                            @if ($appointment->status === 'confirmed')
                                <span class="badge">CONFIRMADA</span>
                            @else
                                <span class="badge-cancelled">CANCELADA</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="label">Cliente Solicitante</td>
                        <td class="value">{{ $clientName }}</td>
                    </tr>
                    <tr>
                        <td class="label">Servicio Contratado</td>
                        <td class="value">{{ $appointment->service->name ?? 'Servicio General' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Especialista / Atendido Por</td>
                        <td class="value">{{ $appointment->staff_name ?? 'Recepción' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Fecha Programada</td>
                        <td class="value">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('d/m/Y') }}
                        </td>
                    </tr>
                    <tr>
                        <td class="label">Hora de Atención</td>
                        <td class="value" style="color: #4f46e5; font-size: 15px;">
                            {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if (!empty($cleanNotes))
            <div class="ticket-card" style="margin-top: -15px; padding: 15px 25px;">
                <div style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Notas y
                    Observaciones</div>
                <p style="font-size: 13px; color: #334155; margin: 5px 0 0 0; font-style: italic;">"{{ $cleanNotes }}"
                </p>
            </div>
        @endif

        <div class="notice">
            <p><strong>Nota de Asistencia:</strong> Por favor, preséntese 5 minutos antes de la hora estipulada. Este
                documento digital sirve como comprobante de cupo bloqueado de forma legítima en la infraestructura
                distribuida multi-tenant de BookIt.</p>
        </div>

        <div class="footer">
            <p>Este es un documento generado de forma automática por la plataforma SaaS BookIt - 2026.</p>
            <p>Desarrollado en El Salvador con fines estrictamente académicos.</p>
        </div>
    </div>

</body>

</html>
