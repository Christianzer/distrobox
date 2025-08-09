<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{asset('front/css/sb-admin-2.css')}}" rel="stylesheet">
    <title>{{$titre}}</title>
    <style>
        body {
            font-family: Arial, sans-serif !important;
            line-height: normal !important;
        }

        .header {
            display: flex !important;
            align-items: center !important;
            margin-bottom: 20px !important;
        }

        .footer {
            text-align: center !important;
            margin-bottom: 20px !important;
        }

        .header img {
            max-width: 100px !important;
            margin-right: 20px !important;
        }

        .header h1 {
            font-size: 20px !important;
            margin: 0 !important;
        }

        .header p {
            font-size: 12px !important;
            color: #666 !important;
        }

        .section {
            margin-bottom: 20px !important;
        }

        .section h2 {
            font-size: 16px !important;
            border-bottom: 1px solid #ddd !important;
            padding-bottom: 5px !important;
            margin-bottom: 10px !important;
        }

        .section table {
            width: 100% !important;
            border: none !important;
            border-collapse: collapse !important;
        }

        .section table th, .section table td {
            border: none !important;
            padding: 8px !important;
            text-align: left !important;
        }

        .section table th {
            background-color: #f5f5f5 !important;
        }

        .footer {
            font-size: 12px !important;
            color: #666 !important;
            text-align: center !important;
            margin-top: 20px !important;
        }

    </style>
</head>
<body>
<div class="container-fluid" style="align-content: center">
    <div class="header">
        <img src="{{ asset('front/logoEmaster.png') }}" alt="Logo">
        <div>
            <h1 class="text-primary font-weight-bold">DISTRIBOX</h1>
        </div>
    </div>


    <h4 class="font-weight-bold text-black text-center">{{$titre}}</h4>


    <table class="font-weight-bold" width="98%">
        <tr class="bg-danger text-white font-weight-bold text-center">
            <td colspan="5">SOLDE</td>
        </tr>
        <tr class="text-white font-weight-bold text-center">
            <td width="48%" class="bg-primary" colspan="2">DEPART</td>
            <td width="4%"></td>
            <td width="48%" class="bg-success" colspan="2">EN COURS</td>
        </tr>
        <tr>
            <td width="15%">CASH</td>
            <td width="33%" class="text-right" id="cash_depart">{{ $soldes['cash_depart'] }}</td>
            <td width="4%"></td>
            <td width="15%">CASH</td>
            <td width="33%" class="text-right" id="cash_encours">{{ $soldes['cash_encours'] }}</td>
        </tr>

        <tr>
            <td width="15%">UV DJAMO</td>
            <td width="33%" class="text-right" id="uv_djamo_depart">{{ $soldes['uv_djamo_depart'] }}</td>
            <td width="4%"></td>
            <td width="15%">UV DJAMO</td>
            <td width="33%" class="text-right" id="uv_djamo_encours">{{ $soldes['uv_djamo_encours'] }}</td>
        </tr>

        <tr>
            <td width="15%">UV ORANGE</td>
            <td width="33%" class="text-right" id="uv_orange_depart">{{ $soldes['uv_orange_depart'] }}</td>
            <td width="4%"></td>
            <td width="15%">UV ORANGE</td>
            <td width="33%" class="text-right" id="uv_orange_encours">{{ $soldes['uv_orange_encours'] }}</td>
        </tr>
        <tr>
            <td width="15%">UV PUSH</td>
            <td width="33%" class="text-right" id="uv_push_depart">{{ $soldes['uv_push_depart'] }}</td>
            <td width="4%"></td>
            <td width="15%">UV PUSH</td>
            <td width="33%" class="text-right" id="uv_push_encours">{{ $soldes['uv_push_encours'] }}</td>
        </tr>
        <tr>
            <td width="15%">UV TRESOR</td>
            <td width="33%" class="text-right" id="uv_trmo_depart">{{ $soldes['uv_trmo_depart'] }}</td>
            <td width="4%"></td>
            <td width="15%">UV TRESOR</td>
            <td width="33%" class="text-right" id="uv_trmo_encours">{{ $soldes['uv_trmo_encours'] }}</td>
        </tr>
        <tr>
            <td width="15%">UV WAVE</td>
            <td width="33%" class="text-right" id="uv_wave_depart">{{ $soldes['uv_wave_depart'] }}</td>
            <td width="4%"></td>
            <td width="15%">UV WAVE</td>
            <td width="33%" class="text-right" id="uv_wave_encours">{{ $soldes['uv_wave_encours'] }}</td>
        </tr>
        <tr>
            <td width="15%">DETTE</td>
            <td width="33%" class="text-right" id="dette_depart">{{ $soldes['dette_depart'] }}</td>
            <td width="4%"></td>
            <td width="15%">DETTE</td>
            <td width="33%" class="text-right" id="dette_encours">{{ $soldes['dette_encours'] }}</td>
        </tr>
        <tr>
            <td width="15%">AVOIR</td>
            <td width="33%" class="text-right" id="avoir_depart">{{ $soldes['avoir_depart'] }}</td>
            <td width="4%"></td>
            <td width="15%">AVOIR</td>
            <td width="33%" class="text-right" id="avoir_encours">{{ $soldes['avoir_encours'] }}</td>
        </tr>
        <tr class="bg-secondary text-uppercase text-white font-weight-bold text-center">
            <td class="text-left">Total</td>
            <td class="text-right" id="total_depart">{{ $soldes['total_depart'] }}</td>
            <td></td>
            <td></td>
            <td class="text-right" id="total_encours">{{ $soldes['total_encours'] }}</td>
            <td></td>
        </tr>
    </table>
    <BR>
    <table width="100%">
        <tr>
            <td width="50%" style="vertical-align: top; padding-right: 10px;">
                <table class="table table-bordered">
                    <thead class="bg-danger text-white">
                    <tr>
                        <th colspan="2">Débiteurs</th>
                    </tr>
                    <tr>
                        <th>Code Client</th>
                        <th>Solde</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php($totalDebiteurs = 0)
                    @foreach($clientsDebiteurs as $client)
                        @php($totalDebiteurs += $client->solde)
                        <tr>
                            <td>{{ $client->client }}</td>
                            <td>{{ formatNumber($client->solde) }} FCFA</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot class="bg-danger text-uppercase text-white font-weight-bold">
                    <tr>
                        <td class="text-right text-uppercase">Total :</td>
                        <td>{{ formatNumber($totalDebiteurs) }} FCFA</td>
                    </tr>
                    </tfoot>
                </table>
            </td>
            <td width="50%" style="vertical-align: top; padding-left: 10px;">
                <table class="table table-bordered">
                    <thead class="bg-success text-white">
                    <tr>
                        <th colspan="2">Créditeurs</th>
                    </tr>
                    <tr>
                        <th>Code Client</th>
                        <th>Solde</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php($totalCrediteurs = 0)
                    @foreach($clientsCrediteurs as $client)
                        @php($totalCrediteurs += $client->solde)
                        <tr>
                            <td>{{ $client->client }}</td>
                            <td>{{ formatNumber($client->solde) }} FCFA</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot class="bg-success text-white font-weight-bold">
                    <tr>
                        <td class="text-right">Total :</td>
                        <td>{{ formatNumber($totalCrediteurs) }} FCFA</td>
                    </tr>
                    </tfoot>
                </table>
            </td>
        </tr>
    </table>


</div>
</body>
</html>
