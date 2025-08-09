@extends('layout')
@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item text-primary font-weight-bold text-uppercase active" aria-current="page">Gestion des Soldes</li>
        </ol>
    </nav>

    <div class="mb-4 text-right">
        @if(!$transactionsExist)
            <button class="btn btn-primary mt-1" data-toggle="modal" data-target="#manageSoldeModal">Ajouter le solde du jour</button>
        @endif

    </div>

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

    <style>
        .card-body .text-lg { font-size: 14px; } /* Texte plus petit */
        .card-body .h5 { font-size: 14px; } /* Taille ajustée pour une meilleure lisibilité */
    </style>

    @if(!isset($soldeJour))
        <div class="d-flex justify-content-center align-items-center" style="height: 50vh;">
            <span class="badge badge-danger text-center">
                <h2>VEUILLEZ RENSEIGNER LE SOLDE DU JOUR !!!!!!!!!!!!!!!!!!</h2>
            </span>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered" id="dataTable" style="font-size: 13px">
            <thead class="bg-primary">
            <tr class="text-white text-center">
                <th rowspan="2">Date</th>
                <th rowspan="2">Cash</th>
                <th colspan="5">UV</th>
                <th rowspan="2">Dette</th>
                <th rowspan="2">Avoir</th>
                <th rowspan="2">Total</th>
                <th rowspan="2">Action</th>
            </tr>
            <tr class="text-white text-center">
                <th>Orange</th>
                <th>Wave</th>
                <th>Djamo</th>
                <th>Push</th>
                <th>Trésor Monney</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($ToutSolde as $solde)
                @php
                    $donnees = $solde['donnees'];
                    $total_encours = intval(str_replace(' ', '', $donnees['cash_encours'])) +

                                     intval(str_replace(' ', '', $donnees['uv_orange_encours'])) +

                                     intval(str_replace(' ', '', $donnees['uv_wave_encours'])) +
                                     intval(str_replace(' ', '', $donnees['uv_djamo_encours'])) +
                                     intval(str_replace(' ', '', $donnees['uv_push_encours'])) +
                                     intval(str_replace(' ', '', $donnees['uv_trmo_encours'])) +
                                     intval(str_replace(' ', '', $donnees['dette_encours'])) +
                                     intval(str_replace(' ', '', $donnees['avoir_encours']));

                    $total_depart = intval(str_replace(' ', '', $donnees['cash_depart'])) +

                                    intval(str_replace(' ', '', $donnees['uv_orange_depart'])) +

                                    intval(str_replace(' ', '', $donnees['uv_wave_depart'])) +
                                    intval(str_replace(' ', '', $donnees['uv_djamo_depart'])) +
                                    intval(str_replace(' ', '', $donnees['uv_push_depart'])) +
                                    intval(str_replace(' ', '', $donnees['uv_trmo_depart'])) +
                                    intval(str_replace(' ', '', $donnees['dette_depart'])) +
                                    intval(str_replace(' ', '', $donnees['avoir_depart']));
                @endphp


                    <!-- Ligne Encours -->



                <!-- Ligne Départ -->
                <tr>
                    <td rowspan="2" class="{{$solde['cloturer'] == 1 ? 'bg-success text-white font-weight-bold' : ''}}" style="align-content: center">{{ date('d-m-Y', strtotime($solde['date_solde'])) }}</td>
                    <td>{{ number_format(intval(str_replace(' ', '', $donnees['cash_depart'])), 0, ',', ' ') }}</td>

                    <td>{{ number_format(intval(str_replace(' ', '', $donnees['uv_orange_depart'])), 0, ',', ' ') }}</td>

                    <td>{{ number_format(intval(str_replace(' ', '', $donnees['uv_wave_depart'])), 0, ',', ' ') }}</td>
                    <td>{{ number_format(intval(str_replace(' ', '', $donnees['uv_djamo_depart'])), 0, ',', ' ') }}</td>
                    <td>{{ number_format(intval(str_replace(' ', '', $donnees['uv_push_depart'])), 0, ',', ' ') }}</td>
                    <td>{{ number_format(intval(str_replace(' ', '', $donnees['uv_trmo_depart'])), 0, ',', ' ') }}</td>
                    <td>{{ number_format(intval(str_replace(' ', '', $donnees['dette_depart'])), 0, ',', ' ') }}</td>
                    <td>{{ number_format(intval(str_replace(' ', '', $donnees['avoir_depart'])), 0, ',', ' ') }}</td>
                    <td>{{ number_format($total_depart, 0, ',', ' ') }}</td>

                    <td rowspan="2" style="align-content: center">
                        @if(!TransactionsExists($solde['date_solde']))
                            <button class="btn btn-warning btn-sm editSolde" data-id="{{ $solde['id_solde'] }}"
                                    data-date="{{ $solde['date_solde'] }}" data-cash="{{ $donnees['cash_depart'] }}"
                                    @foreach($operateurs as $operateur)
                                        data-uv_{{ $operateur['id'] }}="{{ $donnees['uv_'.$operateur['id'].'_depart'] }}"
                                    @endforeach
                                    data-dette="{{ $donnees['dette_depart'] }}" data-avoir="{{ $donnees['avoir_depart'] }}">
                                Modifier
                            </button>
                            <br>
                            <button class="btn btn-danger btn-sm deleteSolde mt-1" data-id="{{$solde['date_solde'] }}">Supprimer</button>
                            <button style="align-content: center" class="btn btn-primary mt-1 btn-sm CloturerSolde" data-id="{{$solde['date_solde'] }}">Cloturer</button>
                        @else
                            @if($solde['cloturer'] == 1)
                                <button class="btn btn-danger btn-sm deleteSolde mt-1" data-id="{{$solde['date_solde'] }}">Supprimer</button>
                            @else
                                <button style="align-content: center" class="btn btn-primary mt-1 btn-sm CloturerSolde" data-id="{{$solde['date_solde'] }}">Cloturer</button>
                            @endif

                        @endif

                    </td>
                </tr>

                <tr class="bg-danger text-white font-weight-bold">

                    <td>{{ number_format(intval(str_replace(' ', '', $donnees['cash_encours'])), 0, ',', ' ') }}</td>

                    <td>{{ number_format(intval(str_replace(' ', '', $donnees['uv_orange_encours'])), 0, ',', ' ') }}</td>

                    <td>{{ number_format(intval(str_replace(' ', '', $donnees['uv_wave_encours'])), 0, ',', ' ') }}</td>
                    <td>{{ number_format(intval(str_replace(' ', '', $donnees['uv_djamo_encours'])), 0, ',', ' ') }}</td>
                    <td>{{ number_format(intval(str_replace(' ', '', $donnees['uv_push_encours'])), 0, ',', ' ') }}</td>
                    <td>{{ number_format(intval(str_replace(' ', '', $donnees['uv_trmo_encours'])), 0, ',', ' ') }}</td>
                    <td>{{ number_format(intval(str_replace(' ', '', $donnees['dette_encours'])), 0, ',', ' ') }}</td>
                    <td>{{ number_format(intval(str_replace(' ', '', $donnees['avoir_encours'])), 0, ',', ' ') }}</td>
                    <td>{{ number_format($total_encours, 0, ',', ' ') }}</td>
                </tr>



            @endforeach
            </tbody>
        </table>
    </div>



    <div class="modal fade" id="clotureModal" tabindex="-1" aria-labelledby="clotureModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white text-uppercase font-weight-bold" id="clotureModalLabel">Confirmation de Clôture</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-black font-weight-bold">
                    Êtes-vous sûr de vouloir clôturer la journée ? Cette action est irréversible.
                </div>
                <form action="{{route('soldes.cloturer')}}" method="post">
                    @csrf
                    <div class="modal-footer">
                        <input type="hidden" name="soldeId" id="soldeIdClo">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success" >
                            <i class="fas fa-check"></i> Confirmer
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>








    <!-- Modal de confirmation de suppression -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <form action="{{route('soldes.delete')}}" method="post">
            @csrf
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Confirmation de suppression</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="soldeId" id="soldeIdDel">
                        Êtes-vous sûr de vouloir supprimer cet élément ?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                    </div>
                </div>
            </div>
        </form>

    </div>


    <div class="modal fade" id="manageSoldeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <form id="manageSoldeForm" method="POST" action="{{ route('soldes.store') }}">
            @csrf
            <input type="hidden" name="id" id="soldeId">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title text-white" id="exampleModalLabel">Ajouter ou Modifier un Solde</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">
                            <input type="hidden" name="soldeId" id="soldeId">
                            <div class="form-group col-3">
                                <label for="soldeDate">Date</label>
                                <input type="date" value="{{ date('Y-m-d') }}" class="form-control" name="date_solde" id="soldeDate" required>
                            </div>
                            <div class="form-group col-3">
                                <label for="cash">Cash</label>
                                <input type="text" class="form-control number-format" name="cash" id="cash" required>
                            </div>
                            @foreach($operateurs as $operateur)
                                <div class="form-group col-3">
                                    <label for="uv">UV {{ $operateur['libelle'] }}</label>
                                    <input type="text" class="form-control number-format" name="uv_{{ $operateur['id'] }}" id="uv_{{ $operateur['id'] }}" required>
                                </div>
                            @endforeach
                            <div class="form-group col-3">
                                <label for="dette">Dette</label>
                                <input type="text" class="form-control number-format" name="dette" id="dette" required>
                            </div>
                            <div class="form-group col-3">
                                <label for="avoir">Avoir</label>
                                <input type="text" class="form-control number-format" name="avoir" id="avoir">
                            </div>
                            <div class="form-group col-3">
                                <label for="total">Total</label>
                                <input type="text" class="form-control font-weight-bold text-danger" name="total" id="total" readonly>
                            </div>
                        </div>
                        <div id="errorFeedback" class="text-danger" style="display: none;">
                            Une transaction ou un solde existe déjà pour cette date.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-danger" type="button" data-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function () {



            // Fonction pour ajouter des séparateurs de milliers
            function formatNumberWithSpaces(number) {
                return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
            }

            // Supprimer les espaces pour récupérer la vraie valeur
            function unformatNumber(number) {
                return parseFloat(number.replace(/\s+/g, '')) || 0;
            }

            // Recalcul du total en soustrayant la dette
            function recalculateTotal() {
                let total = 0;
                $('.number-format').each(function () {
                    let fieldId = $(this).attr('id');
                    let value = unformatNumber($(this).val());
                    total += value;
                });
                $('#total').val(formatNumberWithSpaces(total.toFixed(0)));
            }

            // Ajout du formatage en temps réel sur les champs numériques
            $('.number-format').on('input', function () {
                let unformattedValue = $(this).val().replace(/\s+/g, '');
                $(this).val(formatNumberWithSpaces(unformattedValue));
                recalculateTotal();
            });

            // Préparer les champs pour le backend lors de l'envoi
            $('#manageSoldeForm').on('submit', function () {
                $('.number-format').each(function () {
                    let unformattedValue = unformatNumber($(this).val());
                    $(this).val(unformattedValue);
                });
            });

            // Gestion du bouton "Modifier"
            $('body').on('click', '.editSolde', function () {
                $('#soldeId').val($(this).data('id'));
                $('#soldeDate').val($(this).data('date'));
                $('#cash').val(formatNumberWithSpaces($(this).data('cash')));

                @foreach($operateurs as $operateur)
                $('#uv_{{ $operateur['id'] }}').val(formatNumberWithSpaces($(this).data('uv_{{ $operateur['id'] }}')));
                @endforeach

                $('#dette').val(formatNumberWithSpaces($(this).data('dette')));
                $('#avoir').val(formatNumberWithSpaces($(this).data('avoir')));
                recalculateTotal();

                $('#manageSoldeModal').modal('show');
            });


            $('.deleteSolde').click(function() {
                // Récupérer l'ID de l'élément à supprimer
                $('#soldeIdDel').val($(this).data('id'));
                $('#deleteModal').modal('show');
            });


            $('.CloturerSolde').click(function() {
                // Récupérer l'ID de l'élément à supprimer
                $('#soldeIdClo').val($(this).data('id'));
                $('#clotureModal').modal('show');
            });


            // Initialisation de DataTable
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
