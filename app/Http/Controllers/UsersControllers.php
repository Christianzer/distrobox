<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Monolog\Handler\IFTTTHandler;

class UsersControllers extends Controller
{


    public function control(){
        if (Auth::check()){
            return redirect()->route('dashboard');
        }
        return redirect()->route('login');
    }


    public function connexion(){
        return view('login');
    }


    public function dashboard()
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
        return view('dashboard',compact('entrepots'));
    }

    public function consulter(Request $request){
        $entrepots = explode(';',$request->entrepotId);
        $entrepotID = $entrepots[0];
        $entrepotName  = $entrepots[1];



        $subquery = DB::table('catalogue')
            ->select('code_produit', DB::raw('MIN(date_stockage) as first_stock_date'))
            ->where('entrepot_id', $entrepotID)
            ->groupBy('code_produit');

        $resultats = DB::table('catalogue')
            ->joinSub($subquery, 'first_stock', function ($join) {
                $join->on('catalogue.code_produit', '=', 'first_stock.code_produit')
                    ->on('catalogue.date_stockage', '=', 'first_stock.first_stock_date');
            })
            ->join('produits', 'catalogue.code_produit', '=', 'produits.code_produit')
            ->join('avoir_produit', 'avoir_produit.code_produit', '=', 'produits.code_produit')
            ->where('avoir_produit.entrepot_id', $entrepotID)
            ->where('catalogue.entrepot_id', $entrepotID)
            ->where('avoir_produit.statut', 1)
            ->selectRaw('SUM(catalogue.quantite) as quantite_totale, SUM(catalogue.quantite * avoir_produit.prix_revient) as prix_total')
            ->first();

        $subqueryAfta = DB::table('catalogue')
            ->select('code_produit', DB::raw('MIN(date_stockage) as first_stock_date'))
            ->where('entrepot_id', $entrepotID)
            ->groupBy('code_produit');

        $resultatsAfta = DB::table('catalogue')
            ->joinSub($subqueryAfta, 'first_stock', function ($join) {
                $join->on('catalogue.code_produit', '=', 'first_stock.code_produit');
            })
            ->join('produits', 'catalogue.code_produit', '=', 'produits.code_produit')
            ->join('avoir_produit', 'avoir_produit.code_produit', '=', 'produits.code_produit')
            ->where('avoir_produit.entrepot_id', $entrepotID)
            ->where('catalogue.entrepot_id', $entrepotID)
            ->where('avoir_produit.statut', 1)
            ->whereColumn('catalogue.date_stockage', '>', 'first_stock.first_stock_date')
            ->selectRaw('SUM(catalogue.quantite) as quantite_totale, SUM(catalogue.quantite * avoir_produit.prix_revient) as prix_total')
            ->first();

        $totalSorties = DB::table('transactions')
            ->join('avoir_transactions','avoir_transactions.code_transaction','=','transactions.code_transactions')
            ->join('produits', 'produits.code_produit', '=', 'avoir_transactions.code_produit')
            ->join('avoir_produit', 'avoir_produit.code_produit', '=', 'produits.code_produit')
            ->where('avoir_produit.entrepot_id', $entrepotID)
            ->where('transactions.entrepot_id', $entrepotID)
            ->where('avoir_produit.statut', 1)
            ->selectRaw('SUM(avoir_transactions.quantite) as quantite_totale, SUM(avoir_transactions.quantite * avoir_produit.prix_revient) as prix_total')
            ->first();

        $quantiteTotaleInitial = $resultats->quantite_totale;
        $prixTotalInitial = $resultats->prix_total;

        $quantiteTotaleAfta = $resultatsAfta->quantite_totale;
        $prixTotalAfta = $resultatsAfta->prix_total;

        $totalSortiesPcs = $totalSorties->quantite_totale;
        $totalSortiesPrix = $totalSorties->prix_total;

        $stockFinalRhpcs = $quantiteTotaleInitial + $quantiteTotaleAfta - $totalSortiesPcs;
        $stockFinalRPrix = $prixTotalInitial + $prixTotalAfta - $totalSortiesPrix;

        $stockFinalReelPcs = 0;
        $stockFinalRellPrix = 0;

        $ecartsStockPices = $stockFinalRhpcs - $stockFinalReelPcs;
        $ecartsStockPrix = $stockFinalRPrix - $stockFinalRellPrix;

        $totalEnlevement = DB::table('transactions')
            ->join('users','users.id','=','transactions.com_id')
            ->where('transactions.entrepot_id',$entrepotID)
            ->sum('a_payer');

        $totalVersement = DB::table('paiement')
            ->join('users','users.id','=','paiement.id_agent')
            ->where('paiement.entrepot_id',$entrepotID)
            ->where('paiement.type_mouvement','ver')
            ->sum('montant');

        $totalCredit = $totalVersement - $totalEnlevement;

        $entreeCaisse = DB::table('paiement')
            ->join('users','users.id','=','paiement.id_agent')
            ->where('paiement.entrepot_id',$entrepotID)
            ->whereIn('paiement.type_mouvement',['ver','ent'])
            ->sum('montant');

        $sortieCaisse = DB::table('paiement')
            ->join('users','users.id','=','paiement.id_agent')
            ->where('paiement.entrepot_id',$entrepotID)
            ->whereIn('paiement.type_mouvement',['sor'])
            ->sum('montant');

        $soldes = $entreeCaisse - $sortieCaisse;


        $totalSortiesReel = DB::table('transactions')
            ->join('avoir_transactions','avoir_transactions.code_transaction','=','transactions.code_transactions')
            ->join('produits', 'produits.code_produit', '=', 'avoir_transactions.code_produit')
            ->join('avoir_produit', 'avoir_produit.code_produit', '=', 'produits.code_produit')
            ->where('avoir_produit.entrepot_id', $entrepotID)
            ->where('transactions.entrepot_id', $entrepotID)
            ->where('avoir_produit.statut', 1)
            ->selectRaw('SUM(avoir_transactions.quantite * avoir_transactions.prix) as prixTotalReel, SUM(avoir_transactions.quantite * avoir_produit.prix_revient) as prix_total')
            ->first();

        $margesBrutes = $totalSortiesReel->prix_total - $totalSortiesReel->prixTotalReel;

        $margesNettes = $margesBrutes - $sortieCaisse;


        $data = [

            'quantite_totale_initial' => $quantiteTotaleInitial,
            'prix_total_initial' => $prixTotalInitial,

            'quantite_totale_apres_entrees' => $quantiteTotaleAfta,
            'prix_total_apres_entrees' => $prixTotalAfta,

            'total_sorties_pcs' => $totalSortiesPcs,
            'total_sorties_prix' => $totalSortiesPrix,

            'stock_final_theorique_pcs' => $stockFinalRhpcs,
            'stock_final_theorique_prix' => $stockFinalRPrix,

            'stock_final_reel_pcs' => $stockFinalReelPcs,
            'stock_final_reel_prix' => $stockFinalRellPrix,

            'ecart_stock_pcs' => $ecartsStockPices,
            'ecart_stock_prix' => $ecartsStockPrix,

            'total_enlevement' => $totalEnlevement,
            'total_versement' => $totalVersement,
            'total_credit' => $totalCredit,

            'entree_caisse' => $entreeCaisse,
            'sortie_caisse' => $sortieCaisse,
            'solde_caisse' => $soldes,

            'marge_brute' => $margesBrutes,
            'marge_nette' => $margesNettes,
        ];





        return view('ajax',compact('entrepotName','entrepotID','data'));

    }


    public function login(Request $request){
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);
        $username = $request->input('username');
        $mdp = $request->input('password');
        $password = md5($mdp);
        $condition = array(
            array('username' ,'=', $username),
            array('password' ,'=', $password)
        );
        $attempt = User::where($condition)->first();
        if ($attempt) {
            Auth::login($attempt);
            storeActivity('Authentification','Connexion',"Connexion a GESOPE V2");

            if ( (int)ucfirst(Auth()->user()->activer) == 2){
                Session()->flash('success',"Votre compte a été desactiver. Veuillez contacter l'administrateur ");
                return redirect()->route('login');
            }

            if($mdp === 'ciat'){
                //renvoyer vers modifier mot de passe
                return redirect()->route('admin_pass');
                //return redirect()->route('modifier_pass');
            }

            return redirect()->route('dashboard');

        }
        Session()->flash('success','Identifiant ou Mot de Passe erroné.');
        return redirect()->route('login');

    }

    public function logout(){
        Auth::logout();
        return redirect()->route('login');
    }

    public function update_pass(){
        return view('update_pass');
    }

    public function modifier_pass(Request $request){
        $password = $request->input('password');
        $data = array('password'=>md5($password));
        DB::table('users')->where('id','=',$request->input('id_users'))->update($data);
        $infoCompte =  DB::table('users')
            ->where('id','=',$request->input('id_users'))->first();
        storeActivity('Creation','Compte',"Modification du compte utilisateur ".$infoCompte->name);
        return redirect()->route('dashboard');
    }

    public function users_get(){


        $groupe = auth()->user()->groupe;

        $users = DB::table('users');


        if ($groupe === 'GE') {
            $users = $users
                ->whereIn('users.groupe', ['SC', 'DM', 'CL']);
        }

        if ($groupe === 'SP') {
            $users = $users
                ->whereNotIn('users.groupe', ['SA','SP']);
        }

        $users = $users->get();


        if ($groupe != 'SA'){
            $currentEntrepotIds = json_decode(auth()->user()->entrepot_id ?? '[]', true);

// Filtrage en PHP : garde seulement les users dont entrepot_id (JSON) contient $currentEntrepot
            $users = $users->filter(function ($user) use ($currentEntrepotIds) {
                $decoded = json_decode($user->entrepot_id ?? '[]', true);
                $userEntrepots = is_array($decoded) ? $decoded : [];

                return count(array_intersect($userEntrepots, $currentEntrepotIds)) > 0;
            })->values();
        }



        $allEntrepots = DB::table('entrepot')->get()->keyBy('id_entrepot');


        foreach ($users as $user) {
            $ids = json_decode($user->entrepot_id ?? '[]', true);
            $user->entrepots = collect($ids)->map(fn($id) => $allEntrepots[$id] ?? null)->filter()->values();
        }


        $entrepots = DB::table('entrepot');

        $userEntrepots = json_decode(auth()->user()->entrepot_id, true); // decode JSON en tableau PHP

        if (in_array(auth()->user()->groupe, ['GE', 'SP']) && is_array($userEntrepots)) {
            $entrepots->whereIn('id_entrepot', $userEntrepots);
        }

        $entrepots = $entrepots->orderBy('nom', 'ASC')->get();


        return view('personnel.index',compact('users','entrepots'));
    }

    public function create_users(Request $request){


        $new_com = $request->nom;
        $mail = $request->mail;
        $mdp = $request->password;


        $data2 = array(
            "personnel"=>$new_com,
            "username"=>$request->username,
            "password"=>md5($mdp),
            "groupe"=>$request->groupe,
            "email"=>$mail,
            "telephone"=>$request->telephone,
            'entrepot_id' => json_encode($request->entrepot_id ?? null),
        );

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $photoPath = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('photos'), $photoPath);
            $data2['photo'] = $photoPath;
        }

        $insert2 = DB::table('users')->insert($data2);

        //dd(DB::getQueryLog());
        if($insert2){
            storeActivity('Creation','Compte',"Creation du compte utilisateur ".$new_com);
            return back()->with('message', 'Enregistrement effectué avec succès du personnel');
        }
        return back()->with('error', "Erreur lors de l'insertion");

    }

    public function update(Request $request, $id)
    {



        $data = array('personnel' => $request->nom,
            'username' => $request->username,
            'email' => $request->mail,
            'telephone' => $request->telephone,
            'groupe' => $request->groupe,
            'updated_at' => now(),
            'entrepot_id' => json_encode($request->entrepot_id ?? null),
        );

        if ($request->password) {
            $data['password'] = md5($request->password);
        }

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $photoPath = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('photos'), $photoPath);
            $data['photo'] = $photoPath;
        }

        DB::table('users')->where('id', $id)->update($data);
        storeActivity('Modification','Compte',"Modification du compte utilisateur ".$request->nom);
        return redirect()->route('users.index')->with('message', 'Utilisateur mis à jour avec succès.');
    }


    public function edit($id)
    {
        $editUser = DB::table('users')->find($id);

        if (!$editUser) {
            return back()->with('error', 'Utilisateur introuvable.');
        }

        $groupe = auth()->user()->groupe;

        $users = DB::table('users');


        if ($groupe === 'GE') {
            $users = $users
                ->whereIn('users.groupe', ['SC', 'DM', 'CL']);
        }

        if ($groupe === 'SP') {
            $users = $users
                ->whereNotIn('users.groupe', ['SA']);
        }

        $users = $users->get();






        $allEntrepots = DB::table('entrepot')->get()->keyBy('id_entrepot');

        foreach ($users as $user) {
            $ids = json_decode($user->entrepot_id ?? '[]', true);
            $user->entrepots = collect($ids)->map(fn($id) => $allEntrepots[$id] ?? null)->filter()->values();
        }





        $entrepots = DB::table('entrepot');
        if(auth()->user()->groupe == 'GE'){
            $entrepots->where('id_entrepot','=',auth()->user()->entrepot_id);
        }

        if(auth()->user()->groupe == 'SP'){
            $entrepots->where('id_entrepot','=',auth()->user()->entrepot_id);
        }

        $entrepots = $entrepots->orderBy('nom','ASC')
            ->get();

        return view('personnel.index', compact('users', 'editUser','entrepots'));
    }



    public function destroy($id)
    {
        $user = DB::table('users')->find($id);

        if (!$user) {
            return redirect()->route('personnel.index')->with('error', 'Utilisateur introuvable.');
        }

        DB::table('users')->where('id', $id)->delete();

        return back()->with('message', 'Utilisateur supprimé avec succès.');
    }

}
