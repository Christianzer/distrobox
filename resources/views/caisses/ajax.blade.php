<div class="card shadow mb-4">
    <div class="card-header bg-primary py-3">
        <span class="m-0 text-uppercase font-weight-bold text-white">{{ isset($editEncaissement) ? 'Modifier' : 'Ajouter nouveau' }}</span>
    </div>
    <div class="card-body">
        <form method="post" action="{{route('caisses.enregistrer')}}">
            @csrf
            <div class="row">
                <input type="hidden" name="entrepotID" value="{{$entrepotID}}">
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="date_facture">Date transaction</label>
                        <input type="date" class="form-control" id="date_facture" name="date_facture" value="{{ isset($editEncaissement) ? $editEncaissement->date_transaction : '' }}" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="code_sortie">Type de transaction</label>
                        <select class="form-control text-uppercase" id="typeTransaction" name="typeTransaction">
                            <option value="sor">Sortie de caisse</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Compte</label>
                        <select class="form-control text-uppercase" id="id_proprietaires" required name="id_proprietaires">
                            <option value="" selected>--- Choisir un compte ----</option>
                            @foreach($comptes as $proprieatire)
                                <option value="{{ $proprieatire->id }}">
                                    {{ $proprieatire->personnel }} (
                                    @switch($proprieatire->groupe)
                                        @case('SC') Commercial @break
                                        @case('SP') Superviseur @break
                                        @case('SA') Super Admin @break
                                        @case('FO') Fournisseur @break
                                        @case('CL') Client @break
                                        @case('GE') Gerant @break
                                        @case('DM') Client DMG @break
                                    @endswitch
                                    )
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="montant">Montant</label>
                        <input
                            type="number"
                            class="form-control"
                            id="montant"
                            name="montant"
                            value=" {{ isset($editEncaissement) ? $editEncaissement->montant : '' }}"
                            required
                        >
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="observation">Observation</label>
                        <textarea
                            class="form-control"
                            id="observation"
                            name="observation"
                            rows="3"
                        >{{ isset($editEncaissement) ? $editEncaissement->description : '' }}</textarea>
                    </div>
                </div>
            </div>
            <div align="right">
                <a href="{{ route('caisses.index') }}" class="btn btn-danger">Annuler</a>
                <button type="submit" class="btn btn-primary">{{ isset($editEncaissement) ? 'Mettre à jour' : 'Enregistrer' }}</button>
            </div>
        </form>

    </div>


</div>


<div class="card shadow mb-4">
    <div class="card-header bg-primary py-3">
        <span class="m-0 font-weight-bold text-white text-uppercase">Liste des entrées et sorties de caisse</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped" id="dataTable">
                <thead class="bg-primary">
                <tr class="text-white">
                    <th>Date</th>
                    <th>Type Transaction</th>
                    <th>Compte</th>
                    <th>Montant</th>
                    <th>Observation</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($transactions as $transaction)

                    <tr class="text-capitalize">
                        <td width="10%">{{date('d-m-Y',strtotime($transaction->date_paiement))}}</td>

                        <td>
                            @if($transaction->type_mouvement == 'sor')
                                Sortie de caisse
                            @else
                                Entrée de caisse
                            @endif
                        </td>
                        <td>
                            {{$transaction->personnel ?? ''}}
                            (
                            @switch($transaction->groupe)
                                @case('SC') Commercial @break
                                @case('SP') Superviseur @break
                                @case('SA') Super Admin @break
                                @case('FO') Fournisseur @break
                                @case('CL') Client @break
                                @case('GE') Gerant @break
                                @case('DM') Client DMG @break
                            @endswitch
                    )
                        </td>
                        <td>{{ number_format($transaction->montant, 0,',',' ') }} FCFA</td>

                        <td>
                            @if($transaction->type_mouvement == 'ver')
                                Versement
                            @else
                                {{ $transaction->description }}
                            @endif
                        </td>
                        <td>
                            <a href="" target='_bank' class="btn btn-info btn-sm">Re&ccedil;u</a>
                            @if (auth()->user()->groupe != 'GE')
                                <a class="btn btn-danger btn-sm"
                                   data-code="{{ $transaction->id_paiement }}"
                                   data-type="@switch($transaction->type_mouvement)
                                @case('ver') Versement @break
                                @case('ent') Entrée de caisse @break
                                @case('sor') Sortie de caisse @break

                            @endswitch"
                                   data-personne="{{ $transaction->personnel }}"
                                   data-prix="{{ number_format($transaction->montant, 0,',',' ') }} FCFA"
                                   id="suppression" data-toggle="modal"
                                >Supprimer</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>

            </table>
        </div>
    </div>
</div>


<!-- supprimer paiement -->
<div class="modal fade" id="supprimer">
    <form action="{{route('caisses.supprimer')}}" method="post">
        @csrf
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-uppercase font-weight-bold text-white" id="exampleModalLabel">Voulez-vous vraiment annuler le decaissement ?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type='hidden' id="code_transaction" name='code_transaction'>
                    <input type='hidden' id="entrepotID" name='entrepotID' value="{{$entrepotID}}">
                    <div id="data_anuler_supp" class="text-dark">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Non ne pas annuler</button>
                    <button type="submit" class="btn btn-danger" name="supprimer"><i class="fas fa-trash fa-sm fa-fw mr-2 text-gray-400"></i> Oui annuler </button>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    // Call the dataTables jQuery plugin
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "ordering": false, // désactivation du tri
            "language": {
                "sEmptyTable":     "Aucune donnée disponible dans le tableau",
                "sInfo":           "Affichage de l'élément _START_ à _END_ sur _TOTAL_ éléments",
                "sInfoEmpty":      "Affichage de l'élément 0 à 0 sur 0 élément",
                "sInfoFiltered":   "(filtré à partir de _MAX_ éléments au total)",
                "sInfoThousands":  ",",
                "sLengthMenu":     "Afficher _MENU_ éléments",
                "sLoadingRecords": "Chargement...",
                "sProcessing":     "Traitement...",
                "sSearch":         "Rechercher :",
                "sZeroRecords":    "Aucun élément correspondant trouvé",
                "oPaginate": {
                    "sFirst":    "Premier",
                    "sLast":     "Dernier",
                    "sNext":     "Suivant",
                    "sPrevious": "Précédent"
                },
                "oAria": {
                    "sSortAscending":  ": activer pour trier la colonne par ordre croissant",
                    "sSortDescending": ": activer pour trier la colonne par ordre décroissant"
                },
                "select": {
                    "rows": {
                        "_": "%d lignes sélectionnées",
                        "0": "Aucune ligne sélectionnée",
                        "1": "1 ligne sélectionnée"
                    }
                }
            }
        });
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


    $('body').on('click', '#suppression', function (event) {
        var code_transaction = $(this).data('code');
        var type_transaction = $(this).data('type');
        var personnel = $(this).data('personne');
        var prix = $(this).data('prix');
        $("#code_transaction").val(code_transaction)
        var information = "Voulez-vous vraiment annuler la transaction " +
            ", du type " + type_transaction +
            ", pour le compte de " + personnel +
            ", au prix de " + prix + " FCFA ?";
        document.getElementById('data_anuler_supp').textContent = information;
        $('#supprimer').modal('show');
    });

</script>
