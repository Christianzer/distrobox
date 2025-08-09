<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{asset('front/css/sb-admin-2.css')}}" rel="stylesheet">
    <title class="text-uppercase">{{$titre}}</title>
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
            <h3 class="text-primary font-weight-bold">DISTRIBOX</h3>
        </div>
    </div>


    <h6 class="font-weight-bold text-black text-center text-uppercase">{{$titre}}</h6>

    @foreach ($transactions as $transaction)
        <div class="bg-white text-primary text-white p-2">
            <strong>Date attribution:</strong> {{ \Carbon\Carbon::parse($transaction->date_transaction)->format('d/m/Y') }}
        </div>
        <table class="table table-bordered text-capitalize">
            <thead class="bg-success text-white font-weight-bold">
            <tr>
                <th>description</th>
                <th>prix</th>
                <th>quantité</th>
                <th>montant</th>
            </tr>
            </thead>
            <tbody>
            @foreach($transaction->produits as $produit)
                <tr>
                    <td>{{$produit->description}}</td>
                    <td>{{formatNumber($produit->prix)}}</td>
                    <td>{{formatNumber($produit->quantite)}}</td>
                    <td>{{formatNumber($produit->montant)}}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr class="bg-secondary text-white">
                <td colspan="2"><strong>total</strong></td>
                <td><strong>{{ $transaction->total_quantite }}</strong></td>
                <td><strong>{{ number_format($transaction->total_montant, 0, ',', ' ') }}</strong></td>
            </tr>
            </tfoot>
        </table>
    @endforeach

</div>
</body>
</html>
