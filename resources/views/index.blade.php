@extends('layout')
@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item text-primary font-weight-bold text-uppercase active" aria-current="page">Gestion des transactions</li>
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




    @if(!isset($soldeJour))
        <div class="d-flex justify-content-center align-items-center" style="height: 50vh;">
    <span class="badge badge-danger text-center">
        <h2>VEUILLEZ RENSEIGNER LE SOLDE DU JOUR !!!!!!!!!!!!!!!!!!</h2>
    </span>
        </div>
    @else
        <style>
            .card-body .text-lg { font-size: 14px; } /* Texte plus petit */
            .card-body .h5 { font-size: 14px; } /* Taille ajustée pour une meilleure lisibilité */
            .negative-amount {
                background-color: #f8d7da; /* Rouge clair */
            }

            .positive-amount {
                background-color: #d4edda; /* Vert clair */
            }
        </style>


        <div class="row">
            <!-- Earnings (Monthly) Card Example -->

            <div class="col-xl-6 col-md-8 mb-4">
                <div class="row">
                    <div class="col-md-4">
                        <label for="date_filter" class="form-label text-uppercase font-weight-bold text-danger">Sélectionnez une date :</label>
                        <input type="date" class="form-control" id="date_filter">
                    </div>
                    <div class="col-md-3 d-flex align-items-end mt-2">
                        <button id="filter_btn" class="btn btn-primary w-80"> <i class="fas fa-sync-alt"></i> Actualiser</button>
                    </div>
                </div>
            </div>


            <div class="col-xl-6 col-md-4 mb-4">
                <!-- Button Section -->

                <table class="font-weight-bold" width="100%" style="font-size: 13px">
                    <tr class="bg-danger text-white font-weight-bold text-center">
                        <td colspan="5">SOLDE</td>
                    </tr>
                    <tr class="text-white font-weight-bold text-center">
                        <td width="48%" class="bg-primary" colspan="2">DEPART</td>
                        <td width="4%"></td>
                        <td width="48%" class="bg-success" colspan="2">EN COURS</td>
                    </tr>
                    <tr>
                        <td width="15%">CASH</td>
                        <td width="33%" class="text-right" id="cash_depart">0</td>
                        <td width="4%"> </td>
                        <td width="15%">CASH</td>
                        <td width="33%" class="text-right" id="cash_encours">0</td>
                    </tr>
                    <tr>
                        <td width="15%">UV MOOV</td>
                        <td width="33%" class="text-right" id="uv_moov_depart">0</td>
                        <td width="4%"> </td>
                        <td width="15%">UV MOOV</td>
                        <td width="33%" class="text-right" id="uv_moov_encours">0</td>
                    </tr>
                    <tr>
                        <td width="15%">UV DJAMO</td>
                        <td width="33%" class="text-right" id="uv_djamo_depart">0</td>
                        <td width="4%"> </td>
                        <td width="15%">UV DJAMO</td>
                        <td width="33%" class="text-right" id="uv_djamo_encours">0</td>
                    </tr>
                    <tr>
                        <td width="15%">UV MTN</td>
                        <td width="33%" class="text-right" id="uv_mtn_depart">0</td>
                        <td width="4%"> </td>
                        <td width="15%">UV MTN</td>
                        <td width="33%" class="text-right" id="uv_mtn_encours">0</td>
                    </tr>
                    <tr>
                        <td width="15%">UV ORANGE</td>
                        <td width="33%" class="text-right" id="uv_orange_depart">0</td>
                        <td width="4%"> </td>
                        <td width="15%">UV ORANGE</td>
                        <td width="33%" class="text-right" id="uv_orange_encours">0</td>
                    </tr>
                    <tr>
                        <td width="15%">UV PUSH</td>
                        <td width="33%" class="text-right" id="uv_push_depart">0</td>
                        <td width="4%"> </td>
                        <td width="15%">UV PUSH</td>
                        <td width="33%" class="text-right" id="uv_push_encours">0</td>
                    </tr>
                    <tr>
                        <td width="15%">UV TRESOR </td>
                        <td width="33%" class="text-right" id="uv_trmo_depart">0</td>
                        <td width="4%"> </td>
                        <td width="15%">UV TRESOR </td>
                        <td width="33%" class="text-right" id="uv_trmo_encours">0</td>
                    </tr>
                    <tr>
                        <td width="15%">UV WAVE</td>
                        <td width="33%" class="text-right" id="uv_wave_depart">0</td>
                        <td width="4%"> </td>
                        <td width="15%">UV WAVE</td>
                        <td width="33%" class="text-right" id="uv_wave_encours">0</td>
                    </tr>
                    <tr>
                        <td width="15%">DETTE</td>
                        <td width="33%" class="text-right" id="dette_depart">0</td>
                        <td width="4%"> </td>
                        <td width="15%">DETTE</td>
                        <td width="33%" class="text-right" id="dette_encours">0</td>
                    </tr>
                    <tr>
                        <td width="15%">AVOIR</td>
                        <td width="33%" class="text-right" id="avoir_depart">0</td>
                        <td width="4%"> </td>
                        <td width="15%">AVOIR</td>
                        <td width="33%" class="text-right" id="avoir_encours">0</td>
                    </tr>
                    <tr class="bg-secondary text-white font-weight-bold text-center">
                        <td>Total</td>
                        <td class="text-right" id="total_depart"></td>
                        <td></td>
                        <td></td>
                        <td class="text-right" id="total_encours"></td>
                        <td> </td>
                    </tr>

                </table>

                <div class="d-flex justify-content-start mb-3 mt-3">
                    <a href="{{route(('transactions.point'))}}" class="btn btn-warning" target="_blank" id="imprimerButton">Imprimer Point du Jour</a>
                </div>

            </div>






        </div>




        <div class="table-responsive">
            <div class="d-flex justify-content-start gap-2 mb-2 mr-2">
                @if(!in_array($typesUsers, ['SC']))
                    <button class="btn btn-outline-primary w-auto mr-3" data-toggle="modal" data-target="#manageTransactionModalUV">
                        <i class="fas fa-university"></i> UV
                    </button>
                @endif
                @if(!in_array($typesUsers, ['SUW','SUO','SU']))
                    <button class="btn btn-outline-success w-auto mr-3" data-toggle="modal" data-target="#manageTransactionModalCash">
                        <i class="fas fa-money-bill-wave"></i> CASH
                    </button>
                @endif

            </div>


            <table class="table table-bordered" id="transactions_table" style="font-size: 14px">
                <thead class="bg-primary">
                <tr class="text-white">
                    <th>Heure</th>
                    <th>Client</th>
                    <th>Transaction</th>
                    <th class="text-center">Montant</th>
                    <th class="text-center">Operateur</th>
                    <th>Affiliation</th>
                    <th>Agent</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <!-- Les données des transactions seront injectées ici par DataTables -->
                </tbody>
                <tfoot>
                <tr>
                    <th colspan="7" class="text-right text-uppercase">Total</th>
                    <th id="total_amount" class="font-weight-bold text-danger"></th>
                </tr>
                </tfoot>
            </table>


        </div>




    @endif


    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <form action="{{route('transactions.delete')}}" method="post">
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
                        <input type="hidden" name="code_transaction" id="code_transaction">
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


    <div class="modal fade" id="manageTransactionModalUV" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <form id="manageTransactionForm" method="POST" action="{{route('transactions.store')}}">
            @csrf
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title text-white">Nouvelle transaction</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-4">
                                <label for="transactionDate">Date et heure</label>
                                <input type="datetime-local" readonly value="{{ date('Y-m-d H:i:s') }}" class="form-control" name="date_transaction" id="transactionDate" required>
                            </div>
                            <div class="form-group col-8 d-flex align-items-end">
                                <div class="w-100">
                                    <label for="type">Client</label>
                                    <div class="input-group">
                                        <input type="text" name="client" id="client" class="form-control" required readonly value="">
                                        <input type="hidden" name="code_client" id="code_client" class="form-control" value="">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#clientSelectionModal">Choisir</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-8">
                                <label for="type">Type transaction</label>
                                <select class="form-control text-uppercase" name="type_transaction" id="type" required>
                                    @foreach($typesUV as $type)
                                        <option value="{{ $type['id'] }}">
                                            {{ $type['libelle'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
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
                                <select class="form-control" name="operateur" id="operateur">
                                    @foreach($operateurs as $operateur)
                                        <option value="{{ $operateur['id'] }}">
                                            {{ $operateur['libelle'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-4">
                                <label for="montant">Affiliation</label>
                                <input type="text" class="form-control" name="affiliation" id="affiliation">
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



    <div class="modal fade" id="manageTransactionModalCash" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <form id="manageTransactionForm" method="POST" action="{{route('transactions.store')}}">
            @csrf
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title text-white">Nouvelle transaction</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-4">
                                <label for="transactionDate">Date et heure</label>
                                <input type="datetime-local" readonly value="{{ date('Y-m-d H:i:s') }}" class="form-control" name="date_transaction" id="transactionDate" required>
                            </div>
                            <div class="form-group col-8 d-flex align-items-end">
                                <div class="w-100">
                                    <label for="type">Client</label>
                                    <div class="input-group">
                                        <input type="text" name="client" id="client_cash" class="form-control" required readonly value="">
                                        <input type="hidden" name="code_client" id="code_client_cash" class="form-control" value="">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#clientSelectionModal">Choisir</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-8">
                                <label for="type">Type transaction</label>
                                <select class="form-control text-uppercase" name="type_transaction" id="type" required>
                                    @foreach($typesCASH as $type)
                                        <option value="{{ $type['id'] }}">
                                            {{ $type['libelle'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
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
                                <select class="form-control" name="operateur" id="operateur">
                                    @foreach($operateurs as $operateur)
                                        <option value="{{ $operateur['id'] }}">
                                            {{ $operateur['libelle'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-4">
                                <label for="montant">Affiliation</label>
                                <input type="text" class="form-control" name="affiliation" id="affiliation">
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


    <div class="modal fade" id="clientSelectionModal" tabindex="-1" aria-labelledby="clientModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white">Choisir le client</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="dataTablePerso">
                                <thead class="bg-primary">
                                <tr class="text-white">
                                    <th>Identifiant</th>
                                    <th>Raison sociale</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($clients as $client)
                                    <tr>
                                        <td>{{ $client->identifiant }}</td>
                                        <td>{{ $client->raison_sociale }}</td>
                                        <td><button type="button" class="btn btn-sm btn-primary select-client" data-name="{{ $client->identifiant }}" data-code="{{ $client->code }}">Sélectionner</button></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection

@section('js')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let today = new Date().toISOString().split('T')[0];
            document.getElementById("date_filter").value = today;

            const operatorMapping = {
                'moov': 'Moov',
                'orange': 'Orange',
                'mtn': 'MTN',
                'wave': 'Wave',
                'djamo': 'Djamo',
                'push': 'Push',
                'trmo': 'Tresor Monney'
            };

            function updateSoldes() {
                $.ajax({
                    url: "{{ route('transactions.soldes') }}",
                    type: "GET",
                    data: { date: $('#date_filter').val() }, // Prend la date du filtre
                    success: function(response) {
                        $('#cash_depart').text(response.cash_depart);
                        $('#cash_encours').text(response.cash_encours);

                        $('#uv_moov_depart').text(response.uv_moov_depart);
                        $('#uv_moov_encours').text(response.uv_moov_encours);

                        $('#dette_depart').text(response.dette_depart);
                        $('#dette_encours').text(response.dette_encours);

                        $('#avoir_depart').text(response.avoir_depart);
                        $('#avoir_encours').text(response.avoir_encours);

                        $('#uv_djamo_depart').text(response.uv_djamo_depart);
                        $('#uv_djamo_encours').text(response.uv_djamo_encours);

                        $('#uv_mtn_depart').text(response.uv_mtn_depart);
                        $('#uv_mtn_encours').text(response.uv_mtn_encours);

                        $('#uv_orange_depart').text(response.uv_orange_depart);
                        $('#uv_orange_encours').text(response.uv_orange_encours);

                        $('#uv_push_depart').text(response.uv_push_depart);
                        $('#uv_push_encours').text(response.uv_push_encours);

                        $('#uv_trmo_depart').text(response.uv_trmo_depart);
                        $('#uv_trmo_encours').text(response.uv_trmo_encours);

                        $('#uv_wave_depart').text(response.uv_wave_depart);
                        $('#uv_wave_encours').text(response.uv_wave_encours);

                        $('#total_depart').text(response.total_depart);
                        $('#total_encours').text(response.total_encours);

                    }
                });
            }

            // Fonction pour formater les nombres avec un séparateur de millier
            function formatNumber(number) {
                // Arrondir le nombre à l'entier le plus proche
                return Math.round(number).toLocaleString('fr-FR');
            }

            // Fonction pour formater les nombres avec un séparateur de millier


            function formatDate(dateString) {
                let date = new Date(dateString);
                let day = ("0" + date.getDate()).slice(-2);
                let month = ("0" + (date.getMonth() + 1)).slice(-2);
                let year = date.getFullYear();
                let hours = ("0" + date.getHours()).slice(-2);
                let minutes = ("0" + date.getMinutes()).slice(-2);
                let seconds = ("0" + date.getSeconds()).slice(-2);
                return `${day}-${month}-${year} ${hours}:${minutes}:${seconds}`;
            }

            let table = $('#transactions_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('transactions.data') }}",
                    data: function(d) {
                        d.date = $('#date_filter').val(); // Envoi de la date
                    },
                    dataSrc: function(json) {
                        // Mise à jour du total avec formatage des milliers
                        $('#total_amount').html(formatNumber(json.total));

                        // Retourner les données à afficher dans DataTables
                        return json.data.map(function(item) {
                            // Formater les montants avant de les renvoyer à DataTables
                            item.montant = formatNumber(item.montant);
                            item.date_transaction = formatDate(item.date_transaction);

                            if (item.transaction === 'AU') {
                                item.transaction = 'Achat UV';
                            } else if (item.transaction === 'RU') {
                                item.transaction = 'Retour UV';
                            } else if (item.transaction === 'EC') {
                                item.transaction = 'Encaissement Achat UV';
                            } else if (item.transaction === 'DC') {
                                item.transaction = 'Decaissement Retour UV';
                            }
                            else if (item.transaction === 'DP') {
                                item.transaction = 'Dépot';
                            }
                            else if (item.transaction === 'RE') {
                                item.transaction = 'Retrait';
                            }



                            if (operatorMapping[item.operateur]) {
                                item.operateur = operatorMapping[item.operateur];
                            }



                            return item;
                        });
                    }
                },
                lengthMenu: [10], // Définit la pagination à 10 éléments par page, mais supprime le menu déroulant
                pageLength: 10,   // Nombre d'éléments par page
                searching: true,  // Activer la recherche
                paging: true,     // Afficher la pagination
                info: false,      // Masquer l'info de pagination (par exemple "1 à 10 sur 100")
                dom: '<"top"f>rt<"bottom"p><"clear">',
                columns: [
                    { data: 'date_transaction', name: 'date_transaction' ,searchable: false},
                    { data: 'client', name: 'client' , searchable: false },
                    { data: 'transaction', name: 'transaction' , searchable: false },
                    { data: 'montant', name: 'montant' , searchable: false },
                    { data: 'operateur', name: 'operateur' , searchable: false },
                    { data: 'affiliation', name: 'affiliation' , searchable: false },
                    { data: 'agent', name: 'agent' , searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                columnDefs: [
                    {
                        targets: 3, // 3 est l'index de la colonne Montant
                        className: 'text-right' // Alignement à droite pour la colonne Montant
                    }
                ],
                language: {
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

            setInterval(updateSoldes, 60000);
            updateSoldes();

            // Filtrer lorsque l'utilisateur clique sur "Filtrer"
            $('#filter_btn').on('click', function() {
                table.ajax.reload();
                updateSoldes();
            });



            $('#dataTablePerso').DataTable({
                "ordering": false,
                "lengthMenu": [5], // Définit la pagination à 10 éléments par page, mais supprime le menu déroulant
                "pageLength": 5,   // Nombre d'éléments par page
                "searching": true,  // Activer la recherche
                "paging": true,     // Afficher la pagination
                "info": false,      // Masquer l'info de pagination (par exemple "1 à 10 sur 100")
                "dom": '<"top"f>rt<"bottom"p><"clear">',
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


            $(document).on("click", ".select-client", function() {
                let clientName = $(this).data("name");
                let clientCode = $(this).data("code");

                $("#client").val(clientName);
                $("#code_client").val(clientCode);
                
                 $("#client_cash").val(clientName);
                $("#code_client_cash").val(clientCode);

                // Fermer la modale après la sélection
                $("#clientSelectionModal").modal("hide");
            });


            $(document).on("click", ".recu-btn", function() {
                let code_transaction = $(this).data("code");
                let baseUrl = "{{ route('transactions.recu', ':code') }}";
                let url = baseUrl.replace(':code', code_transaction);
                window.open(url, '_blank');
            });

            $(document).on("click", ".delete-btn", function() {
                let code_transaction = $(this).data("code");
                $("#code_transaction").val(code_transaction);
                $("#deleteModal").modal("show");
            });

        });






    </script>
@endsection
