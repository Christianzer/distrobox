<?php
set_time_limit(0);

use Illuminate\Support\Facades\DB;
use PHPMailer\PHPMailer\PHPMailer;


if(!function_exists('agentId')) {
    function agentId($id)
    {
        $personne = DB::table('users')->
        where('id','=',$id)
            ->first();

        return isset($personne) ? $personne->personnel : ' ';
    }
}


if(!function_exists('clientName')) {
    function clientName($code)
    {
        $personne = DB::table('client')->
        where('code','=',$code)
            ->first();

        return isset($personne) ? $personne->raison_sociale : ' ';
    }
}


if(!function_exists('dateToFrench')) {
    function dateToFrench($date, $format)
    {
        $english_days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
        $french_days = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
        $english_months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
        $french_months = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
        return str_replace($english_months, $french_months, str_replace($english_days, $french_days, date($format, strtotime($date))));
    }
}

if(!function_exists('formatNumber')) {
    function formatNumber($number)
    {
        return $number == 0 ? '0' : number_format($number, 0, ',', ' ');
    }
}

if(!function_exists('formatNumberType')) {
    function formatNumberType($number,$type)
    {
        if ($type == 'EC'){
            return $number == 0 ? '0' : number_format($number, 0, ',', ' ');
        }else{
            return $number == 0 ? '- 0' : ' - '.number_format($number, 0, ',', ' ');
        }

    }
}

if(!function_exists('formatNumberPart')) {
    function formatNumberPart($number,$type,$statut)
    {
        if ($type === 'AU'):
            $prefixe = '';
            $attente = "En attente d'achat UV ";
        elseif ($type === 'RU'):
            $prefixe = '-';
            $attente = "En attente de retour UV ";
        elseif ($type === 'EC'):
            $prefixe = '';
            $attente = "En attente d'encaissement pour achat UV ";
        elseif ($type === 'DC'):
            $prefixe = '-';
            $attente = "En attente de decaissement pour achat UV ";
        endif;

        if($statut == 1){
            return $number == 0 ? ' ' : $attente.number_format($number, 0, ',', ' ');
        }else{
            return $number == 0 ? ' ' : $prefixe.' '.number_format($number, 0, ',', ' ');
        }

    }
}

if (!function_exists('genererCode')) {
    function genererCode($type)
    {
        $types = ['AU', 'RU', 'EC', 'DC', 'DP', 'RE'];

        // Vérifier si le type passé est valide
        if (!in_array($type, $types)) {
            return 'Type invalide';
        }

        // Obtenir la date actuelle
        $jour = date('d');
        $mois = date('m'); // Mois sous forme de 2 chiffres
        $annee = date('Y'); // Année sous forme de 4 chiffres

        $numero = 0;

        do {
            $numero++;
            $compteurFormatte = str_pad($numero, 7, '0', STR_PAD_LEFT);
            $code = $type . $jour . $mois . $annee . $compteurFormatte;

            // Vérifier si le code existe déjà
            $exists = DB::table('transaction')->where('code_transaction', $code)->exists();
        } while ($exists); // Continuer jusqu'à trouver un code unique

        return $code;
    }
}



if (!function_exists('genererBordereau')) {
    function genererBordereau()
    {
        // Obtenir la date actuelle
        $jour = date('d');
        $mois = date('m'); // Mois sous forme de 2 chiffres
        $annee = date('Y'); // Année sous forme de 4 chiffres

        $numero = 0;

        do {
            $numero++;
            $compteurFormatte = str_pad($numero, 7, '0', STR_PAD_LEFT);
            $code = 'BV' . $jour . $mois . $annee . $compteurFormatte;

            // Vérifier si le code existe déjà
            $exists = DB::table('transactions')->where('code_transactions',$code)->exists();
        } while ($exists); // Continuer jusqu'à trouver un code unique

        return $code;
    }
}





if(!function_exists('convertirMontantEnLettres')) {
    function convertirMontantEnLettres($nombre)
    {
        $unites = ['', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf'];
        $dizaines = ['', 'dix', 'vingt', 'trente', 'quarante', 'cinquante', 'soixante', 'soixante-dix', 'quatre-vingt', 'quatre-vingt-dix'];

        if ($nombre == 0) {
            return "zéro franc CFA";
        }

        $lettres = '';

        // Gestion des milliers
        if ($nombre >= 1000) {
            $milliers = floor($nombre / 1000);
            $lettres .= $milliers == 1 ? "mille " : convertirMontantEnLettres($milliers) . " mille ";
            $nombre %= 1000;
        }

        // Gestion des centaines
        if ($nombre >= 100) {
            $centaines = floor($nombre / 100);
            $lettres .= $centaines == 1 ? "cent " : $unites[$centaines] . " cent ";
            $nombre %= 100;
        }

        // Gestion des dizaines et unités
        if ($nombre > 0) {
            if ($nombre <= 16) {
                $lettres .= [
                    '', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf',
                    'dix', 'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize'
                ][$nombre];
            } elseif ($nombre < 20) {
                $lettres .= "dix-" . $unites[$nombre - 10];
            } else {
                $dizaine = floor($nombre / 10);
                $unite = $nombre % 10;

                if ($dizaine == 7 || $dizaine == 9) {
                    $lettres .= $dizaines[$dizaine - 1] . "-dix";
                    if ($unite > 0) {
                        $lettres .= "-" . $unites[$unite];
                    }
                } else {
                    $lettres .= $dizaines[$dizaine];
                    if ($unite > 0) {
                        $lettres .= ($dizaine == 8 ? "-" : "-") . $unites[$unite];
                    }
                }
            }
        }

        return trim($lettres) . " franc" . ($nombre > 1 ? "s CFA" : " CFA");
    }
}

if(!function_exists('generateUniqueCode')) {
    function generateUniqueCode()
    {
        $mois = now()->format('m'); // Mois actuel
        $annee = now()->format('Y'); // Année actuelle

        // Trouver le dernier code pour ce mois et cette année
        $lastCode = DB::table('client')
            ->whereRaw('SUBSTRING(code, 3, 2) = ? AND SUBSTRING(code, 5, 4) = ?', [$mois, $annee])
            ->orderBy('id', 'desc')
            ->first();

        // Si un code existe, on récupère le dernier numéro et on l'incrémente
        $lastNumber = $lastCode ? (int) substr($lastCode->code, -6) : 0;

        do {
            $lastNumber++;
            $newNumber = str_pad($lastNumber, 6, '0', STR_PAD_LEFT); // Formater avec 6 chiffres
            $newCode = 'CL' . $mois . $annee . 'SM' . $newNumber;

            // Vérifier si le code existe déjà
            $exists = DB::table('client')->where('code', $newCode)->exists();

        } while ($exists); // Continuer jusqu'à trouver un code unique

        return $newCode;
    }

}


if(!function_exists('compterTransactions')) {
    function compterTransactions($code)
    {


        $count = DB::table('transaction')
            ->where('code_client','=',$code)
            ->count();
        return $count;


    }
}


if(!function_exists('ListesTransactions')) {
    function ListesTransactions($id,$entrepotID)
    {

        $transactions = DB::table('transactions')
            ->where('com_id', $id)
            ->where('entrepot_id', $entrepotID)
            ->select(
                'transactions.code_transactions',
                'transactions.date_transaction',
                DB::raw('COALESCE(SUM(avoir_transactions.quantite), 0) as total_quantite'),
                DB::raw('COALESCE(SUM(avoir_transactions.quantite * avoir_transactions.prix), 0) as total_montant')
            )
            ->join('avoir_transactions', 'transactions.code_transactions', '=', 'avoir_transactions.code_transaction')
            ->groupBy('transactions.code_transactions', 'transactions.date_transaction')
            ->orderBy('transactions.date_transaction', 'desc')
            ->get();

        foreach ($transactions as $transaction) {
            $transaction->produits = DB::table('avoir_transactions')
                ->join('produits', 'avoir_transactions.code_produit', '=', 'produits.code_produit')
                ->join('avoir_produit', 'avoir_produit.code_produit', '=', 'produits.code_produit')
                ->where('avoir_transactions.code_transaction', $transaction->code_transactions)
                ->where('avoir_produit.statut', 1)
                ->where('avoir_produit.entrepot_id', $entrepotID)
                ->select(
                    'produits.description',
                    'avoir_transactions.quantite',
                    'avoir_transactions.prix',
                    DB::raw('avoir_transactions.quantite * avoir_transactions.prix as montant')
                )
                ->get();
        }



        return $transactions;


    }
}


if(!function_exists('TransactionsExists')) {
    function TransactionsExists($dateSolde)
    {


        $exist = DB::table('transaction')
            ->whereDate('date_transaction',$dateSolde)
            ->exists();

        return $exist;

    }
}


if(!function_exists('calculerMontant')) {
    function calculerMontant($id)
    {
        $totaux = DB::table('transactions as t')
            ->join('avoir_transactions as a', 't.code_transactions', '=', 'a.code_transaction')
            ->where('t.com_id', $id)
            ->sum('a.quantite');

        $prixTotal = DB::table('transactions')
            ->where('transactions.com_id', '=', $id)
            ->sum('a_payer');


        $paiement = DB::table('paiement')
            ->where('id_agent', '=', $id)
            ->where('type_mouvement', '=', 'ver')
            ->sum('montant');

        $paiementDepenses = DB::table('paiement')
            ->where('id_agent', '=', $id)
            ->where('type_mouvement', '=', 'sor')
            ->sum('montant');

        return [$totaux,$prixTotal,$paiement,abs($prixTotal - $paiement),$paiementDepenses];

    }
}

