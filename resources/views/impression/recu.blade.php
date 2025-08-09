<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu de Transaction {{$transaction->code_transaction}}</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        .recu-container { width: 100%; padding: 15px; text-align: center; }
        .logo-container img { max-width: 100px; height: auto; }
        .company-name { font-size: 18px; font-weight: bold; color: #007bff; margin-top: 5px; }
        .recu-info { text-align: left; margin-top: 20px; }
        .recu-info p { margin: 5px 0; }
        .recu-footer { margin-top: 15px; font-size: 12px; }
    </style>
</head>
<body>

<div class="recu-container">
    <div class="logo-container">
        <img src="{{ asset('front/logoEmaster.png') }}" alt="Logo">
        <div class="company-name">DISTRIBOX</div>
    </div>

    <h2 style="text-transform: uppercase">Reçu de Transaction {{$transaction->code_transaction}}</h2>

    @php
        $operatorMapping = [
        'moov' => 'Moov',
        'orange' => 'Orange',
        'mtn' => 'MTN',
        'wave' => 'Wave',
        'djamo' => 'Djamo',
        'push' => 'Push',
        'trmo' => 'Tresor Monney'
        ];

                    $typesTransactionsTableau = [
'AU' => ['libelle' => 'Achat UV', 'couleur' => 'bg-success'],  // Vert
'RU' => ['libelle' => 'Retour UV', 'couleur' => 'bg-danger'],   // Rouge
'EC' => ['libelle' => 'Encaissement Achat UV', 'couleur' => 'bg-info'],  // Bleu clair
'DC' => ['libelle' => 'Decaissement pour retour UV', 'couleur' => 'bg-warning'],  // Jaune
];
    @endphp

    <div class="recu-info">
        <p><strong>Date et Heure :</strong> {{ date('d-m-Y h:i:s',strtotime($transaction->date_transaction ))}}</p>
        <p><strong>Client :</strong> {{ $transaction->client }}</p>
        <p><strong>Transaction :</strong> {{ $typesTransactionsTableau[$transaction->transaction]['libelle'] }}</p>
        <p><strong>Montant :</strong> {{ number_format($transaction->montant, 0, ',', ' ') }} FCFA</p>
        <p><strong>Opérateur :</strong> {{ $operatorMapping[$transaction->operateur] }}</p>
        <p><strong>Affiliation :</strong> {{ $transaction->affiliation }}</p>
        <p><strong>Agent :</strong> {{ $transaction->agent }}</p>
    </div>

    <div class="recu-footer">Merci pour votre confiance</div>
</div>

</body>
</html>
