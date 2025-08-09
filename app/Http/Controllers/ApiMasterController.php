<?php

namespace App\Http\Controllers;

use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ApiMasterController extends Controller
{
    public function login(Request $request){


        $username = $request->input('login');
        $mdp = $request->input('password');
        $password = md5($mdp);
        $condition = array(
            array('username' ,'=', $username),
            array('password' ,'=', $password),
            array('groupe','=','AG')
        );

        $attempt = User::where($condition)->first();

        if ($attempt) {
            return response()->json($attempt, 201);
        }else{
            return response()->json(406);
        }


    }

    public function dashboard($id){



        $course = DB::table('transaction')
            ->orderBy('date_transaction', 'desc')
            ->where('id_agent','=',$id)
            ->where('statut', 1)
            ->where('course', 1)
            ->count();

        $achat = DB::table('transaction')
            ->orderBy('date_transaction', 'desc')
            ->where('type_transaction','=','AU')
            ->where('id_agent','=',$id)
            ->where('statut', 1)
            ->where('course', 1)
            ->count();


        $retour = DB::table('transaction')
            ->orderBy('date_transaction', 'desc')
            ->where('type_transaction','=','RU')
            ->where('id_agent','=',$id)
            ->where('statut', 1)
            ->where('course', 1)
            ->count();



        $cashin = DB::table('transaction')
            ->orderBy('date_transaction', 'desc')
            ->where('type_transaction','=','EC')
            ->where('id_agent','=',$id)
            ->where('statut', 2)
            ->where('course', 2)
            ->sum('montant');


        $cashout = DB::table('transaction')
            ->orderBy('date_transaction', 'desc')
            ->where('type_transaction','=','DC')
            ->where('id_agent','=',$id)
            ->where('statut', 2)
            ->where('course', 2)
            ->sum('montant');


        $solde = $cashin - $cashout;

        $donnees = array($course,$achat,$retour,$cashin,$cashout,$solde);

        return response()->json($donnees, 201);

    }

    public function transaction($id,$type){
        if ($type == 'EC'):
            $result = DB::table('transaction')
                ->join('client', 'transaction.code_client', '=', 'client.code')
                ->leftJoin(
                    DB::raw('(
            SELECT code_encaissement, SUM(montant) AS montant_encaisse
            FROM transaction
            WHERE type_transaction = "EC" AND course = 2 AND statut = 2
            GROUP BY code_encaissement
        ) AS encaisse'),
                    'transaction.code_transaction', '=', 'encaisse.code_encaissement'
                )
                ->select(
                    'transaction.*','client.*','transaction.montant as montant_dep','client.raison_sociale as identifiant',
                    DB::raw('transaction.montant - IFNULL(encaisse.montant_encaisse, 0) AS montant')
                )
                ->orderBy('transaction.date_transaction', 'desc')
                ->where('transaction.type_transaction', '=', 'AU')
                ->where('transaction.id_agent', '=', $id)
                ->where('transaction.statut', 1)
                ->where('transaction.course', 1)
                ->get();


        elseif ($type == 'DC'):
            $result = DB::table('transaction')
                ->join('client', 'transaction.code_client', '=', 'client.code')
                ->leftJoin(
                    DB::raw('(
            SELECT code_encaissement, SUM(montant) AS montant_encaisse
            FROM transaction
            WHERE type_transaction = "DC" AND course = 2 AND statut = 2
            GROUP BY code_encaissement
        ) AS encaisse'),
                    'transaction.code_transaction', '=', 'encaisse.code_encaissement'
                )
                ->select(
                    'transaction.*','client.*','transaction.montant as montant_dep','client.raison_sociale as identifiant',
                    DB::raw('transaction.montant - IFNULL(encaisse.montant_encaisse, 0) AS montant')
                )
                ->orderBy('transaction.date_transaction', 'desc')
                ->where('transaction.type_transaction', '=', 'RU')
                ->where('transaction.id_agent', '=', $id)
                ->where('transaction.statut', 1)
                ->where('transaction.course', 1)
                ->get();

        else:


            $bordereauxAll = DB::table('avoir_transaction')
                ->pluck('code_transaction');

            $result = DB::table('transaction')
                ->select('transaction.*','client.*','client.raison_sociale as identifiant')
                ->join('client','transaction.code_client','=','client.code')
                ->orderBy('transaction.date_transaction', 'desc')
                ->whereIn('transaction.type_transaction',['EC','DC'])
                ->whereNotIn('transaction.code_transaction',$bordereauxAll)
                ->where('transaction.id_agent','=',$id)
                ->where('transaction.statut', 2)
                ->where('transaction.course', 2)
                ->get();

        endif;

        return response()->json($result, 201);

    }



    public function validation(Request $request){

        $code = $request->code_transaction;
        $montant = (int)$request->montant;
        $montantDepart = $request->montant_depart;
        $affiliation = $request->affiliation;
        $client = $request->client;
        $agent = $request->agent;
        $type = $request->type;
        $operateur = $request->operateur;
        $codeTrans = genererCode($type);

        $calculSommeTrnasaction = DB::table('transaction')
            ->where('code_encaissement','=',$code)
            ->sum('montant');

        $totalMontant = $calculSommeTrnasaction + $montant;

        if ($totalMontant >= $montantDepart):
            $result = DB::table('transaction')
                ->where('code_transaction','=',$code)
                ->update(array(
                    "course"=>2,
                    "statut"=>2
                ));
        endif;


        $data = array(
            'type_transaction'=>$type,
            'code_client'=>$client,
            'code_transaction'=>$codeTrans,
            'montant'=>$montant,
            'id_agent'=>$agent,
            'operateur'=>$operateur,
            'affiliation'=>$affiliation,
            'code_encaissement'=>$code,
            'course'=>2,
            'statut'=>2,
            'paie'=>1,
            'date_transaction'=>now(),
        );

        $result = DB::table('transaction')
            ->insert($data);




        return response()->json($result, 201);
    }
    public function imprimer(Request $request){

        $code = $request->code_transaction;
        $montant = (int)$request->montant;
        $montantDepart = $request->montant_depart;
        $affiliation = $request->affiliation;
        $client = $request->client;
        $agent = $request->agent;
        $type = $request->type;
        $operateur = $request->operateur;
        $codeTrans = genererCode($type);

        $calculSommeTrnasaction = DB::table('transaction')
            ->where('code_encaissement','=',$code)
            ->sum('montant');

        $totalMontant = $calculSommeTrnasaction + $montant;

        if ($totalMontant >= $montantDepart):
            $result = DB::table('transaction')
                ->where('code_transaction','=',$code)
                ->update(array(
                    "course"=>2,
                    "statut"=>2
                ));
        endif;


        $data = array(
            'type_transaction'=>$type,
            'code_client'=>$client,
            'code_transaction'=>$codeTrans,
            'montant'=>$montant,
            'id_agent'=>$agent,
            'operateur'=>$operateur,
            'affiliation'=>$affiliation,
            'code_encaissement'=>$code,
            'course'=>2,
            'statut'=>2,
            'date_transaction'=>now(),
        );

        DB::table('transaction')
            ->insert($data);



        $transaction = DB::table('transaction')
            ->join('client', 'client.code', '=', 'transaction.code_client')
            ->leftJoin('users', 'users.id', '=', 'transaction.id_agent')
            ->where('transaction.code_transaction', $codeTrans)
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

        $fileName = 'recu_'.$code.'.pdf';

        $filePath = storage_path('app/public/' . $fileName);

        Storage::put('public/' . $fileName, $pdf->output());

        return response()->json(asset('storage/' . $fileName),201);


    }


    public function bordereaux(Request $request){

        $bordereaux = $request->dataBordereau;
        $agent = $request->agent;

        $code=genererBordereau();

        DB::table('bordereau')
            ->insert(array(
                'code_bordereau'=>$code,
                'date_bordereau'=>now(),
                'id_agent'=>$agent
            ));

        foreach ($bordereaux as $bordereau){
            DB::table('avoir_transaction')
                ->insert(array(
                    'code_bordereau'=>$code,
                    'code_transaction'=>$bordereau['code_transaction'],
                    'date_avoir_transaction'=>now(),
                ));
        }

        return response()->json($code, 201);
    }

    public function historiques($id)
    {
        $bordereauxAvecTransactions = DB::table('bordereau')
            ->where('bordereau.id_agent', '=', $id)
            ->leftJoin('avoir_transaction', 'avoir_transaction.code_bordereau', '=', 'bordereau.code_bordereau')
            ->leftJoin('transaction', 'transaction.code_transaction', '=', 'avoir_transaction.code_transaction')
            ->leftJoin('client', 'client.code', '=', 'transaction.code_client')
            ->select([
                'bordereau.code_bordereau',
                'bordereau.date_bordereau',
                'bordereau.statut as statut_bordereau',
                'avoir_transaction.code_transaction',
                'avoir_transaction.date_avoir_transaction',
                'avoir_transaction.statut as statutTransac',
                'transaction.*', // Récupère toutes les colonnes de "transaction"
                'client.*', // Récupère toutes les colonnes de "client"
                'client.raison_sociale as identifiant'
            ])
            ->orderBy('bordereau.date_bordereau', 'desc')
            ->orderBy('avoir_transaction.date_avoir_transaction', 'desc')
            ->get()
            ->groupBy('code_bordereau');

        // Reformater la réponse pour inclure les transactions sous chaque bordereau
        $result = $bordereauxAvecTransactions->map(function ($transactions, $code_bordereaux) {
            return [
                'code_bordereaux' => $code_bordereaux,
                'date_bordereau' => $transactions->first()->date_bordereau,
                'statut' => $transactions->first()->statut_bordereau,
                'transactions' => $transactions->map(function ($t) {
                    return (array) $t; // Convertit directement l'objet en tableau pour inclure toutes les infos
                })->toArray()
            ];
        })->values();

        return response()->json($result, 201);
    }


    public function sendPosition(Request $request){
        $longitude = $request->longitude;
        $latitude = $request->latitude;
        $userId = $request->userId;

        DB::table('localisation')
            ->insert(array(
                'longitude'=>$longitude,
                'latitude'=>$latitude,
                'user_id'=>$userId
            ));
    }
}
