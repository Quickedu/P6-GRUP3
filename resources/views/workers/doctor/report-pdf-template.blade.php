<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <title>Informe PMF</title>

    <style>
        @page {
            margin: 8mm 18mm;
        }

        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 14px;
            color: #1e1e1e;
            line-height: 1.45;
        }

        .page-inner {
            max-width: 175mm;
            margin: 0 auto;
        }

        /* HEADER */
        .header {
            margin-bottom: 5px;
        }

        .header-table {
            width: 100%;
        }

        .header-logo {
            width: 95px;
        }

        .header-logo img {
            width: 90px;
        }

        .main-title {
            font-size: 16px;
            font-weight: bold;
        }

        .sub-title {
            font-size: 12px;
            color: #555;
        }

        /* META TABLE */
        .meta-table {
            width: 100%;
            margin-top: 5px;
            margin-bottom: 10px;
        }

        .meta-table td {
            vertical-align: top;
            padding: 5px;
        }

        .meta-left {
            width: 60%;
        }

        .meta-right {
            width: 40%;
        }

        .report-number {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 6px;
        }

        .meta-row {
            margin: 2px 0;
        }

        .patient-name {
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        /* DIVIDER */
        .divider {
            border-top: 1px solid #000;
            margin: 5px 0;
        }

        /* SECTIONS */
        .section {
            margin-bottom: 8px;
        }

        .section-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 6px;
        }

        .section-body {
            text-align: justify;
            white-space: pre-wrap;
        }

        /* FOOTER */
        .footer {
            margin-top: 20px;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="page-inner">

        <!-- HEADER -->
        <table class="header-table">
            <tr>
                <td class="header-logo">
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/logo.png'))) }}" alt="Logo PMF">                
                </td>
                <td>
                    <div class="main-title">INFORME PMF</div>
                    <div class="sub-title">Servei de Diagnòstic per la Imatge</div>
                </td>
            </tr>
        </table>

        <!-- META -->
        <table class="meta-table">
            <tr>

                <td class="meta-left">
                    <div class="report-number">Informe {{ $data['nreport'] ?? '' }}</div>

                    <div class="meta-row">
                        <span class="lbl">Centre sol·licitant:</span> {{ $data['center_requested'] ?? '' }}
                    </div>
                    <div class="meta-row">
                        <span class="lbl">Metge sol·licitant:</span> {{ $data['doctor_name'] ?? '' }}
                    </div>

                    <br>

                    <div class="meta-row">
                        <span class="lbl">Data sol·licitud:</span> {{ $data['data_request'] ?? '' }}
                    </div>
                    <div class="meta-row">
                        <span class="lbl">Data exploració:</span> {{ $data['data_exploration'] ?? '' }}
                    </div>
                    <div class="meta-row">
                        <span class="lbl">Centre destí:</span> {{ $data['center_destination'] ?? '' }}
                    </div>
                </td>

                <td class="meta-right">
                    <div class="patient-card">
                        <div class="patient-name">{{ $data['name'] ?? 'NOM DEL PACIENT' }}</div>
                        <div>
                            {{ $data['address'] ?? '' }}<br>
                            {{ $data['postal_code'] ?? '' }} {{ $data['city'] ?? '' }}<br>
                            {{ $data['province'] ?? '' }}
                        </div>
                    </div>

                    {{-- <br> --}}

                    <div class="meta-row">
                        <span class="lbl">Data naixement:</span> {{ $data['birth_date'] ?? '' }}
                    </div>
                    <div class="meta-row">
                        <span class="lbl">NTS:</span> {{ $data['nts'] ?? '' }}
                    </div>
                </td>

            </tr>
        </table>

        <hr class="divider">

        <!-- SECTIONS -->
        <div class="section">
            <div class="section-title">Motiu</div>
            <div class="section-body">{{ $data['reason'] ?? '' }}</div>
        </div>

        <div class="section">
            <div class="section-title">Exploració</div>
            <div class="section-body">{{ $data['exploration'] ?? '' }}</div>
        </div>

        <div class="section">
            <div class="section-title">Informe</div>
            <div class="section-body">{{ $data['report'] ?? '' }}</div>
        </div>

        <!-- FOOTER -->
        <div class="footer">
            Dr/a {{ $data['doctor_name'] ?? '' }} Nº col·legiat: {{ $data['license_number'] ?? '' }}<br>
            Sabadell, {{ now()->format('d-m-Y') }}
        </div>

    </div>
</body>

</html>
