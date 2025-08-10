<?php

namespace App\Providers;

use Native\Laravel\Facades\Window;
use Native\Laravel\Facades\Menu;
use Native\Laravel\Facades\MenuBar;
use Native\Laravel\Contracts\ProvidesPhpIni;
use App\Services\UpdateManager;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        // Configuration de la fenêtre principale
        Window::open()
            ->title('Distrobox')
            ->width(1280)
            ->height(800)
            ->minWidth(1024)
            ->minHeight(768)
            ->rememberState()
            ->showDevTools(false)
            ->icon($this->getAppIcon());

        // Configuration du menu de l'application
        $this->setupApplicationMenu();

        // Vérification automatique des mises à jour au démarrage
        $this->checkForUpdatesOnStartup();
    }

    /**
     * Configure le menu de l'application
     */
    protected function setupApplicationMenu()
    {
        // Vider le menu existant
        MenuBar::empty();
        
        // Créer seulement le menu Aide
        $helpMenu = Menu::new()
            ->label('Aide')
            ->submenu([
                Menu::new()->label('Documentation')->click(fn() => $this->downloadDocumentation()),
            ]);

        // Appliquer le nouveau menu
        MenuBar::default([
            $helpMenu,
        ]);
    }

    /**
     * Vérifie les mises à jour au démarrage
     */
    protected function checkForUpdatesOnStartup()
    {
        // Utiliser une tâche en arrière-plan pour ne pas bloquer le démarrage
        dispatch(function () {
            try {
                $updateManager = app(UpdateManager::class);
                $updateManager->checkForUpdates();
            } catch (\Exception $e) {
                \Log::error('Erreur lors de la vérification de mise à jour au démarrage: ' . $e->getMessage());
            }
        });
    }

    /**
     * Affiche la boîte de dialogue "À propos"
     */
    protected function showAbout()
    {
        $version = config('nativephp.version', '1.0.0');
        $author = config('nativephp.author', 'Votre Nom');
        
        \Native\Laravel\Facades\Notification::title('À propos de Distrobox')
            ->message("Version {$version}\nDéveloppé par {$author}\nApplication de gestion d'entreprise")
            ->show();
    }

    /**
     * Lance le téléchargement de la documentation
     */
    protected function downloadDocumentation()
    {
        try {
            // Chemin vers la documentation
            $docFiles = [
                'DOCUMENTATION_UTILISATEUR.md' => 'Documentation utilisateur',
                'CLAUDE.md' => 'Guide du développeur',
                'AUTO_UPDATE_GUIDE.md' => 'Guide des mises à jour',
                'NATIVE_APP_GUIDE.md' => 'Guide de l\'application desktop',
                'scripts/build/README.md' => 'Guide des builds'
            ];

            // Créer un dossier temporaire pour la documentation
            $tempDir = storage_path('app/temp/documentation');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            // Copier les fichiers de documentation
            $copiedFiles = [];
            foreach ($docFiles as $file => $description) {
                $sourcePath = base_path($file);
                if (file_exists($sourcePath)) {
                    $fileName = basename($file);
                    $destPath = $tempDir . '/' . $fileName;
                    copy($sourcePath, $destPath);
                    $copiedFiles[] = $fileName;
                }
            }

            if (!empty($copiedFiles)) {
                // Ouvrir le dossier dans l'explorateur de fichiers
                $this->openFolder($tempDir);
                
                \Native\Laravel\Facades\Notification::title('Documentation Distrobox')
                    ->message('Documentation téléchargée dans: ' . $tempDir)
                    ->show();
            } else {
                \Native\Laravel\Facades\Notification::title('Documentation')
                    ->message('Aucun fichier de documentation trouvé.')
                    ->show();
            }

        } catch (\Exception $e) {
            \Log::error('Erreur lors du téléchargement de la documentation: ' . $e->getMessage());
            
            \Native\Laravel\Facades\Notification::title('Erreur')
                ->message('Impossible de télécharger la documentation.')
                ->show();
        }
    }

    /**
     * Ouvre un dossier dans l'explorateur de fichiers
     */
    protected function openFolder($path)
    {
        $os = php_uname('s');
        
        try {
            if (strpos($os, 'Windows') !== false) {
                exec("explorer " . escapeshellarg(str_replace('/', '\\', $path)));
            } elseif (strpos($os, 'Darwin') !== false) {
                exec("open " . escapeshellarg($path));
            } else {
                // Linux
                exec("xdg-open " . escapeshellarg($path));
            }
        } catch (\Exception $e) {
            \Log::warning('Impossible d\'ouvrir le dossier: ' . $e->getMessage());
        }
    }

    /**
     * Retourne le chemin de l'icône appropriée selon la plateforme
     */
    protected function getAppIcon(): string
    {
        $os = php_uname('s');
        
        if (strpos($os, 'Windows') !== false) {
            // Windows : utiliser l'icône ICO
            if (file_exists(resource_path('icons/windows/app.ico'))) {
                return resource_path('icons/windows/app.ico');
            }
        } elseif (strpos($os, 'Darwin') !== false) {
            // macOS : utiliser l'icône ICNS si disponible, sinon PNG haute résolution
            if (file_exists(resource_path('icons/macos/app.icns'))) {
                return resource_path('icons/macos/app.icns');
            } elseif (file_exists(resource_path('icons/macos/app.png'))) {
                return resource_path('icons/macos/app.png');
            }
        } else {
            // Linux : utiliser l'icône PNG principale
            if (file_exists(resource_path('icons/linux/app.png'))) {
                return resource_path('icons/linux/app.png');
            }
        }
        
        // Fallback vers l'icône originale
        return 'public/front/logoEmaster.png';
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [
        ];
    }
}
