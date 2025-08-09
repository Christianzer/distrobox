<?php

use App\Http\Controllers\ApiMasterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('voir/{userId}',function ($userId){

    $achat = DB::table('transaction')
        ->orderBy('date_transaction', 'desc')
        ->where('type_transaction','=','AU')
        ->where('id_agent','=',$userId)
        ->where('statut', 1)
        ->where('course', 1)
        ->count();


    $retour = DB::table('transaction')
        ->orderBy('date_transaction', 'desc')
        ->where('type_transaction','=','RU')
        ->where('id_agent','=',$userId)
        ->where('statut', 1)
        ->where('course', 1)
        ->count();

    return response()->json([
        'encaissements'=>$achat,
        'decaissements'=>$retour,
    ]);
});

Route::post('login', [ApiMasterController::class,'login']);
Route::get('dashboard/{id}', [ApiMasterController::class,'dashboard']);
Route::get('transactions/{id}/{type}', [ApiMasterController::class,'transaction']);
Route::post('validation', [ApiMasterController::class,'validation']);
Route::post('imprimer', [ApiMasterController::class,'imprimer']);
Route::post('bordereaux',[ApiMasterController::class,'bordereaux']);
Route::post('localisation',[ApiMasterController::class,'sendPosition']);
Route::get('historiques/{id}',[ApiMasterController::class,'historiques']);

Route::get('transact/unnotified/{userId}', function ($userId) {

    $transactions = DB::table('transaction')
        ->where('transaction.id_agent', $userId)
        ->whereIn('transaction.type_transaction',['AU','RU'])
        ->join('client','transaction.code_client','=','client.code')
        ->where('transaction.is_notified', false)
        ->get();

    return response()->json($transactions);
});


Route::post('transact/mark-notified', function (Request $request) {
    DB::table('transaction')
        ->whereIn('id_transaction', $request->input('transaction_ids'))
        ->update(['is_notified' => true]);
    return response()->json(['message' => 'Notifications marquées comme lues']);
});

Route::get('voirPosition/{id}',function ($id_agent){

    $informationsMap = DB::table('localisation')
        ->where('user_id', $id_agent)
        ->orderByDesc('date_loca')
        ->first();

    if (!$informationsMap || !$informationsMap->latitude || !$informationsMap->longitude) {
        return response()->json(['error' => 'Coordonnées non disponibles'], 404);
    }

    return response()->json([
        'latitude' => $informationsMap->latitude,
        'longitude' => $informationsMap->longitude
    ]);
})->name('voirPosition');


Route::get('messages/unnotified/{userId}', function ($userId) {

    $transactions = DB::table('message_agents')
        ->where('agent_id', $userId)
        ->where('is_notified', false)
        ->get();

    return response()->json($transactions);
});

Route::post('messages/mark-notified', function (Request $request) {
    DB::table('message_agents')
        ->whereIn('message_id', $request->input('message_ids'))
        ->update(['is_notified' => true]);
    return response()->json(['message' => 'Notifications marquées comme lues']);
});

Route::get('messages/read/{userId}', function ($userId) {

    $transactions = DB::table('message_agents')
        ->join('message','message.id_message','=','message_agents.message_id')
        ->orderBy('message.created_at', 'desc')
        ->where('message_agents.agent_id', $userId)
        ->get();

    return response()->json($transactions);
});
