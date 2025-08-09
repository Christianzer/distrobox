@extends('layout')
@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item text-primary font-weight-bold text-uppercase active" aria-current="page">Gestion des Transactions du client {{$client->identifiant}}</li>
        </ol>
    </nav>

    <div class="mb-4 text-right">
        <button class="btn btn-outline-danger mt-1 text-uppercase font-weight-bold" data-toggle="modal" data-target="#manageTransactionModal">Ajouter une transaction pour {{$client->identifiant}}</button>
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



    <div class="card shadow mb-4">
        <div class="card-header bg-primary py-3 d-flex justify-content-between align-items-center">
            <span class="m-0 font-weight-bold text-white text-uppercase">Liste des Transactions de {{$client->identifiant}}</span>
            <button class="btn btn-light " onclick="location.reload()">
                <i class="fas fa-sync-alt"></i> Actualiser
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable">
                    <thead class="bg-primary">
                    <tr class="text-white">
                        <th>Date</th>
                        <th>Heure</th>
                        <th>Code transaction</th>
                        <th>Transactions</th>
                        <th>Montant</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>

                    @php

                        $typesTransactionsTableau = [
        'AU' => ['libelle' => 'Achat UV', 'couleur' => 'bg-success'],  // Vert
        'RU' => ['libelle' => 'Retour UV', 'couleur' => 'bg-danger'],   // Rouge
        'EC' => ['libelle' => 'Encaissement Achat UV', 'couleur' => 'bg-info'],  // Bleu clair
        'DC' => ['libelle' => 'Decaissement pour retour UV', 'couleur' => 'bg-warning'],  // Jaune
    ];
                        @endphp

                    @foreach($transactionClients as $transactionClient)

                        @php
                            $type = $transactionClient->type_transaction;
                            $couleur = isset($typesTransactionsTableau[$type]) ? $typesTransactionsTableau[$type]['couleur'] : 'bg-light';
                        @endphp
                        <tr class="font-weight-bold text-white {{$couleur}}">
                            <td>{{date('d-m-y',strtotime($transactionClient->date_transaction))}}</td>
                            <td>{{date('H:i:s',strtotime($transactionClient->date_transaction))}}</td>
                            <td>{{$transactionClient->code_transaction}}</td>
                            <td class="text-uppercase">{{$typesTransactionsTableau[$type]['libelle']}}</td>
                            <td>{{formatNumberPart($transactionClient->montant,$transactionClient->type_transaction,$transactionClient->statut)}}</td>
                            <td class="bg-white" align="center">
                                <button class="btn btn-outline-danger btn-sm text-center deleteTransactionBtn"
                                        data-toggle="modal"
                                        data-target="#clotureModal"
                                        data-code="{{ $transactionClient->code_transaction }}"
                                        data-type="{{ $typesTransactionsTableau[$type]['libelle'] }}"
                                        data-montant="{{ formatNumber($transactionClient->montant) }}"
                                        data-date="{{ date('d-m-Y H:i:s', strtotime($transactionClient->date_transaction)) }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>

                    <tfoot class="bg-primary">
                    <tr class="text-white font-weight-bold">
                        <td colspan="4" class="text-right">TOTAL</td>
                        <td colspan="2" class="text-left">{{formatNumber($totalAmount)}}</td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal de confirmation de suppression -->
    <div class="modal fade" id="clotureModal" tabindex="-1" aria-labelledby="clotureModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white text-uppercase font-weight-bold" id="clotureModalLabel">Confirmation de Suppression</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body font-weight-bold">
                    <p>Êtes-vous sûr de vouloir supprimer cette transaction ? Cette action est irréversible.</p>
                    <div id="transactionDetails" class="text-muted">
                        <p><strong>Date:</strong> <span id="transactionDate"></span></p>
                        <p><strong>Code Transaction:</strong> <span id="transactionCode"></span></p>
                        <p><strong>Montant:</strong> <span id="transactionAmount"></span></p>
                        <p><strong>Type de Transaction:</strong> <span id="transactionType"></span></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-danger" id="confirmCloture">
                        <i class="fas fa-trash"></i> Confirmer la suppression
                    </button>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="manageTransactionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <form id="manageTransactionForm" method="POST" action="{{route('transactions.store')}}">
            @csrf
            <input type="hidden" name="id" id="transactionId">
            <input type="hidden" name="code_client" value="{{$client->code}}" id="code_client">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title text-white">Ajouter ou Modifier une Transaction</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">

                            @php
                                $uv_disponible = $soldeJour->uv - $results['AU'] + $results['RU'];
                                $cash_disponible = $soldeJour->cash + $results['EC'] - $results['DC'];
                                $uv_disponible_retour = $transactionDifference->difference < 0 ? 0 : $transactionDifference->difference;
                            @endphp

                            <div class="form-group col-4">
                                <label for="transactionDateuv">CASH DISPONIBLE</label>
                                <input  type="text" readonly value="{{number_format($cash_disponible,'0',',',' ')}}" class="form-control font-weight-bold text-danger number-format" name="cash_disponible" id="cash_disponible">
                            </div>

                            <div class="form-group col-4">
                                <label for="transactionDatecASH">UV DISPONIBLE</label>
                                <input  type="text" readonly value="{{number_format($uv_disponible,'0',',',' ')}}" class="form-control font-weight-bold text-danger number-format" name="uv_disponible" id="uv_disponible">
                            </div>


                            <div class="form-group col-4">
                                <label for="transactionDaterETOUR">UV DU CLIENT {{$client->identifiant}}</label>
                                <input  type="text" readonly value="{{number_format($uv_disponible_retour,'0',',',' ')}}" class="form-control font-weight-bold text-danger number-format" name="uv_disponible_retour" id="uv_disponible_retour">
                            </div>

                        </div>
                        <div class="form-row">
                            <div class="form-group col-4">
                                <label for="transactionDate">Date et heure</label>
                                <input  type="datetime-local" readonly value="{{date('Y-m-d H:i:s')}}" class="form-control" name="date_transaction" id="transactionDate" required>
                            </div>

                            <div class="form-group col-8">
                                <label for="type">Type transaction</label>
                                <select class="form-control text-uppercase" name="type_transaction" id="type" required>
                                    @foreach($typesTransactions as $type)
                                        <option value="{{ $type['id'] }}">
                                            {{ $type['libelle'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                        <div class="form-row">
                            <div class="form-group col-4">
                                <label for="montant">Montant</label>
                                <input type="text" class="form-control number-format" name="montant" id="montant" required>
                            </div>
                            <div class="form-group col-4">
                                <label for="agent">Agent recouvreur</label>
                                <select class="form-control" name="agent" id="agent">
                                    <option value=""></option>
                                    @foreach($agents as $agent)
                                        <option value="{{ $agent->id }}">
                                            {{ $agent->personnel }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-4">
                                <label for="operateur">Opérateur</label>
                                <input type="text" class="form-control" name="operateur" id="operateur">
                            </div>
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



        $(document).ready(function() {


            $('.deleteTransactionBtn').on('click', function() {
                let transactionCode = $(this).data('code');
                let transactionType = $(this).data('type');
                let transactionMontant = $(this).data('montant');
                let transactionDate = $(this).data('date');

                // Mettre à jour les valeurs dans le modal
                $('#transactionCode').text(transactionCode);
                $('#transactionType').text(transactionType);
                $('#transactionAmount').text(transactionMontant);
                $('#transactionDate').text(transactionDate);

            });

            let uvDisponibleInitial = parseFloat($('#uv_disponible').val().replace(/\s+/g, '')) || 0;
            let CashDisponibleInitial = parseFloat($('#cash_disponible').val().replace(/\s+/g, '')) || 0;
            let RetourDisponibleInitial = parseFloat($('#uv_disponible_retour').val().replace(/\s+/g, '')) || 0;


            function updateUvDisponible() {
                let montant = parseFloat($('#montant').val().replace(/\s+/g, '')) || 0;
                let typeTransaction = $('#type').val();

                let uvCalcule = uvDisponibleInitial; // Commence avec la valeur initiale

                if (typeTransaction === 'AU') { // Achat UV => diminue UV disponible
                    if (montant > uvDisponibleInitial) {
                        alert("Le montant ne peut pas dépasser l'UV disponible !");
                        $('#montant').val(uvDisponibleInitial); // Remet le montant au maximum disponible
                        montant = uvDisponibleInitial;
                    }
                    uvCalcule -= montant;
                } else if (typeTransaction === 'RU') { // Retour UV => augmente UV disponible
                    uvCalcule += montant;
                }

                $('#uv_disponible').val(uvCalcule.toLocaleString()); // Met à jour le champ
            }

            function verifierRetour(){
                let montant = parseFloat($('#montant').val().replace(/\s+/g, '')) || 0;
                let typeTransaction = $('#type').val();
                let RetourCalcule = RetourDisponibleInitial;
                if (typeTransaction === 'RU') {
                    if (montant > RetourDisponibleInitial) {
                        alert("Le retour d'uv ne peut pas superieur pour l'uv disponible pour le client !");
                        $('#montant').val(RetourDisponibleInitial);
                        montant = RetourDisponibleInitial;
                    }
                    RetourCalcule -= montant;
                }else if(typeTransaction === 'AU'){
                    RetourCalcule += montant;
                }

                $('#uv_disponible_retour').val(RetourCalcule.toLocaleString()); // Met à jour le champ
            }

            function updateCashDisponible() {
                let montant = parseFloat($('#montant').val().replace(/\s+/g, '')) || 0;
                let typeTransaction = $('#type').val();
                let agent = $('#agent').val().trim();

                let CashCalcule = CashDisponibleInitial;

                if (typeTransaction === 'DC') {
                    if (montant > CashDisponibleInitial) {
                        alert("Le montant ne peut pas dépasser le cash disponible !");
                        $('#montant').val(CashDisponibleInitial);
                        montant = CashDisponibleInitial;
                    }
                    CashCalcule -= montant;
                } else if (typeTransaction === 'EC') {
                    if (agent === "") {
                        CashCalcule += montant;
                    }
                }

                $('#cash_disponible').val(CashCalcule.toLocaleString()); // Met à jour le champ
            }



            $('#type').on('change', function() {
                updateUvDisponible();
                updateCashDisponible();
                verifierRetour();
            });

            $('#montant').on('input', function() {
                updateUvDisponible();
                updateCashDisponible();
                verifierRetour();
            });


            $('#agent').on('change', function() {
                updateCashDisponible();
            });



            function formatNumberWithSpaces(number) {
                return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
            }

            // Supprimer les espaces pour récupérer la vraie valeur
            function unformatNumber(number) {
                return parseFloat(number.replace(/\s+/g, '')) || 0;
            }

            $('.number-format').on('input', function () {
                const unformattedValue = $(this).val().replace(/\s+/g, '');
                $(this).val(formatNumberWithSpaces(unformattedValue));
            });


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
