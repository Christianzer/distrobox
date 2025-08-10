@extends('layout')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Mises à jour</h1>
    </div>

    <div class="row">
        <!-- Informations de version -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informations de version</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Version actuelle :</strong>
                        <span class="badge badge-primary">{{ $currentVersion }}</span>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Dernière vérification :</strong>
                        <span id="last-check">
                            {{ $lastCheck ? date('d/m/Y H:i', $lastCheck) : 'Jamais' }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <strong>Mises à jour automatiques :</strong>
                        <span class="badge {{ $autoUpdateEnabled ? 'badge-success' : 'badge-secondary' }}">
                            {{ $autoUpdateEnabled ? 'Activées' : 'Désactivées' }}
                        </span>
                    </div>

                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-primary" id="check-updates">
                            <i class="fas fa-sync-alt"></i> Vérifier maintenant
                        </button>
                        
                        <button type="button" class="btn btn-outline-secondary" id="toggle-auto-update">
                            {{ $autoUpdateEnabled ? 'Désactiver' : 'Activer' }} les mises à jour auto
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historique des mises à jour -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Historique des mises à jour</h6>
                </div>
                <div class="card-body">
                    <div id="update-history">
                        @if(empty($updateHistory))
                            <p class="text-muted">Aucun historique de mise à jour</p>
                        @else
                            <div class="timeline">
                                @foreach($updateHistory as $update)
                                    <div class="timeline-item mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="timeline-marker 
                                                {{ $update['status'] === 'success' ? 'bg-success' : 'bg-danger' }}">
                                            </div>
                                            <div class="timeline-content ml-3">
                                                <strong>Version {{ $update['version'] }}</strong>
                                                <br>
                                                <small class="text-muted">
                                                    {{ date('d/m/Y H:i', $update['timestamp']) }}
                                                    - {{ ucfirst($update['status']) }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Paramètres avancés -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Paramètres avancés</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Canal de mise à jour :</label>
                                <select class="form-control" id="update-channel">
                                    <option value="stable">Stable (recommandé)</option>
                                    <option value="beta">Beta</option>
                                    <option value="alpha">Alpha (expérimental)</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Fréquence de vérification :</label>
                                <select class="form-control" id="check-frequency">
                                    <option value="3600">Toutes les heures</option>
                                    <option value="21600">Toutes les 6 heures</option>
                                    <option value="86400">Quotidienne</option>
                                    <option value="604800">Hebdomadaire</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="notify-updates" 
                               {{ $autoUpdateEnabled ? 'checked' : '' }}>
                        <label class="form-check-label" for="notify-updates">
                            Recevoir des notifications pour les nouvelles mises à jour
                        </label>
                    </div>

                    <hr>

                    <div class="text-muted">
                        <small>
                            <strong>Note :</strong> Les mises à jour automatiques garantissent que vous avez 
                            toujours les dernières fonctionnalités et corrections de sécurité. 
                            L'application se redémarrera automatiquement après une mise à jour.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Variables globales
    let autoUpdateEnabled = {{ $autoUpdateEnabled ? 'true' : 'false' }};

    // Vérification manuelle des mises à jour
    $('#check-updates').click(function() {
        const $btn = $(this);
        const originalText = $btn.html();
        
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Vérification...');
        $btn.prop('disabled', true);

        $.post('{{ route("updates.check") }}')
            .done(function(response) {
                if (response.success) {
                    showAlert(response.message, response.has_update ? 'info' : 'success');
                    $('#last-check').text(response.last_check);
                } else {
                    showAlert('Erreur: ' + response.message, 'danger');
                }
            })
            .fail(function() {
                showAlert('Erreur de connexion', 'danger');
            })
            .always(function() {
                $btn.html(originalText);
                $btn.prop('disabled', false);
            });
    });

    // Toggle des mises à jour automatiques
    $('#toggle-auto-update').click(function() {
        const $btn = $(this);
        const newState = !autoUpdateEnabled;
        
        $.post('{{ route("updates.auto-toggle") }}', {
            enabled: newState,
            _token: '{{ csrf_token() }}'
        })
        .done(function(response) {
            if (response.success) {
                autoUpdateEnabled = response.enabled;
                $btn.text(autoUpdateEnabled ? 'Désactiver les mises à jour auto' : 'Activer les mises à jour auto');
                
                const badge = $('.badge:contains("Activées"), .badge:contains("Désactivées")');
                badge.removeClass('badge-success badge-secondary');
                badge.addClass(autoUpdateEnabled ? 'badge-success' : 'badge-secondary');
                badge.text(autoUpdateEnabled ? 'Activées' : 'Désactivées');
                
                showAlert(response.message, 'success');
            } else {
                showAlert('Erreur: ' + response.message, 'danger');
            }
        })
        .fail(function() {
            showAlert('Erreur de connexion', 'danger');
        });
    });

    // Fonction pour afficher les alertes
    function showAlert(message, type) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `;
        
        $('.container-fluid').prepend(alertHtml);
        
        // Auto-dismiss après 5 secondes
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
    }

    // Vérification périodique des mises à jour (toutes les 30 minutes)
    if (autoUpdateEnabled) {
        setInterval(function() {
            $.get('{{ route("updates.version") }}')
                .done(function(response) {
                    // Logique pour gérer les notifications de mise à jour
                });
        }, 1800000); // 30 minutes
    }
});
</script>

<style>
.timeline-marker {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    flex-shrink: 0;
}

.timeline-item {
    border-left: 2px solid #e3e6f0;
    padding-left: 15px;
    margin-left: 6px;
}

.timeline-item:last-child {
    border-left: none;
}

.card-header h6 {
    display: flex;
    align-items: center;
}

.card-header h6::before {
    content: "\f021";
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    margin-right: 8px;
    color: #4e73df;
}
</style>
@endsection