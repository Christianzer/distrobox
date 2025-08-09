@extends('layout')
@section('content')

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item text-primary font-weight-bold text-uppercase active" aria-current="page">
                Entrepots
            </li>
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

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session()->get('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header bg-primary py-3">
            <span class="m-0 font-weight-bold text-white">{{ isset($editClient) ? 'Modifier un entrepot' : 'Ajouter un nouveau entrepot' }}</span>
        </div>
        <div class="card-body">
            <form action="{{ isset($editClient) ? route('entrepots.update', $editClient->id_entrepot) : route('entrepots.store') }}" method="post">
                @csrf
                @if(isset($editClient))
                    @method('PUT')
                @endif

                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>Nom</label>
                        <input type="text" class="form-control" name="nom" value="{{ isset($editClient) ? $editClient->nom : '' }}">
                    </div>
                    <div class="form-group col-md-3">
                        <label>Adresse</label>
                        <input type="text" class="form-control" name="lieu" value="{{ isset($editClient) ? $editClient->lieu : '' }}">
                    </div>
                    <div class="form-group col-md-3">
                        <label>Contact</label>
                        <input type="text" class="form-control" name="contact" value="{{ isset($editClient) ? $editClient->contact : '' }}">
                    </div>
                </div>


                <div align="right">
                    <a href="{{ route('entrepots.index') }}" class="btn btn-outline-danger">Annuler</a>
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-save"></i> {{ isset($editClient) ? 'Mettre à jour' : 'Enregistrer' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header bg-primary py-3">
            <span class="m-0 font-weight-bold text-white">Liste des entrepots</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="dataTable">
                    <thead class="bg-primary">
                    <tr class="text-white"></th>
                        <th>Nom</th>
                        <th>Adresse</th>
                        <th>Contact</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($entrepots as $client)
                        <tr>
                            <td>{{ $client->nom }}</td>
                            <td>{{ $client->lieu }}</td>
                            <td>{{ $client->contact }}</td>
                            <td>
                                <a href="{{ route('clients.edit', $client->id_entrepot )}}" class="btn btn-sm btn-outline-info">Modifier</a>
                                <form action="{{ route('clients.destroy', $client->id_entrepot) }}" method="post" class="d-inline">
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
                    "sEmptyTable": "Aucune donnée disponible dans le tableau",
                    "sInfo": "Affichage de l'élément _START_ à _END_ sur _TOTAL_ éléments",
                    "sInfoEmpty": "Affichage de l'élément 0 à 0 sur 0 élément",
                    "sInfoFiltered": "(filtré à partir de _MAX_ éléments au total)",
                    "sLengthMenu": "Afficher _MENU_ éléments",
                    "sLoadingRecords": "Chargement...",
                    "sProcessing": "Traitement...",
                    "sSearch": "Rechercher :",
                    "sZeroRecords": "Aucun élément correspondant trouvé",
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
