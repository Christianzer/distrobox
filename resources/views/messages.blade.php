@extends('layout')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item text-primary font-weight-bold text-uppercase active" aria-current="page">Gestion des Messages</li>
        </ol>
    </nav>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session()->get('message') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header bg-primary py-3">
            <span class="m-0 font-weight-bold text-white">{{ isset($editMessage) ? 'Modifier le Message' : 'Créer un Message' }}</span>
        </div>
        <div class="card-body">
            <form action="{{ isset($editMessage) ? route('message.update', $editMessage->id_message) : route('message.store') }}" method="post">
                @csrf
                @if(isset($editMessage))
                    @method('PUT')
                @endif

                <div class="form-group">
                    <label>Message</label>
                    <textarea class="form-control" required name="message">{{ isset($editMessage) ? $editMessage->message : '' }}</textarea>
                </div>

                <div class="form-group">
                    <label>Agents concernés</label>
                    <select class="form-control" name="id_agent[]" multiple required>
                        @foreach($agents as $agent)
                            <option value="{{ $agent->id }}"
                                {{ isset($editMessage) && in_array($agent->id, $selectedAgents) ? 'selected' : '' }}>
                                {{ $agent->personnel }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="text-right">
                    <a href="{{ route('message.index') }}" class="btn btn-outline-danger">Annuler</a>
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-save"></i> {{ isset($editMessage) ? 'Mettre à jour' : 'Enregistrer' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div class="card shadow mb-4">
        <div class="card-header bg-primary py-3">
            <span class="m-0 font-weight-bold text-white">Liste des Messages</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="dataTable">
                    <thead class="bg-primary">
                    <tr class="text-white">
                        <th>Message</th>
                        <th>Agents concernés</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($messages as $message)
                        <tr>
                            <td>{{ $message->message }}</td>
                            <td>{{ $message->agents_noms }}</td>
                            <td>
                                <a href="{{ route('message.edit', $message->id_message) }}" class="btn btn-sm btn-outline-info">Modifier</a>
                                <form action="{{ route('message.destroy', $message->id_message) }}" method="post" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "ordering": false,
                "language": {
                    "sEmptyTable": "Aucune donnée disponible",
                    "sInfo": "Affichage de _START_ à _END_ sur _TOTAL_ éléments",
                    "sInfoEmpty": "Aucun élément à afficher",
                    "sLengthMenu": "Afficher _MENU_ éléments",
                    "sSearch": "Rechercher :",
                    "oPaginate": {
                        "sFirst": "Premier",
                        "sLast": "Dernier",
                        "sNext": "Suivant",
                        "sPrevious": "Précédent"
                    }
                }
            });
        });
    </script>
@endsection
