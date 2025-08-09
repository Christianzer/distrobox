@extends('layout')
@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item text-primary font-weight-bold text-uppercase active" aria-current="page">Base de données</li>
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
            <span class="m-0 font-weight-bold text-white">Liste des données</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="bg-primary">
                    <tr class="text-white text-uppercase">
                        <th>Tables</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($tables as $table)
                        <tr>
                            <td class="text-uppercase">{{$table['libelle']}}</td>
                            <td>
                                <button data-toggle="modal" data-target="#confirmModal" type="button" data-code="{{$table['id']}}" class="btn btn-outline-danger">Vider les informations</button>
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{route('bd.vider')}}" method="post">
                    @csrf
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir vider les informations de cette table ? Cette action est irréversible.</p>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="code" id="code">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                        <button type="submit" id="confirmDelete" class="btn btn-danger">Vider</button>
                    </div>
                </form>
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

            let tableId = null;

            $('button[data-target="#confirmModal"]').on('click', function() {
                $("#code").val($(this).data('code'));
            });


        });
    </script>
@endsection
