<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use  Yajra\DataTables\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class TransactionController extends Controller
{


    public function formatMontantToNumber($montant) {
        return (int) str_replace(' ', '', $montant);
    }


    public function LeanTransaction($code){
        $transaction = DB::table('transaction')
            ->join('client','client.code','=','transaction.code_client')
            ->where('transaction.code_transaction','=',$code)->first();

        return response()->json($transaction);
    }

    public function index(){

        $typesUsers = ucfirst(Auth()->user()->groupe);

        $soldeJour = DB::table('solde')
            ->where('date_solde', date('Y-m-d'))->first();


        $typesUV = [
            ['id' => 'AU', 'libelle' => 'Achat UV'],
            ['id' => 'RU', 'libelle' => 'Retour UV']
        ];


        $typesCASH = [
            ['id' => 'EC', 'libelle' => 'Encaissement Achat UV'],
            ['id' => 'DC', 'libelle' => 'Decaissement pour retour UV'],
            ['id' => 'DP', 'libelle' => 'Dépot'],
            ['id' => 'RE', 'libelle' => 'Retrait']
        ];


        $operateurs = [];


        $operateursTotal = [

            ['id' => 'orange', 'libelle' => 'Orange'],
            ['id' => 'wave', 'libelle' => 'Wave'],
            ['id' => 'djamo', 'libelle' => 'Djamo'],
            ['id' => 'push', 'libelle' => 'Push'],
            ['id' => 'trmo', 'libelle' => 'Tresor Monney'],
        ];


        $operateursWave = [
            ['id' => 'wave', 'libelle' => 'Wave'],
        ];

        $operateursNonWve = [

            ['id' => 'orange', 'libelle' => 'Orange'],

            ['id' => 'djamo', 'libelle' => 'Djamo'],
            ['id' => 'push', 'libelle' => 'Push'],
            ['id' => 'trmo', 'libelle' => 'Tresor Monney'],
        ];


        if ($typesUsers == 'SUW'):
            $operateurs = $operateursWave;
        elseif ($typesUsers == 'SUO'):
            $operateurs = $operateursNonWve;
        else:
            $operateurs = $operateursTotal;
        endif;


        $typesTrans = ['AU', 'RU', 'EC', 'DC'];

        $transactionsJour =  DB::table('transaction')
            ->select('type_transaction', DB::raw('SUM(montant) as total'))
            ->whereDate('date_transaction', date('Y-m-d'))
            ->where('statut',0)
            ->groupBy('type_transaction')
            ->get()
            ->pluck('total', 'type_transaction') // Transforme en tableau associatif
            ->toArray();


        $result = array_merge(array_fill_keys($typesTrans, 0), $transactionsJour);


        $clients = DB::table('client')
            ->get();

        $agents = DB::table('users')
            ->where('groupe','=','AG')
            ->get();


        return view('transaction.index',compact('clients','operateurs','soldeJour','typesUV','typesCASH','result','clients','agents','typesUsers'));
    }


    public function transaction($code){

        $soldeJour = DB::table('solde')
            ->where('date_solde', date('Y-m-d'))->first();

        $client = DB::table('client')
            ->where('code','=',$code)
            ->orderBy('identifiant','ASC')
            ->first();


        $types = ['AU', 'RU', 'EC', 'DC']; // Liste des types de transactions

        $transactions = DB::table('transaction')
            ->where('statut', 0)
            ->select('type_transaction', DB::raw('COALESCE(SUM(montant), 0) as total_montant'))
            ->whereDate('date_transaction', Carbon::today()) // Filtrer par la date du jour
            ->groupBy('type_transaction')
            ->pluck('total_montant', 'type_transaction'); // Retourne un tableau clé-valeur

// Assurer que tous les types sont présents avec une valeur par défaut de 0
        $results = [];
        foreach ($types as $type) {
            $results[$type] = $transactions[$type] ?? 0;
        }


        $transactionDifference = DB::table('transaction')
            ->select(DB::raw('SUM(CASE WHEN type_transaction = "AU" THEN montant ELSE 0 END) - SUM(CASE WHEN type_transaction = "RU" THEN montant ELSE 0 END) AS difference'))
            ->where('statut', 0)
            ->where('code_client', $code)
            ->first();

        $agents = DB::table('users')
            ->where('groupe','=','AG')
            ->get();

        $typesTransactions = [
            ['id' => 'AU', 'libelle' => 'Achat UV'],
            ['id' => 'RU', 'libelle' => 'Retour UV'],
            ['id' => 'EC', 'libelle' => 'Encaissement Achat UV'],
            ['id' => 'DC', 'libelle' => 'Decaissement pour retour UV']
        ];

        $transactionClients = DB::table('transaction')
            ->where('code_client', $code)
            ->orderByDesc('date_transaction')
            ->get();



        $total = DB::table('transaction')
            ->select(DB::raw('SUM(CASE WHEN type_transaction = "AU" THEN montant
            WHEN type_transaction = "EC" THEN montant
            WHEN type_transaction = "RU" THEN -montant
            WHEN type_transaction = "DC" THEN -montant
            ELSE 0 END) AS total'))
            ->where('code_client', $code)
            ->where('statut', 0)
            ->first();

        // Si aucun résultat, retournez 0
        $totalAmount = $total->total ?? 0;




        return view('transaction.create',compact('client','typesTransactions','agents','results','soldeJour','transactionDifference','transactionClients','totalAmount'));
    }

    public function store(Request $request){

        $codeTransaction = genererCode($request->type_transaction);

        $data = array(
            'type_transaction'=>$request->type_transaction,
            'code_client'=>$request->code_client,
            'code_transaction'=>$codeTransaction,
            'montant'=>$this->formatMontantToNumber($request->montant),
            'id_agent'=>$request->agent,
            'affiliation'=>$request->affiliation,
            'operateur'=>$request->operateur,
            'date_transaction'=>$request->date_transaction,
        );

        if (isset($request->agent)){
            $data['course'] = 1;
            $data['statut'] = 1;
        }


        DB::table('transaction')
            ->insert($data);

        storeActivity('Transactions','Client',"Transactions de $request->type_transaction du client ".$request->code_client);
        return back()->with('message', 'Transaction effectué avec succès du client');

    }

    public function update(Request $request){

        $codeTransaction = $request->code_transaction;
        $data = array();
        if (isset($request->agent)){
            $data['id_agent']= $request->agent;
            $data['course'] = 1;
            $data['statut'] = 1;
        }else{
            $data['id_agent']= null;
            $data['course'] = 0;
            $data['statut'] = 0;
        }


        DB::table('transaction')
            ->where('code_transaction','=',$codeTransaction)
            ->update($data);

        storeActivity('Transactions','Client',"Modification de $request->type_transaction du client ".$request->code_client);
        return back()->with('message', 'Transaction effectué avec succès du client');

    }
    public function delete(Request $request)
    {
        $codeTransaction = $request->input('code_transaction');

        // Vérifier si la transaction existe
        $transaction = DB::table('transaction')
            ->where('code_transaction', $codeTransaction)
            ->first();

        if (!$transaction) {
            return back()->with('error', 'Transaction introuvable.');
        }

        try {
            DB::transaction(function () use ($codeTransaction, $transaction) {
                // Suppression de la transaction principale
                DB::table('transaction')
                    ->where('code_transaction', $codeTransaction)
                    ->delete();

                // Suppression des éventuelles références dans avoir_transaction
                DB::table('avoir_transaction')
                    ->where('code_transaction', $codeTransaction)
                    ->delete();

                // Mise à jour si un code d'encaissement est lié
                if (!is_null($transaction->code_encaissement)) {
                    DB::table('transaction')
                        ->where('code_transaction', $transaction->code_encaissement)
                        ->update([
                            'statut' => 2,
                            'paie'   => 1,
                        ]);
                }
            });

            return back()->with('message', 'Transaction supprimée avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de la transaction : ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la suppression.');
        }
    }


    public function getData(Request $request)
    {
        $date = $request->input('date');



        $codeNotUpdate = DB::table('avoir_transaction')
            ->join('transaction', 'transaction.code_transaction', '=', 'avoir_transaction.code_transaction')
            ->whereDate('transaction.date_transaction', $date)
            ->whereNotNull('transaction.code_encaissement')
            ->pluck('avoir_transaction.code_transaction')
            ->toArray();

        $query = DB::table('transaction')
            ->join('client', 'client.code', '=', 'transaction.code_client')
            ->leftJoin('users', 'users.id', '=', 'transaction.id_agent')
            ->where('transaction.paie', 0)
            ->whereDate('transaction.date_transaction', $date)
            ->select([
                'transaction.code_transaction',
                'transaction.date_transaction',
                'client.raison_sociale as client',
                'transaction.type_transaction as transaction',
                'transaction.affiliation',
                DB::raw('IFNULL(users.personnel, "bureau") as agent'),
                'transaction.operateur',
                'transaction.montant'
            ]);

        if ($request->filled('type_transaction')) {
            $query->where('transaction.type_transaction', $request->input('type_transaction'));
        }

        if ($request->filled('operateur')) {
            $query->where('transaction.operateur', $request->input('operateur'));
        }

        if ($request->filled('agent')) {
            if($request->filled('agent') == -1){
                $query->whereNull('transaction.id_agent');
            }else{
                $query->where('transaction.id_agent', $request->input('agent'));
            }

        }

        if ($request->filled('search.value')) {
            $search = $request->input('search')['value'];
            $query->where(function ($q) use ($search) {
                $q->where('client.raison_sociale', 'like', "%{$search}%")
                    ->orWhere('transaction.type_transaction', 'like', "%{$search}%")
                    ->orWhere('transaction.affiliation', 'like', "%{$search}%")
                    ->orWhere('users.personnel', 'like', "%{$search}%");
            });
        }

        $totalQuery = (clone $query)->selectRaw("
        SUM(CASE
            WHEN transaction.type_transaction IN ('AU', 'DC', 'RE') THEN -transaction.montant
            ELSE transaction.montant
        END) as total
    ");
        $totalResult = $totalQuery->first(); // Récupérer la première ligne du résultat
        $total = $totalResult ? $totalResult->total : 0; // Accéder à la propriété 'total'



        return DataTables::of($query)
            ->editColumn('montant', function ($transaction) {
                return in_array($transaction->transaction, ['AU', 'DC', 'RE'])
                    ? -$transaction->montant
                    : $transaction->montant;
            })
            ->addColumn('action', function ($transaction) use ($codeNotUpdate) {
                $buttons = '<button class="btn btn-primary btn-sm recu-btn" data-code="' . e($transaction->code_transaction) . '">Reçu</button>';
                if (!in_array($transaction->code_transaction, $codeNotUpdate)) {
                    $buttons .= ' <button class="btn btn-warning btn-sm edit-btn" data-code="' . e($transaction->code_transaction) . '" data-type="' . e($transaction->transaction) . '">Modifier</button>';
                }
                $buttons .= ' <button class="btn btn-danger btn-sm delete-btn" data-code="' . e($transaction->code_transaction) . '">Supprimer</button>';
                return $buttons;
            })
            ->with([
                'total' => $total,
                'draw' => intval($request->input('draw'))
            ])
            ->rawColumns(['action'])
            ->toJson();
    }




    public function getSoldesXXX(Request $request)
    {
        $date = $request->input('date', now()->toDateString());


        $types = [
            ['libelle' => 'CASH', 'colonne' => 'cash'],
            ['libelle' => 'UV ORANGE', 'colonne' => 'uv_orange'],
            ['libelle' => 'UV WAVE', 'colonne' => 'uv_wave'],
            ['libelle' => 'UV DJAMO', 'colonne' => 'uv_djamo'],
            ['libelle' => 'UV PUSH', 'colonne' => 'uv_push'],
            ['libelle' => 'UV TRESOR MONNEY', 'colonne' => 'uv_trmo'],
            ['libelle' => 'DETTE', 'colonne' => 'dette'],
            ['libelle' => 'AVOIR', 'colonne' => 'avoir'],
        ];

        $soldes = [];
        $totalDepart = 0;
        $totalEncours = 0;

        foreach ($types as $type) {

            // Handle CASH first
            if ($type['libelle'] == 'CASH'){
                // Calculating CASH
                $soldeCash = DB::table('solde')->whereDate('date_solde', $date)->sum('cash');
                $totalUvEncaissement = DB::table('transaction')->where('type_transaction','=','EC')->whereDate('date_transaction','=',$date)->sum('montant');
                $totalDecaissement = DB::table('transaction')->where('type_transaction','=','DC')->whereDate('date_transaction','=',$date)->sum('montant');
                $totalDepot = DB::table('transaction')->where('type_transaction','=','DP')->whereDate('date_transaction','=',$date)->sum('montant');
                $totalRetrait = DB::table('transaction')->where('type_transaction','=','RE')->whereDate('date_transaction','=',$date)->sum('montant');

                $cashEnccours = $soldeCash - $totalDecaissement + $totalUvEncaissement + $totalDepot - $totalRetrait;
                $soldes[strtolower($type['colonne']) . '_depart'] = formatNumber($soldeCash);
                $soldes[strtolower($type['colonne']) . '_encours'] = formatNumber($cashEnccours);

                $totalDepart += $soldeCash;
                $totalEncours += $cashEnccours;
            }

            // Handle UV types
            if (in_array($type['libelle'], ['UV ORANGE', 'UV WAVE', 'UV DJAMO', 'UV PUSH', 'UV TRESOR MONNEY'])) {

                $colonnePerso = str_replace('uv_', '', $type['colonne']);
                $soldeUv = DB::table('solde')->whereDate('date_solde', $date)->sum($type['colonne']);
                $totalUvachatUV = DB::table('transaction')->where('type_transaction','=','AU')->where('operateur',$colonnePerso)->whereDate('date_transaction','=',$date)->sum('montant');
                $totalURetourUV = DB::table('transaction')->where('type_transaction','=','RU')->where('operateur',$colonnePerso)->whereDate('date_transaction','=',$date)->sum('montant');
                $totalDepot = DB::table('transaction')
                    ->where('type_transaction','=','DP')
                    ->where('operateur','=',$colonnePerso)
                    ->whereDate('date_transaction','=',$date)
                    ->sum('montant');

                $totalRetrait = DB::table('transaction')
                    ->where('type_transaction','=','RE')
                    ->where('operateur','=',$colonnePerso)
                    ->whereDate('date_transaction','=',$date)
                    ->sum('montant');
                $soldesUvENcours = $soldeUv - $totalUvachatUV + $totalURetourUV + $totalDepot - $totalRetrait;
                $soldes[strtolower($type['colonne']) . '_depart'] = formatNumber($soldeUv);
                $soldes[strtolower($type['colonne']) . '_encours'] = formatNumber($soldesUvENcours);

                $totalDepart += $soldeUv;
                $totalEncours += $soldesUvENcours;
            }

            // Handle DETTE (with subtraction logic)
            if ($type['libelle'] == 'DETTE') {
                $soldeUv = DB::table('solde')->whereDate('date_solde', $date)->sum('dette');
                $totalURetourUV = DB::table('transaction')->where('type_transaction','=','AU')->whereDate('date_transaction','=',$date)->sum('montant');

                $totalURetourAchatUV = DB::table('transaction')
                    ->where('type_transaction','=','RU')
                    ->whereDate('date_transaction','=',$date)
                    ->sum('montant');

                $totalDecaissement = DB::table('transaction')->where('type_transaction','=','EC')->whereDate('date_transaction','=',$date)->sum('montant');

                $totalDecaissementEnc = DB::table('transaction')
                    ->where('type_transaction','=','DC')
                    ->whereDate('date_transaction','=',$date)
                    ->sum('montant');


                $tchekDette = ($totalURetourUV - $totalURetourAchatUV) - ($totalDecaissement - $totalDecaissementEnc);


                $soldesDettesEncours = $soldeUv + $tchekDette;
                $soldes[strtolower($type['colonne']) . '_depart'] = formatNumber($soldeUv);
                $soldes[strtolower($type['colonne']) . '_encours'] = formatNumber($soldesDettesEncours);

                $totalDepart += $soldeUv;
                $totalEncours += $soldesDettesEncours;
            }

            // Handle AVOIR (similar to DETTE with subtraction logic)
            if ($type['libelle'] == 'AVOIR') {
                $soldeUv = DB::table('solde')->whereDate('date_solde', $date)->sum('avoir');
                $totalURetourUV = DB::table('transaction')->where('type_transaction','=','RU')->whereDate('date_transaction','=',$date)->sum('montant');
                $totalDecaissement = DB::table('transaction')->where('type_transaction','=','DC')->whereDate('date_transaction','=',$date)->sum('montant');
                $tchekDette = $totalURetourUV - $totalDecaissement;

                $tchekDette = max(0, $tchekDette);

                $soldesDettesEncours = $soldeUv + $tchekDette;
                $soldes[strtolower($type['colonne']) . '_depart'] = formatNumber($soldeUv);
                $soldes[strtolower($type['colonne']) . '_encours'] = formatNumber($soldesDettesEncours);

                $totalDepart += $soldeUv;
                $totalEncours += $soldesDettesEncours;
            }

        }

// Add Total row
        $soldes['total_depart'] = formatNumber($totalDepart);
        $soldes['total_encours'] = formatNumber($totalEncours);

        return response()->json($soldes);

    }
    public function getSoldes(Request $request)
    {
        $date = $request->input('date', now()->toDateString());

        $totalCash = 0;
        $totalDette = 0;
        $totalAvoir = 0;
        $totalDepart = 0;
        $totalEncours = 0;

        $operateurs = ['moov', 'orange', 'mtn', 'wave', 'djamo', 'push', 'trmo'];
        $totalUV = array_fill_keys($operateurs, 0);

        // Récupération du solde initial
        $soldesData = DB::table('solde')
            ->whereDate('date_solde', $date)
            ->first();

        if (!$soldesData) {
            return response()->json(['error' => 'Aucune donnée trouvée pour cette date.'], 404);
        }

        // Récupération et traitement des transactions en une seule requête
        $transactions = DB::table('transaction')
            ->whereDate('date_transaction', $date)
            ->where('paie', 0)
            ->get();

        foreach ($transactions as $transaction) {
            $montant = intval($transaction->montant);
            $operateur = $transaction->operateur;

            switch ($transaction->type_transaction) {
                case 'AU': // Achat UV
                case 'DP': // Dépôt
                    if (isset($totalUV[$operateur])) {
                        $totalUV[$operateur] -= $montant;
                    }
                    if ($transaction->type_transaction === 'DP') {
                        $totalCash += $montant;
                    }
                    break;

                case 'RU': // Retour UV
                case 'RE': // Retrait
                    if (isset($totalUV[$operateur])) {
                        $totalUV[$operateur] += $montant;
                    }
                    if ($transaction->type_transaction === 'RE') {
                        $totalCash -= $montant;
                    }
                    break;

                case 'EC': // Encaissement Achat UV
                    $totalCash += $montant;
                    break;

                case 'DC': // Décaissement Retour UV
                    $totalCash -= $montant;
                    break;
            }
        }

        // Récupération des clients débiteurs et créditeurs en une seule requête
        $clientsSoldes = DB::table('transaction')
            ->join('client', 'client.code', '=', 'transaction.code_client')
            ->whereIn('transaction.type_transaction', ['RU', 'EC', 'AU', 'DC'])
            ->where('transaction.paie', 0)
            ->whereDate('transaction.date_transaction', '<=', $date)
            ->select(
                'client.identifiant as client',
                DB::raw("SUM(
                CASE
                    WHEN transaction.type_transaction IN ('RU', 'EC') THEN transaction.montant
                    WHEN transaction.type_transaction IN ('AU', 'DC') THEN -transaction.montant
                    ELSE 0
                END
            ) AS solde")
            )
            ->groupBy('transaction.code_client')
            ->get();

        $clientsDebiteurs = $clientsSoldes->where('solde', '<', 0);
        $clientsCrediteurs = $clientsSoldes->where('solde', '>', 0);

        $totalDette = abs($clientsDebiteurs->sum('solde'));
        $totalAvoir = $clientsCrediteurs->sum('solde');

        // Calcul des soldes
        $soldes = [
            'cash_depart' => formatNumber($soldesData->cash),
            'cash_encours' => formatNumber($totalCash + $soldesData->cash),
            'dette_depart' => formatNumber($soldesData->dette),
            'dette_encours' => formatNumber($totalDette),
            'avoir_depart' => formatNumber($soldesData->avoir),
            'avoir_encours' => formatNumber($totalAvoir),
        ];

        // Calcul du total initial et en cours
        $totalDepart = $soldesData->cash + $soldesData->dette - $soldesData->avoir;
        $totalEncours = $totalCash + $soldesData->cash + $totalDette - $totalAvoir;

        foreach ($operateurs as $operateur) {
            $departUV = $soldesData->{'uv_' . $operateur};
            $encoursUV = $totalUV[$operateur] + $departUV;

            $soldes['uv_' . $operateur . '_depart'] = formatNumber($departUV);
            $soldes['uv_' . $operateur . '_encours'] = formatNumber($encoursUV);

            $totalDepart += $departUV;
            $totalEncours += $encoursUV;
        }

        $soldes['total_depart'] = formatNumber($totalDepart);
        $soldes['total_encours'] = formatNumber($totalEncours);

        return response()->json($soldes);
    }
    public function pointJour()
    {
        $date = date('Y-m-d');



        $totalCash = 0;
        $totalUV = [
            'moov' => 0, 'orange' => 0, 'mtn' => 0, 'wave' => 0,
            'djamo' => 0, 'push' => 0, 'trmo' => 0
        ];
        $totalDette = 0;
        $totalAvoir = 0;


        $soldes = [];

        $soldesData = DB::table('solde')
            ->whereDate('date_solde',$date)
            ->first();

        $operateurs = [
            ['id' => 'orange', 'libelle' => 'Orange'],
            ['id' => 'wave', 'libelle' => 'Wave'],
            ['id' => 'djamo', 'libelle' => 'Djamo'],
            ['id' => 'push', 'libelle' => 'Push'],
            ['id' => 'trmo', 'libelle' => 'Tresor Monney'],
        ];


        $transactions = DB::table('transaction')
            ->join('client','client.code','=','transaction.code_client')
            ->whereDate('date_transaction',$date)
            ->get();

        // Initialisation des totaux globaux
        $totalDepart = 0;
        $totalEncours = 0;

        foreach ($transactions as $transaction) {
            $montant = intval($transaction->montant);
            $type = $transaction->type_transaction;
            $operateur = $transaction->operateur;

            switch ($type) {
                case 'AU': // Achat UV
                    if (isset($totalUV[$operateur])) {
                        $totalUV[$operateur] -= $montant;  // On enlève les UV achetées
                    }

                    break;

                case 'RU': // Retour UV
                    if (isset($totalUV[$operateur])) {
                        $totalUV[$operateur] += $montant; // On ajoute les UV retournées
                    }

                    break;

                case 'EC': // Encaissement Achat UV
                    $totalCash += $montant;

                    break;

                case 'DC': // Décaissement Retour UV
                    $totalCash -= $montant;

                    break;

                case 'DP': // Dépôt = Achat UV + Encaissement UV
                    if (isset($totalUV[$operateur])) {
                        $totalUV[$operateur] -= $montant;  // On enlève les UV pour le dépôt
                    }
                    $totalCash += $montant;

                    break;

                case 'RE': // Retrait = Retour UV + Décaissement pour Retour
                    if (isset($totalUV[$operateur])) {
                        $totalUV[$operateur] += $montant; // On ajoute les UV retournées
                    }
                    $totalCash -= $montant;

                    break;
            }
        }

        $clientsDebiteurs = DB::table('transaction')
            ->join('client','client.code','=','transaction.code_client')
            ->where('transaction.paie','=',0)
            ->whereIn('transaction.type_transaction',['RU','EC','AU','DC'])
            ->whereDate('transaction.date_transaction','<=',$date)
            ->select('client.raison_sociale as client',
                DB::raw("SUM(
            CASE
                WHEN transaction.type_transaction IN ('RU', 'EC') THEN transaction.montant
                WHEN transaction.type_transaction IN ('AU', 'DC') THEN -transaction.montant
                ELSE 0
            END
        ) AS solde"))
            ->groupBy('transaction.code_client')
            ->having('solde', '<', 0)
            ->orderBy('solde', 'asc')
            ->get();

        $clientsCrediteurs = DB::table('transaction')
            ->join('client','client.code','=','transaction.code_client')
            ->where('transaction.paie','=',0)
            ->whereDate('transaction.date_transaction','<=',$date)
            ->whereIn('transaction.type_transaction',['RU','EC','AU','DC'])
            ->select('client.raison_sociale as client',
                DB::raw("SUM(
            CASE
                WHEN transaction.type_transaction IN ('RU', 'EC') THEN transaction.montant
                WHEN transaction.type_transaction IN ('AU', 'DC') THEN -transaction.montant
                ELSE 0
            END
        ) AS solde"))
            ->groupBy('transaction.code_client')
            ->having('solde', '>', 0)
            ->orderBy('solde', 'desc')
            ->get();



        $totalDette = $clientsDebiteurs->sum('solde') * -1; // On convertit en positif
        $totalAvoir = $clientsCrediteurs->sum('solde'); // Directement positif



        $soldes['cash_depart'] = formatNumber($soldesData->cash);
        $soldes['cash_encours'] = formatNumber($totalCash + $soldesData->cash);

        $soldes['dette_depart'] = formatNumber($soldesData->dette);
        $soldes['dette_encours'] = formatNumber($totalDette);

        $soldes['avoir_depart'] = formatNumber($soldesData->avoir);
        $soldes['avoir_encours'] = formatNumber($totalAvoir);

        $totalDepart += $soldesData->cash;
        $totalEncours += ($totalCash + $soldesData->cash);

// Ajout des valeurs de dette
        $totalDepart += $soldesData->dette;
        $totalEncours += ($totalDette);

// Ajout des valeurs d'avoir
        $totalDepart -= $soldesData->avoir;
        $totalEncours -= ($totalAvoir);

        foreach ($operateurs as $operateur){
            $soldes['uv_'.$operateur['id'].'_depart'] = formatNumber($soldesData->{'uv_'.$operateur['id']});
            $soldes['uv_'.$operateur['id'].'_encours'] = formatNumber($totalUV[$operateur['id']] + $soldesData->{'uv_'.$operateur['id']});
            $totalDepart += $soldesData->{'uv_'.$operateur['id']};
            $totalEncours += ($totalUV[$operateur['id']] + $soldesData->{'uv_'.$operateur['id']});
        }

        $soldes['total_depart'] = formatNumber($totalDepart);
        $soldes['total_encours'] = formatNumber($totalEncours);


        $clients = DB::table('transaction')
            ->join('client','client.code','=','transaction.code_client')
            ->where('transaction.statut',0)
            ->select('identifiant','code_client',
                DB::raw("SUM(CASE WHEN type_transaction IN ('AU', 'DC','RE') THEN -montant ELSE montant END) AS solde"))
            ->groupBy('code_client')
            ->get();



        $titre = "POINT DE ".date('d-m-Y');


        $pdf = Pdf::loadView('impression.point',compact('soldes','clientsDebiteurs','clientsCrediteurs','titre'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream($titre.'.pdf');


    }


    public function recu($code){
        $transaction = DB::table('transaction')
            ->join('client', 'client.code', '=', 'transaction.code_client')
            ->leftJoin('users', 'users.id', '=', 'transaction.id_agent')
            ->where('transaction.code_transaction', $code)
            ->select([
                'transaction.code_transaction as code_transaction',
                'transaction.date_transaction as date_transaction',
                'client.raison_sociale as client',
                'transaction.type_transaction as transaction',
                'transaction.affiliation as affiliation',
                'users.personnel as agent',
                'transaction.operateur as operateur',
                'transaction.montant as montant'
            ])->first();

        $pdf = Pdf::loadView('impression.recu',compact('transaction'));

        return $pdf->stream('recu_'.$code.'.pdf');

    }





}
