<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProduitsController extends Controller
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
        return view('produits.index', compact('entrepots'));
    }



    function genererCodeProduit(string $famille): string
    {
        $prefixe = strtoupper(substr($famille, 0, 3));

        $dernier = DB::table('produits')
            ->where('code_produit', 'like', $prefixe . '%')
            ->orderByDesc('id_produits') // ou orderBy('code_produit', 'desc')
            ->first();

        if ($dernier && isset($dernier->code_produit)) {
            $lastNumber = intval(substr($dernier->code_produit, 3)) + 1;
        } else {
            $lastNumber = 1;
        }

        return $prefixe . str_pad($lastNumber, 6, '0', STR_PAD_LEFT);
    }

    public function edit($id)
    {
        // Récupérer les informations d'un client pour modification
        $editClient = DB::table('avoir_produit')->where('id_avoir_produit', $id)
            ->join('produits', 'avoir_produit.code_produit', '=', 'produits.code_produit')
            ->first();

        if (!$editClient) {
            return back()->with('error', 'Produits introuvable.');
        }

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

        return view('produits.index', compact('entrepots','editClient'));
    }

    public function store(Request $request)
    {
        $famille = $request->famille;
        $description = $request->description;
        $prix = $request->prix;
        $prix_dmg = $request->prix_dmg;
        $prix_commercial = $request->prix_commercial;
        $idEntrepot = $request->entrepotID;

        $code = $this->genererCodeProduit($famille);

        $insert2 = DB::table('produits')->insert([
            'code_produit' => $code,
            'famille' => $famille,
            'description' =>$description,
        ]);

        $insert3 = DB::table('avoir_produit')->insert([
            'code_produit' => $code,
            'prix_revient' => $prix,
            'prix_dmg' => $prix_dmg,
            'prix_commercial' => $prix_commercial,
            'entrepot_id' =>$idEntrepot,
        ]);

        if($insert2){
            storeActivity('Creation','Produits',"Produit créé avec succès".$request->description);
            return back()->with('message', 'Produit créé avec succès');
        }
        return back()->with('error', "Erreur lors de l'insertion");

    }


    public function update(Request $request, $id)
    {

        $codeProduit = DB::table('avoir_produit')
            ->where('id_avoir_produit',$id)
            ->value('code_produit');


        $famille = $request->famille;
        $description = $request->description;
        $prix = $request->prix;
        $prix_dmg = $request->prix_dmg;
        $prix_commercial = $request->prix_commercial;


        $insert2 = DB::table('produits')
            ->where('code_produit', $codeProduit)
            ->update([
                'famille' => $famille,
                'description' =>$description,
            ]);

        $insert3 = DB::table('avoir_produit')
            ->where('id_avoir_produit', $id)
            ->update([
                'prix_revient' => $prix,
                'prix_dmg' => $prix_dmg,
                'prix_commercial' => $prix_commercial
            ]);




        if($insert2){
            storeActivity('Creation','Produits',"Produit modifié avec succès".$request->description);
            return back()->with('message', 'Produit modifié avec succès');
        }
        return redirect()->route('produits.index')->with('message', 'Produits mis à jour avec succès.');
    }
    public function ajaxUpdate(Request $request)
    {
        $id_produit = $request->id_avoir_produit;
        $field = $request->field;
        $value = (int) $request->value;

        // Vérification du champ autorisé
        $allowedFields = ['prix_revient', 'prix_dmg', 'prix_commercial'];
        if (!in_array($field, $allowedFields)) {
            return response()->json(['error' => 'Champ non autorisé'], 400);
        }

        // Récupération des infos du produit
        $produitInfo = DB::table('avoir_produit')
            ->join('produits', 'avoir_produit.code_produit', '=', 'produits.code_produit')
            ->where('id_avoir_produit', $id_produit)
            ->select('avoir_produit.*')
            ->first();

        if (!$produitInfo) {
            return response()->json(['error' => 'Produit introuvable'], 404);
        }

        // Préparer les données à mettre à jour
        $data = [
            'prix_revient' => $produitInfo->prix_revient,
            'prix_dmg' => $produitInfo->prix_dmg,
            'prix_commercial' => $produitInfo->prix_commercial,
        ];

        $data[$field] = $value; // Modifier uniquement le champ concerné

        // Mise à jour en base
        $updated = DB::table('avoir_produit')
            ->where('id_avoir_produit', $id_produit)
            ->update($data);

        if ($updated) {
            return response()->json(['success' => true, 'message' => 'Mise à jour effectuée']);
        } else {
            return response()->json(['success' => false, 'message' => 'Aucune modification effectuée']);
        }
    }


    public function destroy($id)
    {
        $user = DB::table('avoir_produit')->where('id_avoir_produit', $id);

        if (!$user) {
            return redirect()->route('produits.index')->with('error', 'Produit introuvable.');
        }


        DB::table('avoir_produit')->where('id_avoir_produit', $id)->update(array(
            'statut'=>0
        ));

        return back()->with('message', 'produit supprimé avec succès.');
    }

    public function consulter(Request $request){
        $entrepots = explode(';',$request->entrepotId);
        $entrepotID = $entrepots[0];
        $entrepotName  = $entrepots[1];
        $produits = DB::table('produits')
            ->join('avoir_produit', 'avoir_produit.code_produit', '=', 'produits.code_produit')
            ->where('avoir_produit.entrepot_id','=',$entrepotID)
            ->where('avoir_produit.statut','=',1)
            ->orderBy('produits.description','asc')
            ->get();

        return view('produits.ajax',compact('produits','entrepotName'));

    }

}
