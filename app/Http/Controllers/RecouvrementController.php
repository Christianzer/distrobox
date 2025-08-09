<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class RecouvrementController extends Controller
{
    public function index()
    {

        if (auth()->user()->groupe == 'SA'){
            $entrepots = DB::table('entrepot')
                ->orderBy('nom','asc')
                ->get();
        }else{
           $ids = json_decode(auth()->user()->entrepot_id, true); // décode ["1", "2"]

            $entrepots = DB::table('entrepot')
                ->when(is_array($ids), function ($query) use ($ids) {
                    return $query->whereIn('id_entrepot', $ids);
                }, function ($query) {
                    return $query->whereNull('id_entrepot'); // ou une clause par défaut
                })
                ->get();
        }

        return view('recouvrement.index', compact('entrepots'));
    }



    public function consulter(Request $request){
        $entrepots = explode(';',$request->entrepotId);
        $entrepotID = $entrepots[0];
        $entrepotName  = $entrepots[1];
        $agents = DB::table('users')
            ->select(
                'users.*',
                'users.id',
                'users.personnel'
            )
            ->join('transactions', 'users.id', '=', 'transactions.com_id')
            ->join('avoir_transactions', 'avoir_transactions.code_transaction', '=', 'transactions.code_transactions')
            ->where('transactions.entrepot_id', '=',$entrepotID)
            ->groupBy('users.id')
            ->get();

        return view('recouvrement.ajax',compact('agents','entrepotName','entrepotID'));

    }

    public function paiement(Request $request){

        DB::table('paiement')->insert([
            'id_agent' => $request->input('id_agent'),
            'montant' => $request->input('montant_verse'),
            'date_paiement' => $request->input('date_versement'),
            'entrepot_id' => $request->input('entrepot_id'),
            'type_mouvement' => 'ver',
        ]);

        return redirect()->back()
            ->with('message', 'Versement enregistré avec succès.')
            ->with('entrepotID', $request->input('entrepot_id'));

    }

    public function imprimer($code,$entrepot){

        $transactions = ListesTransactions($code,$entrepot);
        $titre = 'STOCK COMMERCIAL '.agentId($code);

        $pdf = Pdf::loadView('impression.versement',compact('transactions','titre'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream($titre.'.pdf');


    }

    public function compte(){
        if (auth()->user()->groupe == 'SA'){
            $entrepots = DB::table('entrepot')
                ->orderBy('nom','asc')
                ->get();
        }else{
           $ids = json_decode(auth()->user()->entrepot_id, true); // décode ["1", "2"]

            $entrepots = DB::table('entrepot')
                ->when(is_array($ids), function ($query) use ($ids) {
                    return $query->whereIn('id_entrepot', $ids);
                }, function ($query) {
                    return $query->whereNull('id_entrepot'); // ou une clause par défaut
                })
                ->get();
        }

        return view('recouvrement.compte',compact('entrepots'));
    }

    public function consulterCompte(Request $request){
        $entrepots = explode(';',$request->entrepotId);
        $entrepotID = $entrepots[0];
        $entrepotName  = $entrepots[1];
        $agents = DB::table('users')
            ->whereNotIn('groupe',['SP','GE'])
            ->whereJsonContains('entrepot_id',$entrepotID)
            ->get();

        $comptes = [];

        foreach ($agents as $agent) {
            $agentID = $agent->id;
            switch ($agent->groupe) {
                case 'SC':
                    $debit = DB::table('paiement')
                        ->where('type_mouvement', '=', 'ver')
                        ->where('id_agent', '=', $agentID)
                        ->sum('montant');

                    $credit = DB::table('transactions')
                        ->where('com_id', '=', $agentID)
                        ->sum('a_payer');

                    $categorie = 'COMMERCIAL';
                    break;

                case 'FO':
                    $debit = DB::table('paiement')
                        ->where('id_agent', '=', $agentID)
                        ->where('type_mouvement', '=', 'sor')
                        ->sum('montant');
                    $credit = DB::table('catalogue')
                        ->join('produits', 'catalogue.code_produit', '=', 'produits.code_produit')
                        ->join('avoir_produit', 'avoir_produit.code_produit', '=', 'produits.code_produit')
                        ->where('avoir_produit.entrepot_id', $entrepotID)
                        ->where('catalogue.entrepot_id', $entrepotID)
                        ->where('avoir_produit.statut', 1)
                        ->selectRaw('SUM(catalogue.quantite * avoir_produit.prix_revient) as montant_total')
                        ->value('montant_total');
                    $categorie = 'fournisseur';
                    break;

                case 'DM':
                    $debit = DB::table('paiement')
                        ->where('id_agent', '=', $agentID)
                        ->whereIn('type_mouvement', ['sor','ver'])
                        ->sum('montant');
                    $credit1 = DB::table('paiement')
                        ->where('id_agent', '=', $agentID)
                        ->where('type_mouvement', '=', 'ent')
                        ->sum('montant');

                    $creditpRODUIT = DB::table('transactions')
                        ->where('com_id', '=', $agentID)
                        ->sum('a_payer');

                    $credit = $credit1 + $creditpRODUIT;
                    $categorie = 'client DMG';
                    break;
                case 'CL':
                    $debit = DB::table('paiement')
                        ->where('id_agent', '=', $agentID)
                        ->whereIn('type_mouvement', ['sor','ver'])
                        ->sum('montant');
                    $credit1 = DB::table('paiement')
                        ->where('id_agent', '=', $agentID)
                        ->where('type_mouvement', '=', 'ent')
                        ->sum('montant');

                    $creditpRODUIT = DB::table('transactions')
                        ->where('com_id', '=', $agentID)
                        ->sum('a_payer');

                    $credit = $credit1 + $creditpRODUIT;
                    $categorie = 'client';
                    break;

                default:
                    $debit = 0;
                    $credit = 0;
                    $categorie = '';
                    break;
            }

            $comptesDD = array(
                'comptes' => $agent->personnel,
                'debit'   => $debit,
                'credit'  => $credit,
                'categorie'  => $categorie,
                'solde'   => $credit - $debit,
            );

            // 🔽 Ajouter au tableau $comptes
            $comptes[] = $comptesDD;
        }

        usort($comptes, function ($a, $b) {
            return $b['solde'] <=> $a['solde'];
        });



        return view('recouvrement.compte_ajax',compact('agents','entrepotName','comptes'));

    }

}
