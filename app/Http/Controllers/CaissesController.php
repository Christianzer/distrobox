<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CaissesController extends Controller
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
        return view('caisses.index', compact('entrepots'));
    }

    public function consulter(Request $request){
        $entrepots = explode(';',$request->entrepotId);
        $entrepotID = $entrepots[0];
        $entrepotName  = $entrepots[1];
        $comptes =  DB::table('users')
            ->whereNotIn('groupe',['SP','GE'])
            ->whereJsonContains('entrepot_id',$entrepotID)
            ->get();

        $transactions = DB::table('paiement')
            ->join('users','users.id','=','paiement.id_agent')
            ->whereJsonContains('users.entrepot_id',$entrepotID)
            ->get();

        return view('caisses.ajax',compact('entrepotName','entrepotID','comptes','transactions','entrepotID'));

    }
    public function enregistrer(Request $request){


        /*
        $montantAgent = DB::table('users')
            ->where('id','=',$request->input('id_proprietaires'))
            ->first();

        if (isset($montantAgent)){
            if ($montantAgent->groupe == 'SC'){

                $montantTotal = DB::table('paiement')
                    ->where('id_agent','=',$request->input('id_proprietaires'))
                    ->where('type_mouvement','=','sor')
                    ->sum('montant');

                $montantReel = $montantTotal + (int)$request->input('montant');

                $montantDepenses = (int)$montantAgent->depense;

                if ($montantReel > $montantDepenses){
                    return redirect()->route('caisses.index')->with('error', 'Le total montant dépasse les dépenses enregistrées pour cet agent.')
                        ->with('entrepotID', $request->input('entrepotID'));
                }
            }
        }

        */






        DB::table('paiement')->insert([
            'date_paiement' => $request->input('date_facture'),
            'type_mouvement' => $request->input('typeTransaction'),
            'id_agent' => $request->input('id_proprietaires'),
            'montant' => $request->input('montant'),
            'description' => $request->input('observation'),
            'entrepot_id' => $request->input('entrepotID')
        ]);

        return redirect()->route('caisses.index')->with('message', 'Transaction enregistrée avec succès.')
            ->with('entrepotID', $request->input('entrepotID'));

    }
    public function supprimer(Request $request){
        DB::table('paiement')
            ->where('id_paiement', $request->input('code_transaction'))
            ->delete();

        return redirect()->route('caisses.index')->with('success', 'Transaction annulée avec succès.')->with('entrepotID', $request->input('entrepotID'));
    }
}
