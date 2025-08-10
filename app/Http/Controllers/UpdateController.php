<?php

namespace App\Http\Controllers;

use App\Services\UpdateManager;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Native\Laravel\Facades\Settings;

class UpdateController extends Controller
{
    protected $updateManager;

    public function __construct(UpdateManager $updateManager)
    {
        $this->updateManager = $updateManager;
    }

    /**
     * Affiche la page des paramètres de mise à jour
     */
    public function index()
    {
        $currentVersion = config('nativephp.version', '1.0.0');
        $autoUpdateEnabled = $this->updateManager->isAutoUpdateEnabled();
        $updateHistory = $this->updateManager->getUpdateHistory();
        $lastCheck = Settings::get('last_update_check', null);

        return view('updates.index', compact(
            'currentVersion', 
            'autoUpdateEnabled', 
            'updateHistory', 
            'lastCheck'
        ));
    }

    /**
     * Vérifie manuellement les mises à jour
     */
    public function check(Request $request): JsonResponse
    {
        try {
            $hasUpdate = $this->updateManager->checkManually();
            
            return response()->json([
                'success' => true,
                'has_update' => $hasUpdate,
                'message' => $hasUpdate 
                    ? 'Mise à jour disponible !' 
                    : 'Vous avez la dernière version.',
                'last_check' => now()->format('d/m/Y H:i')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la vérification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Active/désactive les mises à jour automatiques
     */
    public function toggleAutoUpdate(Request $request): JsonResponse
    {
        $enabled = $request->boolean('enabled');
        
        try {
            $this->updateManager->setAutoUpdate($enabled);
            
            return response()->json([
                'success' => true,
                'enabled' => $enabled,
                'message' => $enabled 
                    ? 'Mises à jour automatiques activées' 
                    : 'Mises à jour automatiques désactivées'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère les informations de version
     */
    public function version(): JsonResponse
    {
        return response()->json([
            'current_version' => config('nativephp.version', '1.0.0'),
            'build_date' => config('app.build_date', null),
            'environment' => config('app.env'),
            'auto_update_enabled' => $this->updateManager->isAutoUpdateEnabled()
        ]);
    }

    /**
     * Récupère l'historique des mises à jour
     */
    public function history(): JsonResponse
    {
        $history = $this->updateManager->getUpdateHistory();
        
        return response()->json([
            'success' => true,
            'history' => $history
        ]);
    }

    /**
     * API endpoint pour vérifier les mises à jour (utilisé par le frontend)
     */
    public function apiCheck(): JsonResponse
    {
        try {
            $hasUpdate = $this->updateManager->checkForUpdates();
            $lastCheck = Settings::get('last_update_check');
            
            return response()->json([
                'has_update' => $hasUpdate,
                'current_version' => config('nativephp.version', '1.0.0'),
                'last_check' => $lastCheck ? date('d/m/Y H:i', $lastCheck) : null,
                'auto_update_enabled' => $this->updateManager->isAutoUpdateEnabled()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}