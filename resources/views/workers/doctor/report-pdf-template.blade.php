<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe de Pacient</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }
        .container {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            border-bottom: 3px solid #2c3e50;
            margin-bottom: 25px;
            padding-bottom: 15px;
            text-align: center;
        }
        .header h1 {
            font-size: 18px;
            margin-bottom: 8px;
            color: #2c3e50;
        }
        .header p {
            font-size: 10px;
            color: #666;
        }
        .section {
            margin-bottom: 18px;
        }
        .section-title {
            font-weight: bold;
            font-size: 12px;
            background-color: #34495e;
            color: white;
            padding: 8px 10px;
            margin-bottom: 10px;
            border-radius: 3px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        .info-item {
            background-color: #ecf0f1;
            padding: 8px;
            border-radius: 3px;
        }
        .info-item strong {
            display: block;
            font-size: 10px;
            color: #2c3e50;
            margin-bottom: 3px;
        }
        .info-item p {
            font-size: 11px;
            color: #555;
            word-wrap: break-word;
        }
        .full-width {
            grid-column: 1 / -1;
        }
        .text-content {
            background-color: #f8f9fa;
            padding: 10px;
            border-left: 3px solid #3498db;
            margin-bottom: 10px;
            font-size: 11px;
            line-height: 1.5;
            color: #555;
        }
        .qr-section {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 2px solid #bdc3c7;
            text-align: center;
        }
        .qr-section .section-title {
            text-align: left;
            margin-bottom: 15px;
        }
        .qr-code {
            display: inline-block;
            background: white;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .qr-code svg {
            max-width: 150px;
            height: auto;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 9px;
            color: #999;
        }
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .container {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Informe Mèdic de Pacient</h1>
            <p>Data de creació: {{ now()->format('d/m/Y H:i') }}</p>
        </div>

        <!-- Dades del Pacient -->
        <div class="section">
            <div class="section-title">Dades del Pacient</div>
            <div class="info-grid">
                <div class="info-item">
                    <strong>Nom:</strong>
                    <p>{{ $data['name'] ?? 'N/A' }}</p>
                </div>
                <div class="info-item">
                    <strong>NTS:</strong>
                    <p>{{ $data['nts'] ?? 'N/A' }}</p>
                </div>
                <div class="info-item">
                    <strong>Data de naixement:</strong>
                    <p>{{ $data['birth_date'] ?? 'N/A' }}</p>
                </div>
                <div class="info-item">
                    <strong>Adreça:</strong>
                    <p>{{ $data['address'] ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Informació de la Derivació -->
        <div class="section">
            <div class="section-title">Informació de la Derivació</div>
            <div class="info-grid">
                <div class="info-item">
                    <strong>Centre sol·licitat:</strong>
                    <p>{{ $data['center_requested'] ?? 'N/A' }}</p>
                </div>
                <div class="info-item">
                    <strong>Centre de destinació:</strong>
                    <p>{{ $data['center_destination'] ?? 'N/A' }}</p>
                </div>
                <div class="info-item full-width">
                    <strong>Nom del doctor:</strong>
                    <p>{{ $data['doctor_name'] ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Dades de la Demanda -->
        @if($data['data_request'] ?? null)
        <div class="section">
            <div class="section-title">Dades de la Demanda</div>
            <div class="text-content">
                {{ $data['data_request'] }}
            </div>
        </div>
        @endif

        <!-- Exploració -->
        <div class="section">
            <div class="section-title">Exploració</div>
            @if($data['data_exploration'] ?? null)
            <div class="text-content">
                <strong>Exploració:</strong><br>
                {{ $data['data_exploration'] }}
            </div>
            @endif
            
            @if($data['reason'] ?? null)
            <div class="text-content">
                <strong>Raó:</strong><br>
                {{ $data['reason'] }}
            </div>
            @endif
            
            @if($data['exploration'] ?? null)
            <div class="text-content">
                <strong>Detalls:</strong><br>
                {{ $data['exploration'] }}
            </div>
            @endif
        </div>

        <!-- Codi QR -->
        <div class="qr-section">
            <div class="section-title">Codi QR de Verificació</div>
            <div class="qr-code">
                {!! $Code !!}
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Informe generat automàticament el {{ now()->format('d/m/Y') }} a les {{ now()->format('H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
