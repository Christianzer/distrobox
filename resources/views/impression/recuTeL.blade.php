<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu de Transaction</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .recu-container {
            width: 300px;
            padding: 15px;
            border: 1px solid #000;
            text-align: center;
        }
        .recu-header {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .recu-info {
            text-align: left;
            margin-bottom: 10px;
        }
        .recu-info p {
            margin: 5px 0;
        }
        .recu-footer {
            margin-top: 15px;
            font-size: 12px;
        }
        .logo {
            width: 80px;
            height: auto;
        }
    </style>
</head>
<body>

<div class="recu-container">
    <!-- Logo -->
    <img src="{{ asset('front/logoEmaster.png') }}" alt="Logo" class="logo">

    <!-- Nom de l'entreprise -->
    <div>
        <h4 style=" color: #4e73df !important;font-weight: bold">SUCCESS MASTER</h4>
    </div>

    <div class="recu-header">Reçu de Transaction</div>

    <div class="recu-info">
        <p><strong>Heure :</strong> <span id="recu-heure"></span></p>
        <p><strong>Client :</strong> <span id="recu-client"></span></p>
        <p><strong>Transaction :</strong> <span id="recu-transaction"></span></p>
        <p><strong>Montant :</strong> <span id="recu-montant"></span></p>
        <p><strong>Opérateur :</strong> <span id="recu-operateur"></span></p>
        <p><strong>Affiliation :</strong> <span id="recu-affiliation"></span></p>
        <p><strong>Agent :</strong> <span id="recu-agent"></span></p>
    </div>

    <div class="recu-footer">Merci pour votre confiance</div>
</div>
</body>
</html>
