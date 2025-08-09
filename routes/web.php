<?php


use App\Http\Controllers\CaissesController;
use App\Http\Controllers\ClientController;

use App\Http\Controllers\EntrepotController;
use App\Http\Controllers\ProduitsController;
use App\Http\Controllers\RecouvrementController;

use App\Http\Controllers\SoldeController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UsersControllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['auth', 'activation']], function () {
    Route::get('/', [UsersControllers::class, 'control'])->name('control');
    Route::get('page/dashboard', [UsersControllers::class, 'dashboard'])->name('dashboard');
    Route::post('page/dashboard/consulter', [UsersControllers::class, 'consulter'])->name('dashboard.consulter');
    Route::get("page/update/password", [UsersControllers::class, 'update_pass'])->name('admin_pass');
    Route::post("page/update/password/modifier", [UsersControllers::class, 'modifier_pass'])->name('admin_modifier');

    Route::get("page/utilisateurs/index",[UsersControllers::class,'users_get'])->name('users.index');
    Route::post("page/utilisateurs/create",[UsersControllers::class,'create_users'])->name('users.store');
    Route::get("page/utilisateurs/{id}/edit",[UsersControllers::class,'edit'])->name('users.edit');
    Route::put("page/utilisateurs/{id}",[UsersControllers::class,'update'])->name('users.update');
    Route::delete("page/utilisateurs/{id}",[UsersControllers::class,'destroy'])->name('users.destroy');


    Route::get("page/entrepots/index",[EntrepotController::class,'index'])->name('entrepots.index');
    Route::post("page/entrepots/create",[EntrepotController::class,'store'])->name('entrepots.store');
    Route::get("page/entrepots/{id}/edit",[EntrepotController::class,'edit'])->name('entrepots.edit');
    Route::put("page/entrepots/{id}",[EntrepotController::class,'update'])->name('entrepots.update');
    Route::delete("page/entrepots/{id}",[EntrepotController::class,'destroy'])->name('entrepots.destroy');


    Route::get("page/stock/index",[StockController::class,'index'])->name('stock.index');
    Route::post("page/stock/entrepot",[StockController::class,'consulter'])->name('stock.consulter');
    Route::post("page/stock/attribuer",[StockController::class,'attribuer'])->name('stock.attribuer');
    Route::post("page/stock/attribuer/enregistrer",[StockController::class,'enregistrer'])->name('stock.enregistrer');
    Route::post("page/stock/enregistrer",[StockController::class,'store'])->name('stock.store');
    Route::post("page/stock/update",[StockController::class,'update'])->name('stock.update');
    Route::post("page/stock/delete",[StockController::class,'deleteBL'])->name('stock.delete');
    Route::get("page/attribution/index",[StockController::class,'attribution'])->name('stock.attribution');

    Route::get("page/clients/index",[ClientController::class,'index'])->name('clients.index');
    Route::post("page/clients/create",[ClientController::class,'store'])->name('clients.store');
    Route::get("page/clients/{id}/edit",[ClientController::class,'edit'])->name('clients.edit');
    Route::put("page/clients/{id}",[ClientController::class,'update'])->name('clients.update');
    Route::delete("page/clients/{id}",[ClientController::class,'destroy'])->name('clients.destroy');


    Route::get("page/produits/index",[ProduitsController::class,'index'])->name('produits.index');
    Route::post("page/produits/create",[ProduitsController::class,'store'])->name('produits.store');
    Route::post("page/produits/consulter",[ProduitsController::class,'consulter'])->name('produits.consulter');
    Route::get("page/produits/{id}/edit",[ProduitsController::class,'edit'])->name('produits.edit');
    Route::put("page/produits/{id}",[ProduitsController::class,'update'])->name('produits.update');
    Route::delete("page/produits/{id}",[ProduitsController::class,'destroy'])->name('produits.destroy');
    Route::post("page/ajax/produit",[ProduitsController::class,'ajaxUpdate'])->name('produits.ajax');



    Route::get("page/soldes/index",[SoldeController::class,'index'])->name('soldes.index');
    Route::post("page/soldes/create",[SoldeController::class,'storeOrUpdate'])->name('soldes.store');
    Route::post("page/soldes/delete",[SoldeController::class,'delete'])->name('soldes.delete');
    Route::post("page/soldes/cloturer",[SoldeController::class,'cloturer'])->name('soldes.cloturer');




    Route::get("page/transactions/index",[TransactionController::class,'index'])->name('transactions.index');
    Route::get("page/transactions/create/{code}",[TransactionController::class,'transaction'])->name('transactions.create');
    Route::post("page/transactions/create",[TransactionController::class,'store'])->name('transactions.store');
    Route::post('page/transactions/update',[TransactionController::class,'update'])->name('transactions.update');
    Route::post("page/transactions/delete",[TransactionController::class,'delete'])->name('transactions.delete');
    Route::get("page/point",[TransactionController::class,'pointJour'])->name('transactions.point');
    Route::get("page/recu/{code}",[TransactionController::class,'recu'])->name('transactions.recu');



    Route::get("page/recouvrements/index",[RecouvrementController::class,'index'])->name('recouvrements.index');
    Route::post("page/recouvrements/consulter",[RecouvrementController::class,'consulter'])->name('recouvrements.consulter');
    Route::post("page/recouvrements/paiement",[RecouvrementController::class,'paiement'])->name('recouvrements.paiement');
    Route::get("page/recouvrements/imprimer/{code}/{entrepot}",[RecouvrementController::class,'imprimer'])->name('recouvrements.imprimer');
    Route::get("page/comptes/index",[RecouvrementController::class,'compte'])->name('compte.index');
    Route::post("page/comptes/consulter",[RecouvrementController::class,'consulterCompte'])->name('compte.consulterCompte');


    Route::get("page/caisses/index",[CaissesController::class,'index'])->name('caisses.index');
    Route::post("page/caisses/consulter",[CaissesController::class,'consulter'])->name('caisses.consulter');
    Route::post("page/caisses/enregistrer",[CaissesController::class,'enregistrer'])->name('caisses.enregistrer');
    Route::post("page/caisses/supprimer",[CaissesController::class,'supprimer'])->name('caisses.supprimer');


    Route::get("page/bd/index",[ClientController::class,'bd'])->name('bd.index');
    Route::post("page/bd/vider",[ClientController::class,'vider'])->name('bd.vider');

    Route::get('page/message/index',[RecouvrementController::class,'messages'])->name('message.index');
    Route::post('page/message/create',[RecouvrementController::class,'store'])->name('message.store');
    Route::get('page/message/edit/{id}',[RecouvrementController::class,'edit'])->name('message.edit');
    Route::put('page/message/update/{id}',[RecouvrementController::class,'update'])->name('message.update');
    Route::delete('page/message/delete/{id}',[RecouvrementController::class,'destroy'])->name('message.destroy');

});
Route::get('page/login', [UsersControllers::class, 'connexion'])->name('login');
Route::post('page/login/connexion', [UsersControllers::class, 'login'])->name("connecter");
Route::get('autocomplete', [TransactionController::class, 'autocomplete'])->name("autocomplete");
Route::get("logout", [UsersControllers::class, 'logout'])->name('admin_logout');
Route::get('transactions/data',[TransactionController::class,'getData'])->name('transactions.data');
Route::get('transactions/soldes',[TransactionController::class,'getSoldes'])->name('transactions.soldes');
Route::get('transactions/edit/{code}',[TransactionController::class,'LeanTransaction'])->name('transactions.code');
