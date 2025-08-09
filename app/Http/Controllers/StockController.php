<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    //

    public function enregistrer(Request $request){
        $commercialID = $request->input('commercial_id');
        $entrepotID = $request->input('entrepotID');
        $produits = $request->input('produits', []);
        $codeTransactions = genererBordereau();


        $montantTotal = 0;

        foreach ($produits as $data) {

            $codeProduit = $data['code_produit'];
            $quantite = isset($data['quantite']) ? intval($data['quantite']) : 0;
            $prix = isset($data['prix']) ? intval($data['prix']) : 0;
            $prixTotal = $quantite * $prix;
            $montantTotal += $prixTotal;

            // Insertion dans la table stock (ou adapte selon ta table)
            DB::table('avoir_transactions')->insert([
                'code_produit'   => $codeProduit,
                'quantite'       => $quantite,
                'prix'  => $prix,
                'code_transaction'     => $codeTransactions,
            ]);
        }

        $insert = DB::table('transactions')->insert([
            'com_id'  => $commercialID,
            'sup_id'  => auth()->user()->id,
            'code_transactions'  => $codeTransactions,
            'a_payer'  => $montantTotal,
            'entrepot_id'  => $entrepotID,
        ]);

        if (!$insert){
            return redirect()->back()->with('error', 'Erreur lors de lattribution');
        }

        return redirect()->back()
            ->with('message', 'Stock attribué avec succès.')
            ->with('entrepotID', $entrepotID);


    }

    public function index(){

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

        return view('stock.index',compact('entrepots'));
    }
    public function attribution(){

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

        return view('stock.attribution',compact('entrepots'));
    }

    public function store(Request $request){
        $date = $request->input('restock_date');
        $produits = $request->input('produits');
        $entrepotID = $request->input('entrepotID');
        $code_bl = $request->input('code_bl');

        $codeLivraiosn = $code_bl.'-'.$date;



        $existName = DB::table('catalogue')
            ->where('code_bl',$codeLivraiosn)
            ->where('entrepot_id',$entrepotID)
            ->exists();


        if ($existName){
            return redirect()->back()->with('error', 'Ce code de livraison existe deja')
                ->with('entrepotID', $entrepotID);
        }


        $minDate = DB::table('catalogue')
            ->where('entrepot_id',$entrepotID)
            ->min('date_stockage');

        if ($date < $minDate) {
            return redirect()->back()->with('error', 'La date de restockage est ancienne.')
                ->with('entrepotID', $entrepotID);
        }


        foreach ($produits as $data) {
            if (!empty($data['quantite']) && $data['quantite'] > 0) {
                DB::table('catalogue')->insert([
                    'code_produit' => $data['code_produit'],
                    'quantite' => $data['quantite'],
                    'date_stockage' => $date,
                    'entrepot_id' => $entrepotID,
                    'code_bl' => $codeLivraiosn,
                    'date_limite' => $data['date_limite']
                ]);
            }
        }


        return redirect()->back()->with('message', 'Restockage enregistré.')
            ->with('entrepotID', $entrepotID);
    }



    public function update(Request $request){
        $date = $request->date;
        $quantite = $request->quantite;
        $catalogue = $request->id;
        try {
            // Vérifier si l'enregistrement existe
            $exists = DB::table('catalogue')->where('id_catalogue',$catalogue)->exists();
            if (!$exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock introuvable.'
                ], 404);
            }
            // Mise à jour de la quantité
            DB::table('catalogue')
                ->where('id_catalogue',$catalogue)
                ->update([
                    'quantite' => $quantite,
                ]);
            return response()->json([
                'success' => true,
                'message' => 'Quantité mise à jour avec succès.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur : ' . $e->getMessage(),
            ], 500);
        }

    }

    public function consulter(Request $request){
        $entrepots = explode(';',$request->entrepotId);
        $entrepotID = $entrepots[0];
        $entrepotName  = $entrepots[1];

        $lastDates = DB::table('catalogue')
            ->select('date_stockage', 'code_bl')
            ->where('entrepot_id', '=', $entrepotID)
            ->groupBy('date_stockage', 'code_bl') // nécessaire avec select multiple
            ->orderByDesc('date_stockage')
            ->limit(4)
            ->get()
            ->sortBy('date_stockage');
        // retourne une collection




        $dateStockages = $lastDates->pluck('date_stockage')->all();




        $cataloguesRaw  = DB::table('catalogue')
            ->leftJoin('produits', 'catalogue.code_produit', '=', 'produits.code_produit')
            ->leftJoin('avoir_produit', 'avoir_produit.code_produit', '=', 'produits.code_produit')
            ->whereIn('catalogue.date_stockage', $dateStockages)
            ->where('avoir_produit.entrepot_id', '=', $entrepotID)
            ->where('catalogue.entrepot_id', '=', $entrepotID)
            ->where('avoir_produit.statut', '=', 1)
            ->orderBy('produits.description','asc')
            ->orderBy('catalogue.date_stockage','asc')
            ->select(
                'catalogue.code_produit',
                'avoir_produit.prix_revient as prix',
                'avoir_produit.prix_dmg as prix_dmg',
                'avoir_produit.prix_commercial as prix_commercial',
                'catalogue.quantite',
                'catalogue.id_catalogue',
                'catalogue.date_stockage',
                'produits.description'
            )
            ->get();

        $catalogues = [];

        $produits  = DB::table('produits')
            ->join('avoir_produit', 'avoir_produit.code_produit', '=', 'produits.code_produit')
            ->where('avoir_produit.entrepot_id', '=', $entrepotID)
            ->where('avoir_produit.statut', '=', 1)
            ->orderBy('produits.description','asc')
            ->groupBy('produits.code_produit')
            ->get();


        foreach ($cataloguesRaw as $item) {
            $key = $item->code_produit;

            if (!isset($catalogues[$key])) {
                $catalogues[$key] = [
                    'description' => $item->description,
                    'prix' => $item->prix,
                    'prix_dmg' => $item->prix_dmg,
                    'prix_commercial' => $item->prix_commercial,
                    'dates' => [],
                    'total' => 0
                ];
            }

            $catalogues[$key]['dates'][$item->date_stockage] = [
                'quantite' => $item->quantite,
                'id_catalogue' => $item->id_catalogue,
            ];



            $catalogues[$key]['total'] += $item->quantite;
        }


        $latestDate = Carbon::parse($lastDates->pluck('date_stockage')->max())->format('Y-m-d');

        $derniereTransaction = DB::table('transactions')
            ->where('entrepot_id', $entrepotID)
            ->max('date_transaction');

        $derniereTransaction = Carbon::parse($derniereTransaction)->format('Y-m-d');

        return view('stock.ajax',compact('catalogues','entrepotName','lastDates','produits','entrepotID','latestDate','derniereTransaction'));

    }
    public function attribuer(Request $request){
        $entrepots = explode(';',$request->entrepotId);
        $entrepotID = $entrepots[0];
        $entrepotName  = $entrepots[1];

        $commercials = DB::table('users')
            ->whereIn('groupe',['SC','CL','DM'])
            ->whereJsonContains('entrepot_id', $entrepotID)
            ->get();



        $produitsEntrepots = DB::table('avoir_produit')
            ->distinct('code_produit')
            ->where('avoir_produit.entrepot_id', '=', $entrepotID)
            ->where('avoir_produit.statut', '=', 1)
            ->get();

        $produits = [];

        foreach ($produitsEntrepots as $produitsEnt){
            $code_produit = $produitsEnt->code_produit;
            $description = DB::table('produits')->where('code_produit', '=', $code_produit)->value('description');
            $prix = $produitsEnt->prix_revient;
            $prix_dmg = $produitsEnt->prix_dmg;
            $prix_commercial = $produitsEnt->prix_commercial;
            $date_limite = DB::table('catalogue')
                ->where('code_produit', '=', $code_produit)
                ->where('entrepot_id','=',$entrepotID)
                ->orderByDesc('date_stockage') // la plus récente d'abord
                ->value('date_limite');
            $quantite_depart = DB::table('catalogue')
                ->where('code_produit', '=', $code_produit)
                ->where('entrepot_id','=',$entrepotID)
                ->sum('quantite');
            $quantite_sortie = DB::table('avoir_transactions')
                ->join('transactions','transactions.code_transactions','=','avoir_transactions.code_transaction')
                ->where('avoir_transactions.code_produit', '=', $code_produit)
                ->where('transactions.entrepot_id','=',$entrepotID)
                ->sum('quantite');

            $total_quantite = $quantite_depart - $quantite_sortie;

            if ($quantite_depart > 0):
                $produits[] = [
                    'code_produit' => $code_produit,
                    'description' => $description,
                    'prix' => $prix,
                    'prix_dmg' => $prix_dmg,
                    'prix_commercial' => $prix_commercial,
                    'date_limite' => $date_limite,
                    'quantite_depart' => $quantite_depart,
                    'quantite_sortie' => $quantite_sortie,
                    'total_quantite' => $total_quantite,
                ];
            endif;


        }

        usort($produits, function ($a, $b) {
            return strcmp($a['description'], $b['description']);
        });

        /*
        $produits  = DB::table('catalogue')
            ->join('produits', 'catalogue.code_produit', '=', 'produits.code_produit')
            ->join('avoir_produit', 'avoir_produit.code_produit', '=', 'produits.code_produit')
            ->leftJoin(DB::raw('(
        SELECT code_produit, SUM(quantite) AS quantite_sortie
        FROM avoir_transactions
        GROUP BY code_produit
    ) as mvt'), 'produits.code_produit', '=', 'mvt.code_produit')
            ->select(
                'produits.code_produit',
                'produits.description',
                'avoir_produit.prix_revient as prix',
                'avoir_produit.prix_dmg as prix_dmg',
                'avoir_produit.prix_commercial as prix_commercial',
                'catalogue.date_limite as date_limite',
                DB::raw('
            SUM(catalogue.quantite) AS quantite_depart
        '),
                DB::raw('COALESCE(mvt.quantite_sortie, 0)
            AS quantite_sortie
        '),
                DB::raw('
            SUM(catalogue.quantite) - COALESCE(mvt.quantite_sortie, 0)
            AS total_quantite
        ')
            )
            ->where('catalogue.entrepot_id', '=', $entrepotID)
            ->where('avoir_produit.entrepot_id', '=', $entrepotID)
            ->where('avoir_produit.statut', '=', 1)
            ->groupBy('produits.code_produit')
            ->orderBy('produits.description', 'asc')
            ->orderBy('catalogue.date_stockage', 'asc')
            ->get();
        */


        return view('stock.ajax_attribuer',compact('entrepotName','produits','entrepotID','commercials'));

    }

    public function deleteBL(Request $request)
    {
        $request->validate([
            'code_bl' => 'required|string',
            'date_stockage' => 'required|date',
        ]);

        $codeBl = $request->input('code_bl');
        $dateStockage = $request->input('date_stockage');
        $entrepotID = $request->input('entrepotID');

        try {
            // Suppression des lignes dans la table 'catalogue'
            DB::table('catalogue')
                ->where('code_bl', $codeBl)
                ->whereDate('date_stockage', $dateStockage)
                ->delete();


            return redirect()->back()->with('message', "Le bon de livraison $codeBl du $dateStockage a été supprimé avec succès.")
                ->with('entrepotID', $entrepotID);
        } catch (\Exception $e) {

            return redirect()->back()->with('error', "Erreur lors de la suppression : " . $e->getMessage())
                ->with('entrepotID', $entrepotID);
        }
    }
}
