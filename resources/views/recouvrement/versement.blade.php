@extends('layout')
@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item text-primary font-weight-bold text-uppercase active" aria-current="page">Versement Agent</li>
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

    <style>
        .bg-orange {
            background-color: #ff9800 !important; /* Orange */
        }

        .bg-blue {
            background-color: #0077ff !important; /* Wave */
        }

        .bg-purple {
            background-color: #6f42c1 !important; /* Djamo */
        }

        .bg-green {
            background-color: #28a745 !important; /* Push */
        }
    </style>


    @if(isset($transactions))

        @php


            $transactionColors = [
'AU' => 'bg-success text-white font-weight-bold', // Achat UV
'RU' => 'bg-warning text-dark font-weight-bold', // Retour UV
'EC' => 'bg-primary text-white font-weight-bold', // Encaissement Achat UV
'DC' => 'bg-danger text-white font-weight-bold', // Decaissement Retour UV
'DP' => 'bg-info text-white', // Dépôt
'RE' => 'bg-secondary text-white' // Retrait
];


    $operatorColors = [
'orange' => 'bg-orange text-white text-uppercase font-weight-bold',
'wave' => 'bg-blue text-white text-uppercase font-weight-bold',
'djamo' => 'bg-dark text-white text-uppercase font-weight-bold',
'push' => 'bg-green text-white text-uppercase font-weight-bold',
'trmo' => 'bg-purple text-white text-uppercase font-weight-bold'
];

$typesTransactionsTableau = [
'AU' => ['libelle' => 'Achat UV', 'couleur' => 'bg-success'],  // Vert
'RU' => ['libelle' => 'Retour UV', 'couleur' => 'bg-danger'],   // Rouge
'EC' => ['libelle' => 'Encaissement Achat UV', 'couleur' => 'bg-info'],  // Bleu clair
'DC' => ['libelle' => 'Decaissement pour retour UV', 'couleur' => 'bg-warning'],  // Jaune
];
        @endphp
        <div class="card shadow mb-4">
            <div class="card-header bg-primary py-3">
                <span class="m-0 font-weight-bold text-white text-uppercase">BORDEREAU DE VERSEMENT N° {{$code}}</span>
            </div>
            <div class="card-body">

                <table id="paiementTable" class="table table-bordered">
                    <thead class="bg-primary">
                    <tr class="text-white font-weight-bold">
                        <th>Date transaction</th>
                        <th>Client</th>
                        <th>Type transaction</th>
                        <th>Operateur</th>
                        <th>Montant</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>


                    @php
                        $total = 0;
                            $operatorMapping = [
    'moov' => 'Moov',
    'orange' => 'Orange',
    'mtn' => 'MTN',
    'wave' => 'Wave',
    'djamo' => 'Djamo',
    'push' => 'Push',
    'trmo' => 'Tresor Monney'
    ];


                    @endphp

                    @foreach($transactions as $transaction)


                        @php

                            if ($transaction->type_transaction == 'EC'){
                                $montantT = $transaction->montant;
                            }else{
                                $montantT =  - $transaction->montant;
                            }

                                $transactionClass = $transactionColors[$transaction->type_transaction] ?? '';
                                       $operatorClass = $operatorColors[$transaction->operateur] ?? '';
                                       $total += $montantT

                        @endphp


                        <tr data-saved="{{ $transaction->is_saved ? 'true' : 'false' }}">
                            <td>{{date('d-m-Y H-i-s',strtotime($transaction->date_transaction))}}</td>
                            <td>{{$transaction->raison_sociale}}</td>
                            <td class="{{$transactionClass}}" >{{ $typesTransactionsTableau[$transaction->type_transaction]['libelle'] }}</td>
                            <td class="{{ $operatorClass }}">{{ $operatorMapping[$transaction->operateur] }}</td>
                            <td>{{formatNumberType($transaction->montant,$transaction->type_transaction) }}</td>

                            <td>
                                @if($bordereauxInfo->statut == 0)
                                    <button type="button" class="btn btn-danger btn-sm delete-btn" data-code="{{$transaction->code_transaction}}">Supprimer</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                    <tfoot>
                    <tr class="text-white bg-primary">
                        <td colspan="4" class="text-right font-weight-bold">TOTAL</td>
                        <td class="font-weight-bold text-left">{{formatNumber($total)}}</td>
                    </tr>
                    </tfoot>
                </table>

                <div align="right">
                    <a href="{{route('recouvrements.imprimer',$code)}}" target="_blank" class="btn btn-outline-primary mr-1">Imprimer</a>
                    <a href="{{route('recouvrements.versement')}}" class="btn btn-outline-danger mr-1">Annuler</a>
                    @if(auth()->user()->groupe == 'SA' || auth()->user()->groupe == 'A' || auth()->user()->groupe == 'SC')
                        @if($bordereauxInfo->statut == 0)
                            <form action="{{route('validation',$code)}}" method="post" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" id="valider-bordereau" class="btn btn-outline-success mr-1">
                                    <i class="fas fa-save"></i> Valider le bordereau
                                </button>
                            </form>
                        @endif
                    @endif

                </div>


            </div>
        </div>
    @endif


    <div class="card shadow mb-4">
        <div class="card-header bg-primary py-3 d-flex justify-content-between align-items-center">
            <span class="m-0 font-weight-bold text-white">Liste des bordereaux</span>
            <button class="btn btn-light " onclick="location.reload()">
                <i class="fas fa-sync-alt"></i> Actualiser
            </button>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="dataTable">
                    <thead class="bg-primary">
                    <tr class="text-white">
                        <th>Date</th>
                        <th>Heure</th>
                        <th>Code</th>
                        <th>Agent</th>
                        <th>Validé par</th>
                        <th>Statut</th>
                        <th>Statut</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($bordereaux as $bordereau)
                        <tr>
                            <td>{{date('d-m-y',strtotime($bordereau->date_bordereau))}}</td>
                            <td>{{date('h:i:s',strtotime($bordereau->date_bordereau))}}</td>
                            <td>{{$bordereau->code_bordereau}}</td>
                            <td>{{agentId($bordereau->id_agent)}}</td>
                            <td>{{agentId($bordereau->id_valideur)}}</td>
                            <td style="font-size: 18px" class="text-uppercase bg-white">
                                @if($bordereau->statut == 0)
                                    <span class="badge badge-warning">En attente</span>
                                @else
                                    <span class="badge badge-success">Validé</span>
                                @endif
                            </td>
                            <td style="font-size: 18px" class="text-uppercase bg-white">
                                <a href="{{route('recouvrements.consulter',$bordereau->code_bordereau)}}" class="btn btn-sm btn-outline-primary font-weight-bold">Consulter</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


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


    <style>
        .gris {
            background-color: #f0f0f0; /* Couleur de fond gris clair */
            pointer-events: none;     /* Désactive l'interaction avec les éléments */
            opacity: 0.6;             /* Rend la ligne semi-transparente */
        }
    </style>

@endsection



@section('js')
    <script>
        $(document).ready(function () {


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

            checkAllRowsSaved();




        });

        $(document).on("click", ".delete-btn", function() {
            let code_transaction = $(this).data("code");
            $("#code_transaction").val(code_transaction);
            $("#deleteModal").modal("show");
        });
    </script>
@endsection
