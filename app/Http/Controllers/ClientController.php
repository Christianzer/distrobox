<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    public function index()
    {
        // Récupérer la liste des clients depuis la table 'client'
        $clients = DB::table('client')
            ->orderBy('raison_sociale','ASC')
            ->get();
        return view('clients.index', compact('clients'));
    }

    public function edit($id)
    {
        // Récupérer les informations d'un client pour modification
        $editClient = DB::table('client')->find($id);

        if (!$editClient) {
            return back()->with('error', 'Client introuvable.');
        }

        $clients = DB::table('client')->get();
        return view('clients.index', compact('editClient','clients'));
    }

    public function store(Request $request)
    {
        $code = generateUniqueCode();
        $insert2 = DB::table('client')->insert([
            'identifiant' => $request->identifiant,
            'code' => $code,
            'raison_sociale' => $request->raison_sociale,
            'telephone1' => $request->telephone1,
            'telephone2' => $request->telephone2,
            'email' => $request->email,
            'descriptif' => $request->descriptif,
            'adresse' => $request->adresse,
        ]);

        if($insert2){
            storeActivity('Creation','Client',"Creation du client ".$request->identifiant);
            return back()->with('message', 'Enregistrement effectué avec succès du client');
        }
        return back()->with('error', "Erreur lors de l'insertion");

    }


    public function update(Request $request, $id)
    {

        $data =[
            'identifiant' => $request->identifiant,
            'raison_sociale' => $request->raison_sociale,
            'telephone1' => $request->telephone1,
            'telephone2' => $request->telephone2,
            'email' => $request->email,
            'descriptif' => $request->descriptif,
            'adresse' => $request->adresse,
        ];


        DB::table('client')->where('id', $id)->update($data);
        storeActivity('Modification','Client',"Modification du client ".$request->identifiant);
        return redirect()->route('client.index')->with('message', 'Client mis à jour avec succès.');
    }




    public function destroy($id)
    {
        $user = DB::table('client')->find($id);

        if (!$user) {
            return redirect()->route('client.index')->with('error', 'Client introuvable.');
        }

        DB::table('client')->where('id', $id)->delete();
        $transactions = DB::table('transaction')
            ->where('code_client', $id)
            ->pluck('code_transaction');
        DB::table('transaction')->whereIn('code_client', $transactions)->delete();
        DB::table('avoir_transaction')->whereIn('code_transaction', $transactions)->delete();

        return back()->with('message', 'Client supprimé avec succès.');
    }


    public function bd(){
        $tables = array(
            array("id"=>'client',"libelle"=>'Clients'),
            array("id"=>'solde',"libelle"=>'Solde'),
            array("id"=>'transaction',"libelle"=>'transaction'),
            array("id"=>'bordereau',"libelle"=>'Bordereau'),
        );
        return view('base',compact('tables'));
    }


    public function vider(Request $request){
        $code = $request->code;

        if ($code == 'bordereau'){
            DB::table('bordereau')->truncate();
            DB::table('avoir_transaction')->truncate();
        }else{
            DB::table($code)->truncate();
        }

        return back()->with('message', 'Table vidée avec succès.');
    }
}
