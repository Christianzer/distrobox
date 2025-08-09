<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SoldeController extends Controller
{


    public function indexAAA(){

        $soldes = DB::table('solde')
            ->orderByDesc('date_solde')
            ->get();

        $soldeJour = DB::table('solde')
            ->orderByDesc('date_solde')
            ->where('date_solde', date('Y-m-d'))->first();

        $operateurs = [
            ['id' => 'orange', 'libelle' => 'Orange'],
            ['id' => 'wave', 'libelle' => 'Wave'],
            ['id' => 'djamo', 'libelle' => 'Djamo'],
            ['id' => 'push', 'libelle' => 'Push'],
            ['id' => 'trmo', 'libelle' => 'Tresor Monney'],
        ];

        $types = ['AU', 'RU', 'EC', 'DC'];

        $transactionsJour =  DB::table('transaction')
            ->select('type_transaction', DB::raw('SUM(montant) as total'))
            ->whereDate('date_transaction', date('Y-m-d'))
            ->where('statut',0)
            ->groupBy('type_transaction')
            ->get()
            ->pluck('total', 'type_transaction') // Transforme en tableau associatif
            ->toArray();

        $transactionsExist = DB::table('transaction')
            ->whereDate('date_transaction', date('Y-m-d'))
            ->exists();

        $total = 0;

        foreach ($soldes as $solde){

            $total = $solde->cash + array_sum(array_map(function ($op) use ($solde) {
                    return $solde->{'uv_' . $op['id']} ?? 0;
                }, $operateurs)) - $solde->dette + $solde->avoir;
        }


        $result = array_merge(array_fill_keys($types, 0), $transactionsJour);




        $soldesTestData = DB::table('solde')
            ->orderByDesc('date_solde')
            ->get();

        $ToutSolde = [];
        $soldesDD = [];
        if (empty($soldesTestData)){
            $soldesDD = [];
        }else{
            foreach ($soldesTestData as $data){
                $date =  $data->date_solde;

                $typesFormat = [
                    ['libelle' => 'CASH', 'colonne' => 'cash'],
                    ['libelle' => 'UV ORANGE', 'colonne' => 'uv_orange'],
                    ['libelle' => 'UV WAVE', 'colonne' => 'uv_wave'],
                    ['libelle' => 'UV DJAMO', 'colonne' => 'uv_djamo'],
                    ['libelle' => 'UV PUSH', 'colonne' => 'uv_push'],
                    ['libelle' => 'UV TRESOR MONNEY', 'colonne' => 'uv_trmo'],
                    ['libelle' => 'DETTE', 'colonne' => 'dette'],
                    ['libelle' => 'AVOIR', 'colonne' => 'avoir'],
                ];


                $soldesDD = [];

                foreach ($typesFormat as $type) {

                    if ($type['libelle'] == 'CASH'){

                        $soldeCash = DB::table('solde')
                            ->whereDate('date_solde', $date)
                            ->sum('cash');

                        $totalUvEncaissement = DB::table('transaction')
                            ->where('type_transaction','=','EC')
                            ->whereDate('date_transaction','=',$date)
                            ->sum('montant');

                        $totalDecaissement = DB::table('transaction')
                            ->where('type_transaction','=','DC')
                            ->whereDate('date_transaction','=',$date)
                            ->sum('montant');



                        $totalDepot = DB::table('transaction')
                            ->where('type_transaction','=','DP')
                            ->whereDate('date_transaction','=',$date)
                            ->sum('montant');

                        $totalRetrait = DB::table('transaction')
                            ->where('type_transaction', '=','RE')
                            ->whereDate('date_transaction','=',$date)
                            ->sum('montant');






                        $cashEnccours = $soldeCash - $totalDecaissement + $totalUvEncaissement + $totalDepot - $totalRetrait;



                        $soldesDD[strtolower($type['colonne']) . '_depart'] = formatNumber($soldeCash);

                        $soldesDD[strtolower($type['colonne']) . '_encours'] = formatNumber($cashEnccours);
                    }



                    elseif ($type['libelle'] == 'UV ORANGE') {

                        $soldeUv = DB::table('solde')
                            ->whereDate('date_solde', $date)
                            ->sum('uv_orange');

                        $totalUvachatUV = DB::table('transaction')
                            ->where('type_transaction','=','AU')
                            ->where('operateur','=','orange')
                            ->whereDate('date_transaction','=',$date)
                            ->sum('montant');

                        $totalURetourUV = DB::table('transaction')
                            ->where('type_transaction','=','RU')
                            ->where('operateur','=','orange')
                            ->whereDate('date_transaction','=',$date)
                            ->sum('montant');


                        $totalDepot = DB::table('transaction')
                            ->where('type_transaction','=','DP')
                            ->where('operateur','=','orange')
                            ->whereDate('date_transaction','=',$date)
                            ->sum('montant');

                        $totalRetrait = DB::table('transaction')
                            ->where('type_transaction','=','RE')
                            ->where('operateur','=','orange')
                            ->whereDate('date_transaction','=',$date)
                            ->sum('montant');


                        $soldesUvENcours = $soldeUv - $totalUvachatUV + $totalURetourUV + $totalDepot - $totalRetrait;

                        $soldesDD[strtolower($type['colonne']) . '_depart'] = formatNumber($soldeUv);
                        $soldesDD[strtolower($type['colonne']) . '_encours'] = formatNumber($soldesUvENcours);
                    }

                    elseif ($type['libelle'] == 'UV WAVE') {
                        $soldeUv = DB::table('solde')
                            ->whereDate('date_solde', $date)
                            ->sum('uv_wave');

                        $totalUvachatUV = DB::table('transaction')
                            ->where('type_transaction','=','AU')
                            ->where('operateur','=','wave')
                            ->whereDate('date_transaction','=',$date)
                            ->sum('montant');

                        $totalURetourUV = DB::table('transaction')
                            ->where('type_transaction','=','RU')
                            ->where('operateur','=','wave')
                            ->whereDate('date_transaction','=',$date)
                            ->sum('montant');

                        $totalDepot = DB::table('transaction')
                            ->where('type_transaction','=','DP')
                            ->where('operateur','=','wave')
                            ->whereDate('date_transaction','=',$date)
                            ->sum('montant');

                        $totalRetrait = DB::table('transaction')
                            ->where('type_transaction','=','RE')
                            ->where('operateur','=','wave')
                            ->whereDate('date_transaction','=',$date)
                            ->sum('montant');

                        $soldesUvENcours = $soldeUv - $totalUvachatUV + $totalURetourUV + $totalDepot - $totalRetrait;

                        $soldesDD[strtolower($type['colonne']) . '_depart'] = formatNumber($soldeUv);
                        $soldesDD[strtolower($type['colonne']) . '_encours'] = formatNumber($soldesUvENcours);
                    }
                    elseif ($type['libelle'] == 'UV DJAMO') {
                        $soldeUv = DB::table('solde')
                            ->whereDate('date_solde', $date)
                            ->sum('uv_djamo');

                        $totalUvachatUV = DB::table('transaction')
                            ->where('type_transaction','=','AU')
                            ->where('operateur','=','djamo')
                            ->whereDate('date_transaction','=',$date)
                            ->sum('montant');

                        $totalURetourUV = DB::table('transaction')
                            ->where('type_transaction','=','RU')
                            ->where('operateur','=','djamo')
                            ->whereDate('date_transaction','=',$date)
                            ->sum('montant');

                        $totalDepot = DB::table('transaction')
                            ->where('type_transaction','=','DP')
                            ->where('operateur','=','djamo')
                            ->whereDate('date_transaction','=',$date)
                            ->sum('montant');

                        $totalRetrait = DB::table('transaction')
                            ->where('type_transaction','=','RE')
                            ->where('operateur','=','djamo')
                            ->whereDate('date_transaction','=',$date)
                            ->sum('montant');

                        $soldesUvENcours = $soldeUv - $totalUvachatUV + $totalURetourUV + $totalDepot - $totalRetrait;

                        $soldesDD[strtolower($type['colonne']) . '_depart'] = formatNumber($soldeUv);
                        $soldesDD[strtolower($type['colonne']) . '_encours'] = formatNumber($soldesUvENcours);
                    }
                    elseif ($type['libelle'] == 'UV PUSH') {
                        $soldeUv = DB::table('solde')
                            ->whereDate('date_solde', $date)
                            ->sum('uv_push');

                        $totalUvachatUV = DB::table('transaction')
                            ->where('type_transaction','=','AU')
                            ->where('operateur','=','push')
                            ->whereDate('date_transaction','=',$date)
                            ->sum('montant');

                        $totalURetourUV = DB::table('transaction')
                            ->where('type_transaction','=','RU')
                            ->where('operateur','=','push')
                            ->whereDate('date_transaction','=',$date)
                            ->sum('montant');

                        $totalDepot = DB::table('transaction')
                            ->where('type_transaction','=','DP')
                            ->where('operateur','=','push')
                            ->whereDate('date_transaction','=',$date)
                            ->sum('montant');

                        $totalRetrait = DB::table('transaction')
                            ->where('type_transaction','=','RE')
                            ->where('operateur','=','push')
                            ->whereDate('date_transaction','=',$date)
                            ->sum('montant');

                        $soldesUvENcours = $soldeUv - $totalUvachatUV + $totalURetourUV + $totalDepot - $totalRetrait;

                        $soldesDD[strtolower($type['colonne']) . '_depart'] = formatNumber($soldeUv);
                        $soldesDD[strtolower($type['colonne']) . '_encours'] = formatNumber($soldesUvENcours);
                    }
                    elseif ($type['libelle'] == 'UV TRESOR MONNEY') {
                        $soldeUv = DB::table('solde')
                            ->whereDate('date_solde', $date)
                            ->sum('uv_trmo');

                        $totalUvachatUV = DB::table('transaction')
                            ->where('type_transaction','=','AU')
                            ->where('operateur','=','trmo')
                            ->whereDate('date_transaction','=',$date)
                            ->sum('montant');

                        $totalURetourUV = DB::table('transaction')
                            ->where('type_transaction','=','RU')
                            ->where('operateur','=','trmo')
                            ->whereDate('date_transaction','=',$date)
                            ->sum('montant');

                        $totalDepot = DB::table('transaction')
                            ->where('type_transaction','=','DP')
                            ->where('operateur','=','trmo')
                            ->whereDate('date_transaction','=',$date)
                            ->sum('montant');

                        $totalRetrait = DB::table('transaction')
                            ->where('type_transaction','=','RE')
                            ->where('operateur','=','trmo')
                            ->whereDate('date_transaction','=',$date)
                            ->sum('montant');

                        $soldesUvENcours = $soldeUv - $totalUvachatUV + $totalURetourUV + $totalDepot - $totalRetrait;

                        $soldesDD[strtolower($type['colonne']) . '_depart'] = formatNumber($soldeUv);
                        $soldesDD[strtolower($type['colonne']) . '_encours'] = formatNumber($soldesUvENcours);
                    }


                    elseif ($type['libelle'] == 'DETTE'){

                        $soldeUv = DB::table('solde')
                            ->whereDate('date_solde', $date)
                            ->sum('dette');

                        $totalURetourUV = DB::table('transaction')
                            ->where('type_transaction','=','AU')
                            ->whereDate('date_transaction','=',$date)
                            ->sum('montant');


                        $totalURetourAchatUV = DB::table('transaction')
                            ->where('type_transaction','=','RU')
                            ->whereDate('date_transaction','=',$date)
                            ->sum('montant');




                        $totalDecaissement = DB::table('transaction')
                            ->where('type_transaction','=','EC')
                            ->whereDate('date_transaction','=',$date)
                            ->sum('montant');

                        $totalDecaissementEnc = DB::table('transaction')
                            ->where('type_transaction','=','DC')
                            ->whereDate('date_transaction','=',$date)
                            ->sum('montant');





                        $totalUAvoirUV = DB::table('transaction')
                            ->where('type_transaction','=','RU')
                            ->whereDate('date_transaction','=',$date)
                            ->sum('montant');



                        $totalAvoirDecaissement = DB::table('transaction')
                            ->where('type_transaction','=','DC')
                            ->whereDate('date_transaction','=',$date)
                            ->sum('montant');


                        $tchekDetteAvoir = $totalUAvoirUV - $totalAvoirDecaissement;



                        $tchekDetteAvoir = max(0, $tchekDetteAvoir);





                        $tchekDette = ($totalURetourUV - $totalURetourAchatUV) - ($totalDecaissement - $totalDecaissementEnc);




                        $soldesDettesEncours = $soldeUv + $tchekDette;


                        $soldesDD[strtolower($type['colonne']) . '_depart'] = formatNumber($soldeUv);

                        $soldesDD[strtolower($type['colonne']) . '_encours'] = formatNumber($soldesDettesEncours);
                    }


                    elseif ($type['libelle'] == 'AVOIR'){

                        $soldeUv = DB::table('solde')
                            ->whereDate('date_solde', $date)
                            ->sum('avoir');

                        $totalURetourUV = DB::table('transaction')
                            ->where('type_transaction','=','RU')
                            ->whereDate('date_transaction','=',$date)
                            ->sum('montant');



                        $totalDecaissement = DB::table('transaction')
                            ->where('type_transaction','=','DC')
                            ->whereDate('date_transaction','=',$date)
                            ->sum('montant');


                        $tchekDette = $totalURetourUV - $totalDecaissement;

                        $tchekDette = max(0, $tchekDette);


                        $soldesDettesEncours = $soldeUv + $tchekDette;


                        $soldesDD[strtolower($type['colonne']) . '_depart'] = formatNumber($soldeUv);

                        $soldesDD[strtolower($type['colonne']) . '_encours'] = formatNumber($soldesDettesEncours);
                    }






                }


                $element = array(
                    'date_solde'=> $date,
                    'cloturer'=> $data->cloturer,
                    'id_solde'=> $data->id_solde,
                    'donnees'=>$soldesDD
                );

                array_push($ToutSolde,$element);
            }
        }













        return view('solde.index',compact('soldes','soldeJour','result','operateurs','transactionsExist','total','soldesDD','ToutSolde'));
    }
    public function index()
    {
        $today = now()->toDateString();

        $soldeJour = DB::table('solde')
            ->whereDate('date_solde', $today)
            ->latest('date_solde')
            ->first();

        $transactionsExist = DB::table('transaction')
            ->whereDate('date_transaction', $today)
            ->exists();

        $operateurs = [
            ['id' => 'orange', 'libelle' => 'Orange'],
            ['id' => 'wave', 'libelle' => 'Wave'],
            ['id' => 'djamo', 'libelle' => 'Djamo'],
            ['id' => 'push', 'libelle' => 'Push'],
            ['id' => 'trmo', 'libelle' => 'Tresor Monney'],
        ];

        // 👉 on limite le nombre de dates à charger (ex : 7 derniers jours)
        $dataSoldes = DB::table('solde')
            ->select('id_solde', 'date_solde', 'cloturer', 'cash', 'uv_orange', 'uv_wave', 'uv_djamo', 'uv_push', 'uv_trmo', 'dette', 'avoir')
            ->orderByDesc('date_solde')
            ->limit(5)
            ->get();


        $soldes = $dataSoldes->map(function ($solde) use ($operateurs) {
            $date = $solde->date_solde;

            // 🧠 On utilise le cache par date de solde
            $cacheKey = "resume_solde_{$date}";
            $resume = Cache::remember($cacheKey, now()->addMinutes(15), function () use ($date) {
                return $this->calculResumeSolde($date);
            });

            return [
                'id_solde' => $solde->id_solde,
                'date_solde' => $date,
                'cloturer' => $solde->cloturer,
                'depart' => $solde,
                'encours' => $resume
            ];
        });




        return view('solde.index', compact('soldeJour', 'transactionsExist', 'operateurs', 'soldes'));
    }


    private function calculResumeSolde($date)
    {
        $totalCash = 0;
        $totalUV = array_fill_keys(['moov', 'orange', 'mtn', 'wave', 'djamo', 'push', 'trmo'], 0);

        $resultats = DB::table('transaction')
            ->select('operateur', 'type_transaction', DB::raw('SUM(montant) as total'))
            ->whereDate('date_transaction', $date)
            ->where('paie', 0)
            ->groupBy('operateur', 'type_transaction')
            ->get();

        foreach ($resultats as $res) {
            $op = $res->operateur;
            $type = $res->type_transaction;
            $montant = (int) $res->total;

            switch ($type) {
                case 'AU':
                case 'DP':
                    if (isset($totalUV[$op])) $totalUV[$op] -= $montant;
                    if ($type === 'DP') $totalCash += $montant;
                    break;
                case 'RU':
                case 'RE':
                    if (isset($totalUV[$op])) $totalUV[$op] += $montant;
                    if ($type === 'RE') $totalCash -= $montant;
                    break;
                case 'EC':
                    $totalCash += $montant;
                    break;
                case 'DC':
                    $totalCash -= $montant;
                    break;
            }
        }

        // Calcul dette / avoir
        $balances = DB::table('transaction')
            ->join('client', 'client.code', '=', 'transaction.code_client')
            ->whereIn('transaction.type_transaction', ['RU', 'EC', 'AU', 'DC'])
            ->where('transaction.paie', 0)
            ->whereDate('transaction.date_transaction', '<=', $date)
            ->select('client.identifiant as client', DB::raw("
            SUM(
                CASE
                    WHEN transaction.type_transaction IN ('RU', 'EC') THEN transaction.montant
                    WHEN transaction.type_transaction IN ('AU', 'DC') THEN -transaction.montant
                    ELSE 0
                END
            ) AS solde
        "))
            ->groupBy('transaction.code_client', 'client.identifiant')
            ->get();

        $totalDette = 0;
        $totalAvoir = 0;

        foreach ($balances as $b) {
            $s = (float) $b->solde;
            if ($s < 0) $totalDette += -$s;
            else $totalAvoir += $s;
        }

        return [
            'cash_encours' => $totalCash,
            'uv_encours' => $totalUV,
            'dette_encours' => $totalDette,
            'avoir_encours' => $totalAvoir,
        ];
    }


    public function storeOrUpdate(Request $request){


        $dateSolde = $request->input('date_solde');
        $idSolde = $request->input('soldeId');








        if (!is_null($idSolde)) {
            $transactionExists = DB::table('transaction')
                ->where('statut', 0)
                ->where('date_transaction', $dateSolde)->exists();
            if ($transactionExists) {
                return redirect()
                    ->back()
                    ->with('error', 'Modification interdite, une transaction existe déjà à cette date.');
            }
        }


        $uvData = [];
        foreach ($request->all() as $key => $value) {
            if (strpos($key, 'uv_') === 0) {
                $uvData[$key] = $value;
            }
        }

        if (!is_null($idSolde)) {
            // Mise à jour du solde
            DB::table('solde')
                ->where('id_solde', $idSolde)
                ->update([
                    'cash' => $request->input('cash'),
                    'dette' => $request->input('dette'),
                    'avoir' => $request->input('avoir'),
                    'date_solde' => $dateSolde,
                    'uv_orange' => $uvData['uv_orange'] ?? 0,
                    'uv_wave' => $uvData['uv_wave'] ?? 0,
                    'uv_djamo' => $uvData['uv_djamo'] ?? 0,
                    'uv_push' => $uvData['uv_push'] ?? 0,
                    'uv_trmo' => $uvData['uv_trmo'] ?? 0,
                ]);
            return redirect()
                ->route('soldes.index')
                ->with('message', 'Solde modifié avec succès.');
        } else {
            // Création d'un nouveau solde
            DB::table('solde')->insert([
                'cash' => $request->input('cash'),
                'dette' => $request->input('dette'),
                'avoir' => $request->input('avoir'),
                'date_solde' => $dateSolde,
                'uv_orange' => $uvData['uv_orange'] ?? 0,
                'uv_wave' => $uvData['uv_wave'] ?? 0,
                'uv_djamo' => $uvData['uv_djamo'] ?? 0,
                'uv_push' => $uvData['uv_push'] ?? 0,
                'uv_trmo' => $uvData['uv_trmo'] ?? 0,
            ]);

            return redirect()
                ->route('soldes.index')
                ->with('message', 'Solde enregistré avec succès.');
        }

    }

    public function delete(Request $request){


        $idSolde = $request->input('soldeId');


        DB::table('solde')
            ->whereDate('date_solde', $idSolde)
            ->delete();

        return redirect()
            ->route('soldes.index')
            ->with('message', 'Suppresion effectuée avec succès.');
    }
    public function cloturer(Request $request){


        $date = $request->input('soldeId');


        // Convertir la date en instance Carbon
        $dateCarbon = Carbon::parse($date);

// Ajouter un jour à la date
        $dateAvecUnJourDePlus = $dateCarbon->addDay();

// Afficher la nouvelle date
        $dateN1 = $dateAvecUnJourDePlus->toDateString();


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
            ->where('paie','=',0)
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
            ->select('client.identifiant as client',
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
            ->whereIn('transaction.type_transaction',['RU','EC','AU','DC'])
            ->whereDate('transaction.date_transaction','<=',$date)
            ->select('client.identifiant as client',
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



        $soldes['cash_depart'] = ($soldesData->cash);
        $soldes['cash_encours'] = ($totalCash + $soldesData->cash);

        $soldes['dette_depart'] = ($soldesData->dette);
        $soldes['dette_encours'] = ($totalDette);

        $soldes['avoir_depart'] = ($soldesData->avoir);
        $soldes['avoir_encours'] = ($totalAvoir);

        $totalDepart += $soldesData->cash;
        $totalEncours += ($totalCash + $soldesData->cash);

// Ajout des valeurs de dette
        $totalDepart += $soldesData->dette;
        $totalEncours += ($totalDette);

// Ajout des valeurs d'avoir
        $totalDepart -= $soldesData->avoir;
        $totalEncours -= ($totalAvoir);

        foreach ($operateurs as $operateur){
            $soldes['uv_'.$operateur['id'].'_depart'] = ($soldesData->{'uv_'.$operateur['id']});
            $soldes['uv_'.$operateur['id'].'_encours'] = ($totalUV[$operateur['id']] + $soldesData->{'uv_'.$operateur['id']});
            $totalDepart += $soldesData->{'uv_'.$operateur['id']};
            $totalEncours += ($totalUV[$operateur['id']] + $soldesData->{'uv_'.$operateur['id']});
        }

        $soldes['total_depart'] = ($totalDepart);
        $soldes['total_encours'] = ($totalEncours);





        $data = [
            'cash' => $soldes['cash_encours'],
            'uv_orange' => $soldes['uv_orange_encours'],
            'uv_wave' => $soldes['uv_wave_encours'],
            'uv_djamo' => $soldes['uv_djamo_encours'],
            'uv_push' => $soldes['uv_push_encours'],
            'uv_trmo' => $soldes['uv_trmo_encours'],
            'dette' =>$soldes['dette_encours'],
            'avoir' => $soldes['avoir_encours'],
            'date_solde' => $dateN1,
            'cloturer' => 0, // Le solde n'est pas clôturé au moment de l'enregistrement
        ];



        DB::table('solde')->insert($data);

        DB::table('solde')
            ->whereDate('date_solde','=',$date)
            ->update(array(
                'cloturer' => 1,
            ));

        return redirect()
            ->route('soldes.index')
            ->with('message', 'Journée cloturé avec succès.');
    }
    public function cloturerXX(Request $request){




        $date = $request->input('soldeId');



        // Convertir la date en instance Carbon
        $dateCarbon = Carbon::parse($date);

// Ajouter un jour à la date
        $dateAvecUnJourDePlus = $dateCarbon->addDay();

// Afficher la nouvelle date
        $dateN1 = $dateAvecUnJourDePlus->toDateString();



        $types = [
            ['libelle' => 'CASH', 'colonne' => 'cash'],

            ['libelle' => 'UV ORANGE', 'colonne' => 'orange'],

            ['libelle' => 'UV WAVE', 'colonne' => 'wave'],
            ['libelle' => 'UV DJAMO', 'colonne' => 'djamo'],
            ['libelle' => 'UV PUSH', 'colonne' => 'push'],
            ['libelle' => 'UV TRESOR MONNEY', 'colonne' => 'trmo'],
            ['libelle' => 'DETTE', 'colonne' => 'dette'],
            ['libelle' => 'AVOIR', 'colonne' => 'avoir'],
        ];

        $soldes = [];

        foreach ($types as $type) {

            if ($type['libelle'] == 'CASH'){

                $soldeCash = DB::table('solde')
                    ->whereDate('date_solde', $date)
                    ->sum('cash');

                $totalUvEncaissement = DB::table('transaction')
                    ->where('type_transaction','=','EC')
                    ->whereDate('date_transaction','=',$date)
                    ->sum('montant');

                $totalDecaissement = DB::table('transaction')
                    ->where('type_transaction','=','DC')
                    ->whereDate('date_transaction','=',$date)
                    ->sum('montant');



                $totalDepot = DB::table('transaction')
                    ->where('type_transaction','=','DP')
                    ->whereDate('date_transaction','=',$date)
                    ->sum('montant');

                $totalRetrait = DB::table('transaction')
                    ->where('type_transaction','=','RE')
                    ->whereDate('date_transaction','=',$date)
                    ->sum('montant');






                $cashEnccours = $soldeCash - $totalDecaissement + $totalUvEncaissement + $totalDepot - $totalRetrait;



                $soldesDD[strtolower($type['colonne']) . '_depart'] = formatNumber($soldeCash);

                $soldesDD[strtolower($type['colonne']) . '_encours'] = formatNumber($cashEnccours);
            }





            elseif ($type['libelle'] == 'UV ORANGE') {

                $soldeUv = DB::table('solde')
                    ->whereDate('date_solde', $date)
                    ->sum('uv_orange');

                $totalUvachatUV = DB::table('transaction')
                    ->where('type_transaction','=','AU')
                    ->where('operateur','=','orange')
                    ->whereDate('date_transaction','=',$date)
                    ->sum('montant');

                $totalURetourUV = DB::table('transaction')
                    ->where('type_transaction','=','RU')
                    ->where('operateur','=','orange')
                    ->whereDate('date_transaction','=',$date)
                    ->sum('montant');


                $totalDepot = DB::table('transaction')
                    ->where('type_transaction','=','DP')
                    ->where('operateur','=','orange')
                    ->whereDate('date_transaction','=',$date)
                    ->sum('montant');

                $totalRetrait = DB::table('transaction')
                    ->where('type_transaction','=','RE')
                    ->where('operateur','=','orange')
                    ->whereDate('date_transaction','=',$date)
                    ->sum('montant');


                $soldesUvENcours = $soldeUv - $totalUvachatUV + $totalURetourUV + $totalDepot - $totalRetrait;

                $soldesDD[strtolower($type['colonne']) . '_depart'] = formatNumber($soldeUv);
                $soldesDD[strtolower($type['colonne']) . '_encours'] = formatNumber($soldesUvENcours);
            }

            elseif ($type['libelle'] == 'UV WAVE') {
                $soldeUv = DB::table('solde')
                    ->whereDate('date_solde', $date)
                    ->sum('uv_wave');

                $totalUvachatUV = DB::table('transaction')
                    ->where('type_transaction','=','AU')
                    ->where('operateur','=','wave')
                    ->whereDate('date_transaction','=',$date)
                    ->sum('montant');

                $totalURetourUV = DB::table('transaction')
                    ->where('type_transaction','=','RU')
                    ->where('operateur','=','wave')
                    ->whereDate('date_transaction','=',$date)
                    ->sum('montant');

                $totalDepot = DB::table('transaction')
                    ->where('type_transaction','=','DP')
                    ->where('operateur','=','wave')
                    ->whereDate('date_transaction','=',$date)
                    ->sum('montant');

                $totalRetrait = DB::table('transaction')
                    ->where('type_transaction','=','RE')
                    ->where('operateur','=','wave')
                    ->whereDate('date_transaction','=',$date)
                    ->sum('montant');

                $soldesUvENcours = $soldeUv - $totalUvachatUV + $totalURetourUV + $totalDepot - $totalRetrait;

                $soldesDD[strtolower($type['colonne']) . '_depart'] = formatNumber($soldeUv);
                $soldesDD[strtolower($type['colonne']) . '_encours'] = formatNumber($soldesUvENcours);
            }
            elseif ($type['libelle'] == 'UV DJAMO') {
                $soldeUv = DB::table('solde')
                    ->whereDate('date_solde', $date)
                    ->sum('uv_djamo');

                $totalUvachatUV = DB::table('transaction')
                    ->where('type_transaction','=','AU')
                    ->where('operateur','=','djamo')
                    ->whereDate('date_transaction','=',$date)
                    ->sum('montant');

                $totalURetourUV = DB::table('transaction')
                    ->where('type_transaction','=','RU')
                    ->where('operateur','=','djamo')
                    ->whereDate('date_transaction','=',$date)
                    ->sum('montant');

                $totalDepot = DB::table('transaction')
                    ->where('type_transaction','=','DP')
                    ->where('operateur','=','djamo')
                    ->whereDate('date_transaction','=',$date)
                    ->sum('montant');

                $totalRetrait = DB::table('transaction')
                    ->where('type_transaction','=','RE')
                    ->where('operateur','=','djamo')
                    ->whereDate('date_transaction','=',$date)
                    ->sum('montant');

                $soldesUvENcours = $soldeUv - $totalUvachatUV + $totalURetourUV + $totalDepot - $totalRetrait;

                $soldesDD[strtolower($type['colonne']) . '_depart'] = formatNumber($soldeUv);
                $soldesDD[strtolower($type['colonne']) . '_encours'] = formatNumber($soldesUvENcours);
            }
            elseif ($type['libelle'] == 'UV PUSH') {
                $soldeUv = DB::table('solde')
                    ->whereDate('date_solde', $date)
                    ->sum('uv_push');

                $totalUvachatUV = DB::table('transaction')
                    ->where('type_transaction','=','AU')
                    ->where('operateur','=','push')
                    ->whereDate('date_transaction','=',$date)
                    ->sum('montant');

                $totalURetourUV = DB::table('transaction')
                    ->where('type_transaction','=','RU')
                    ->where('operateur','=','push')
                    ->whereDate('date_transaction','=',$date)
                    ->sum('montant');

                $totalDepot = DB::table('transaction')
                    ->where('type_transaction','=','DP')
                    ->where('operateur','=','push')
                    ->whereDate('date_transaction','=',$date)
                    ->sum('montant');

                $totalRetrait = DB::table('transaction')
                    ->where('type_transaction','=','RE')
                    ->where('operateur','=','push')
                    ->whereDate('date_transaction','=',$date)
                    ->sum('montant');

                $soldesUvENcours = $soldeUv - $totalUvachatUV + $totalURetourUV + $totalDepot - $totalRetrait;

                $soldesDD[strtolower($type['colonne']) . '_depart'] = formatNumber($soldeUv);
                $soldesDD[strtolower($type['colonne']) . '_encours'] = formatNumber($soldesUvENcours);
            }
            elseif ($type['libelle'] == 'UV TRESOR MONNEY') {
                $soldeUv = DB::table('solde')
                    ->whereDate('date_solde', $date)
                    ->sum('uv_trmo');

                $totalUvachatUV = DB::table('transaction')
                    ->where('type_transaction','=','AU')
                    ->where('operateur','=','trmo')
                    ->whereDate('date_transaction','=',$date)
                    ->sum('montant');

                $totalURetourUV = DB::table('transaction')
                    ->where('type_transaction','=','RU')
                    ->where('operateur','=','trmo')
                    ->whereDate('date_transaction','=',$date)
                    ->sum('montant');

                $totalDepot = DB::table('transaction')
                    ->where('type_transaction','=','DP')
                    ->where('operateur','=','trmo')
                    ->whereDate('date_transaction','=',$date)
                    ->sum('montant');

                $totalRetrait = DB::table('transaction')
                    ->where('type_transaction','=','RE')
                    ->where('operateur','=','trmo')
                    ->whereDate('date_transaction','=',$date)
                    ->sum('montant');

                $soldesUvENcours = $soldeUv - $totalUvachatUV + $totalURetourUV + $totalDepot - $totalRetrait;

                $soldesDD[strtolower($type['colonne']) . '_depart'] = formatNumber($soldeUv);
                $soldesDD[strtolower($type['colonne']) . '_encours'] = formatNumber($soldesUvENcours);
            }


            elseif ($type['libelle'] == 'DETTE'){

                $soldeUv = DB::table('solde')
                    ->whereDate('date_solde', $date)
                    ->sum('dette');

                $totalURetourUV = DB::table('transaction')
                    ->where('type_transaction','=','AU')
                    ->whereDate('date_transaction','=',$date)
                    ->sum('montant');


                $totalURetourAchatUV = DB::table('transaction')
                    ->where('type_transaction','=','RU')
                    ->whereDate('date_transaction','=',$date)
                    ->sum('montant');




                $totalDecaissement = DB::table('transaction')
                    ->where('type_transaction','=','EC')
                    ->whereDate('date_transaction','=',$date)
                    ->sum('montant');

                $totalDecaissementEnc = DB::table('transaction')
                    ->where('type_transaction','=','DC')
                    ->whereDate('date_transaction','=',$date)
                    ->sum('montant');





                $totalUAvoirUV = DB::table('transaction')
                    ->where('type_transaction','=','RU')
                    ->whereDate('date_transaction','=',$date)
                    ->sum('montant');



                $totalAvoirDecaissement = DB::table('transaction')
                    ->where('type_transaction','=','DC')
                    ->whereDate('date_transaction','=',$date)
                    ->sum('montant');


                $tchekDetteAvoir = $totalUAvoirUV - $totalAvoirDecaissement;



                $tchekDetteAvoir = max(0, $tchekDetteAvoir);





                $tchekDette = ($totalURetourUV - $totalURetourAchatUV) - ($totalDecaissement - $totalDecaissementEnc);




                $soldesDettesEncours = $soldeUv + $tchekDette;


                $soldesDD[strtolower($type['colonne']) . '_depart'] = formatNumber($soldeUv);

                $soldesDD[strtolower($type['colonne']) . '_encours'] = formatNumber($soldesDettesEncours);
            }


            elseif ($type['libelle'] == 'AVOIR'){

                $soldeUv = DB::table('solde')
                    ->whereDate('date_solde', $date)
                    ->sum('avoir');

                $totalURetourUV = DB::table('transaction')
                    ->where('type_transaction','=','RU')
                    ->whereDate('date_transaction','=',$date)
                    ->sum('montant');



                $totalDecaissement = DB::table('transaction')
                    ->where('type_transaction','=','DC')
                    ->whereDate('date_transaction','=',$date)
                    ->sum('montant');


                $tchekDette = $totalURetourUV - $totalDecaissement;

                $tchekDette = max(0, $tchekDette);


                $soldesDettesEncours = $soldeUv + $tchekDette;


                $soldesDD[strtolower($type['colonne']) . '_depart'] = formatNumber($soldeUv);

                $soldesDD[strtolower($type['colonne']) . '_encours'] = formatNumber($soldesDettesEncours);
            }






        }



        foreach ($soldesDD as $key => $value) {
            $soldesDD[$key] = (int) str_replace(' ', '', $value);
        }


        DB::table('solde')->insert([
            'cash' => $soldesDD['cash_encours'],
            'uv_orange' => $soldesDD['orange_encours'],
            'uv_wave' => $soldesDD['wave_encours'],
            'uv_djamo' => $soldesDD['djamo_encours'],
            'uv_push' => $soldesDD['push_encours'],
            'uv_trmo' => $soldesDD['trmo_encours'],
            'dette' => $soldesDD['dette_encours'],
            'avoir' => $soldesDD['avoir_encours'],
            'date_solde' => $dateN1,
            'cloturer' => 0, // Le solde n'est pas clôturé au moment de l'enregistrement
        ]);

        DB::table('solde')
            ->whereDate('date_solde','=',$date)
            ->update(array(
                'cloturer' => 1,
            ));

        return redirect()
            ->route('soldes.index')
            ->with('message', 'Journée cloturé avec succès.');
    }


}
