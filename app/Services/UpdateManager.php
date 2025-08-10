<?php

namespace App\Services;

use Native\Laravel\Facades\Notification;
use Native\Laravel\Facades\Settings;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Exception;

class UpdateManager
{
    protected $currentVersion;
    protected $updateEndpoint;
    protected $lastCheckKey = 'last_update_check';
    protected $autoUpdateKey = 'auto_update_enabled';

    public function __construct()
    {
        $this->currentVersion = config('nativephp.version', '1.0.0');
        $this->updateEndpoint = $this->buildUpdateEndpoint();
    }

    /**
     * Vérifie s'il y a des mises à jour disponibles
     */
    public function checkForUpdates($manual = false)
    {
        try {
            // Si ce n'est pas manuel, vérifier l'intervalle
            if (!$manual && !$this->shouldCheckForUpdates()) {
                return false;
            }

            $latestVersion = $this->getLatestVersion();
            
            if ($this->hasNewerVersion($latestVersion)) {
                $this->handleNewVersionAvailable($latestVersion, $manual);
                return true;
            }

            if ($manual) {
                Notification::title('Distrobox')
                    ->message('Vous avez déjà la dernière version.')
                    ->show();
            }

            // Mettre à jour le timestamp de vérification
            Settings::set($this->lastCheckKey, now()->timestamp);

            return false;

        } catch (Exception $e) {
            Log::error('Erreur lors de la vérification de mise à jour: ' . $e->getMessage());
            
            if ($manual) {
                Notification::title('Erreur de mise à jour')
                    ->message('Impossible de vérifier les mises à jour.')
                    ->show();
            }
            
            return false;
        }
    }

    /**
     * Détermine si on doit vérifier les mises à jour
     */
    protected function shouldCheckForUpdates()
    {
        $lastCheck = Settings::get($this->lastCheckKey, 0);
        $interval = config('nativephp.update_check_interval', 3600); // 1 heure par défaut
        
        return (now()->timestamp - $lastCheck) > $interval;
    }

    /**
     * Récupère la dernière version disponible
     */
    protected function getLatestVersion()
    {
        if (config('nativephp.updater.default') === 'github') {
            return $this->getLatestVersionFromGitHub();
        }

        throw new Exception('Provider de mise à jour non supporté');
    }

    /**
     * Récupère la dernière version depuis GitHub
     */
    protected function getLatestVersionFromGitHub()
    {
        $owner = config('nativephp.updater.providers.github.owner');
        $repo = config('nativephp.updater.providers.github.repo');
        $token = config('nativephp.updater.providers.github.token');

        $headers = [];
        if ($token) {
            $headers['Authorization'] = "token {$token}";
        }

        $response = Http::withHeaders($headers)
            ->get("https://api.github.com/repos/{$owner}/{$repo}/releases/latest");

        if (!$response->successful()) {
            throw new Exception('Impossible de récupérer les informations de release');
        }

        $release = $response->json();
        return ltrim($release['tag_name'], 'v');
    }

    /**
     * Vérifie si une version est plus récente
     */
    protected function hasNewerVersion($latestVersion)
    {
        return version_compare($latestVersion, $this->currentVersion, '>');
    }

    /**
     * Gère une nouvelle version disponible
     */
    protected function handleNewVersionAvailable($latestVersion, $manual = false)
    {
        $autoUpdate = Settings::get($this->autoUpdateKey, true);

        if ($autoUpdate && !$manual) {
            $this->startAutoUpdate($latestVersion);
        } else {
            $this->promptForUpdate($latestVersion);
        }
    }

    /**
     * Démarre la mise à jour automatique
     */
    protected function startAutoUpdate($latestVersion)
    {
        Notification::title('Mise à jour Distrobox')
            ->message("Téléchargement de la version {$latestVersion} en cours...")
            ->show();

        Log::info("Début de la mise à jour automatique vers la version {$latestVersion}");

        // NativePHP gère automatiquement le téléchargement et l'installation
        // L'application se redémarrera automatiquement après la mise à jour
    }

    /**
     * Propose la mise à jour à l'utilisateur
     */
    protected function promptForUpdate($latestVersion)
    {
        Notification::title('Mise à jour disponible')
            ->message("Distrobox v{$latestVersion} est disponible. Cliquez pour mettre à jour.")
            ->show();

        Log::info("Nouvelle version disponible: {$latestVersion}");
    }

    /**
     * Active/désactive les mises à jour automatiques
     */
    public function setAutoUpdate($enabled)
    {
        Settings::set($this->autoUpdateKey, $enabled);
        
        $status = $enabled ? 'activées' : 'désactivées';
        Notification::title('Paramètres de mise à jour')
            ->message("Les mises à jour automatiques sont maintenant {$status}.")
            ->show();
    }

    /**
     * Récupère le statut des mises à jour automatiques
     */
    public function isAutoUpdateEnabled()
    {
        return Settings::get($this->autoUpdateKey, true);
    }

    /**
     * Force une vérification manuelle
     */
    public function checkManually()
    {
        return $this->checkForUpdates(true);
    }

    /**
     * Construit l'endpoint de mise à jour
     */
    protected function buildUpdateEndpoint()
    {
        // Utilisé pour des providers personnalisés si nécessaire
        return null;
    }

    /**
     * Récupère l'historique des mises à jour
     */
    public function getUpdateHistory()
    {
        // Implémentation pour tracker l'historique des mises à jour
        return Settings::get('update_history', []);
    }

    /**
     * Ajoute une entrée à l'historique
     */
    public function addToHistory($version, $date, $status = 'success')
    {
        $history = $this->getUpdateHistory();
        $history[] = [
            'version' => $version,
            'date' => $date,
            'status' => $status,
            'timestamp' => now()->timestamp
        ];

        // Garder seulement les 10 dernières entrées
        $history = array_slice($history, -10);
        
        Settings::set('update_history', $history);
    }
}