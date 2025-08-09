@extends('layout')
@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item text-primary font-weight-bold text-uppercase active" aria-current="page">Gestion du Personnel</li>
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
            <span class="m-0 font-weight-bold text-white ">{{ isset($editUser) ? 'Modifier un personnel' : 'Ajouter un nouveau personnel' }}</span>
        </div>
        <div class="card-body">
            <form action="{{ isset($editUser) ? route('users.update', $editUser->id) : route('users.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                @if(isset($editUser))
                    @method('PUT')
                @endif
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>Photo du personnel</label>
                        <input type="file" class="form-control" name="photo">
                    </div>
                    <div class="form-group col-md-5">
                        <label>Nom et Prénoms du personnel</label>
                        <input type="text" class="form-control" required name="nom" value="{{ isset($editUser) ? $editUser->personnel : '' }}">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Nom d'utilisateur</label>
                        <input type="text" class="form-control" required name="username" value="{{ isset($editUser) ? $editUser->username : '' }}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>Email</label>
                        <input type="email" class="form-control"  name="mail" value="{{ isset($editUser) ? $editUser->email : '' }}">
                    </div>
                    <div class="form-group col-md-3">
                        <label>Telephone</label>
                        <input type="text" class="form-control"  name="telephone" value="{{ isset($editUser) ? $editUser->telephone : '' }}" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Mot de passe</label>
                        <input type="password" class="form-control" @if(!isset($editUser)) required @endif name="password" value="">
                    </div>
                    <div class="form-group col-md-3">
                        <label>Type utilisateur</label>
                        <select class="form-control" required name="groupe">
                            @if (auth()->user()->groupe == 'GE')
                                <option value="SC" {{ isset($editUser) && $editUser->groupe == 'SC' ? 'selected' : '' }}>Commercial</option>
                                <option value="DM" {{ isset($editUser) && $editUser->groupe == 'DM' ? 'selected' : '' }}>Client DMG</option>
                                <option value="CL" {{ isset($editUser) && $editUser->groupe == 'CL' ? 'selected' : '' }}>Client</option>
                            @else
                                <option value="SC" {{ isset($editUser) && $editUser->groupe == 'SC' ? 'selected' : '' }}>Commercial</option>
                                <option value="SP" {{ isset($editUser) && $editUser->groupe == 'SP' ? 'selected' : '' }}>Superviseur</option>
                                <option value="SA" {{ isset($editUser) && $editUser->groupe == 'SA' ? 'selected' : '' }}>Super Admin</option>
                                <option value="FO" {{ isset($editUser) && $editUser->groupe == 'FO' ? 'selected' : '' }}>Fournisseur</option>
                                <option value="CL" {{ isset($editUser) && $editUser->groupe == 'CL' ? 'selected' : '' }}>Client</option>
                                <option value="GE" {{ isset($editUser) && $editUser->groupe == 'GE' ? 'selected' : '' }}>Gerant</option>
                                <option value="DM" {{ isset($editUser) && $editUser->groupe == 'DM' ? 'selected' : '' }}>Client DMG</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>Dépenses</label>
                        <input type="text" class="form-control" name="depenses" value="{{ isset($editUser) ? $editUser->depense : 0 }}">
                    </div>
                    <div class="form-group col-md-3">
                        <label>Entrepôts</label>
                        <select
                            class="form-control select2"
                            name="entrepot_id[]"
                            @if(auth()->user()->groupe != 'GE' && auth()->user()->groupe != 'SP')
                                multiple
                            @endif
                        >
                            @if(auth()->user()->groupe != 'GE' && auth()->user()->groupe != 'SP')
                                <option value=""></option> <!-- Valeur nulle possible -->
                            @endif
                            @php
                                // Décoder JSON des entrepots sélectionnés si édition
                                $selectedEntrepots = isset($editUser) ? json_decode($editUser->entrepot_id ?? '[]', true) : [];
                            @endphp
                            @foreach($entrepots as $entrepot)
                                <option value="{{ $entrepot->id_entrepot }}"
                                    {{ in_array($entrepot->id_entrepot, $selectedEntrepots) ? 'selected' : '' }}>
                                    {{ $entrepot->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div align="right">
                    <a href="{{ route('users.index') }}" class="btn btn-outline-danger">Annuler</a>
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-save"></i> {{ isset($editUser) ? 'Mettre à jour' : 'Enregistrer' }}
                    </button>
                </div>
            </form>
        </div>

    </div>



    <div class="card shadow mb-4">
        <div class="card-header bg-primary py-3">
            <span class="m-0 font-weight-bold text-white ">Liste des utilisateurs</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="dataTable">
                    <thead class="bg-primary">
                    <tr class="text-white" >
                        <th>Photo</th>
                        <th>Utilisateurs</th>
                        <th>Login</th>
                        <th>Email</th>
                        <th>Telephone</th>
                        <th>Groupe</th>
                        <th>Entrepot</th>
                        <th>Depense</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>
                                @if ($user->photo)
                                    <img src="{{ asset('public/photos/' . $user->photo) }}" alt="Photo de {{ $user->personnel }}" width="50" height="50" class="rounded-circle">
                                @else
                                    <span>Aucune photo</span>
                                @endif
                            </td>
                            <td class="text-uppercase">{{ $user->personnel }}</td>
                            <td >{{ $user->username }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->telephone }}</td>
                            <td class="text-uppercase">
                                @switch($user->groupe)
                                    @case('SC') Commercial @break
                                    @case('SP') Superviseur @break
                                    @case('SA') Super Admin @break
                                    @case('FO') Fournisseur @break
                                    @case('CL') Client @break
                                    @case('GE') Gerant @break
                                    @case('DM') Client DMG @break
                                @endswitch
                            </td>
                            <td>
                                @if($user->entrepots->isEmpty())

                                @else
                                    <ul>
                                        @foreach($user->entrepots as $entrepot)
                                            <li>{{ $entrepot->nom }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </td>
                            <td>{{ $user->depense }}</td>
                            <td>
                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-outline-info">Modifier</a>
                                <form action="{{ route('users.destroy', $user->id) }}" method="post" class="d-inline">
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
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // Call the dataTables jQuery plugin
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



        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Sélectionnez un ou plusieurs entrepôts",
                allowClear: true,
                width: '100%'
            });
        });


    </script>
@endsection
