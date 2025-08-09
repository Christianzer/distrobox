<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EntrepotController extends Controller
{
    public function index()
    {
        // Récupérer la liste des clients depuis la table 'client'
        $entrepots = DB::table('entrepot')
            ->orderBy('nom','ASC')
            ->get();
        return view('entrepots.index', compact('entrepots'));
    }

    public function edit($id)
    {
        // Récupérer les informations d'un client pour modification
        $editClient = DB::table('entrepot')->find($id);

        if (!$editClient) {
            return back()->with('error', 'Entrepot introuvable.');
        }

        $entrepots = DB::table('entrepot')->get();
        return view('entrepots.index', compact('editClient','entrepots'));
    }

    public function store(Request $request)
    {

        $insert2 = DB::table('entrepot')->insert([
            'nom' => $request->nom,
            'lieu' => $request->lieu,
            'contact' => $request->contact,
        ]);

        if($insert2){
            storeActivity('Creation','Entrepot',"Creation de l'entrepot ".$request->nom);
            return back()->with('message', 'Enregistrement effectué avec succès');
        }
        return back()->with('error', "Erreur lors de l'insertion");

    }


    public function update(Request $request, $id)
    {

        $data =[
            'nom' => $request->nom,
            'lieu' => $request->lieu,
            'contact' => $request->contact,
        ];


        DB::table('entrepot')->where('id_entrepot', $id)->update($data);
        storeActivity('Modification','Entrepot',"Modification entrepot ".$request->nom);
        return redirect()->route('entrepots.index')->with('message', 'Entrepot mis à jour avec succès.');
    }


    public function destroy($id)
    {
        $user = DB::table('entrepot')->find($id);

        if (!$user) {
            return redirect()->route('entrepots.index')->with('error', 'Entrepot introuvable.');
        }

        DB::table('entrepot')->where('id_entrepot', $id)->delete();
        return back()->with('message', 'Entrepot supprimé avec succès.');
    }
}
