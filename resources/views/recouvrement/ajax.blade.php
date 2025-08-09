<div class="card shadow mb-4 mt-2" id="mouvementCharger">
    <div class="card-header bg-primary py-3">
        <span class="m-0 font-weight-bold text-white text-uppercase">Recouvrement Stock {{$entrepotName}}</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped" id="dataTable">
                <thead class="bg-primary">
                <tr class="text-white">
                    <th>Agent</th>
                    <th class="text-right">Qté</th>
                    <th class="text-right">Total</th>
                    <th class="text-right">Versé</th>
                    <th class="text-right">Reste</th>
                    <th class="text-right">Dépense</th>
                    <th></th>
                </tr>

                </thead>
                <tbody>
                @foreach($agents as $agent)
                    <tr style="align-content: center">
                        <td>{{ $agent->personnel }}</td>
                        <td class="text-right font-weight-bold">{{formatNumber(calculerMontant($agent->id)[0])}}</td>
                        <td class="text-right font-weight-bold">{{formatNumber(calculerMontant($agent->id)[1])}}</td>
                        <td class="text-right text-success font-weight-bold">{{formatNumber(calculerMontant($agent->id)[2])}}</td>
                        <td class="text-right text-danger font-weight-bold">{{formatNumber(calculerMontant($agent->id)[3])}}</td>
                        <td class="text-right text-warning font-weight-bold">{{formatNumber(calculerMontant($agent->id)[4])}}</td>
                        <td class="text-right">
                            <button class="btn btn-outline-primary btn-sm text-center" data-toggle="collapse" data-target="#courses-{{ $agent->id }}">
                                <i class="fas fa-eye"></i> Consulter
                            </button>
                            <button class="btn btn-outline-success btn-sm text-center delete-btn"
                                    data-agent="{{$agent->id}}"
                                    data-montant="{{calculerMontant($agent->id)[3]}}"
                            >
                                <i class="fas fa-money-bill-wave"></i> Versement
                            </button>
                            <a href="{{route('recouvrements.imprimer',['code'=>$agent->id,'entrepot'=>$entrepotID])}}" target="_blank" class="btn btn-outline-info btn-sm text-center">
                                <i class="fas fa-print"></i> Imprimer
                            </a>
                        </td>
                    </tr>

                    <tr id="courses-{{ $agent->id }}" class="collapse">
                        <td colspan="10">
                            @foreach (ListesTransactions($agent->id,$entrepotID) as $transaction)
                                <div class="bg-white text-primary text-white p-2">
                                    <strong>Code attribution :</strong> {{ $transaction->code_transactions ?? 'N/A' }} |
                                    <strong>Date attribution:</strong> {{ \Carbon\Carbon::parse($transaction->date_transaction)->format('d/m/Y') }}
                                </div>
                                <table class="table table-bordered text-capitalize">
                                    <thead class="bg-success text-white font-weight-bold">
                                    <tr>
                                        <th>description</th>
                                        <th>prix</th>
                                        <th>quantité</th>
                                        <th>montant</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($transaction->produits as $produit)
                                        <tr>
                                            <td>{{$produit->description}}</td>
                                            <td>{{formatNumber($produit->prix)}}</td>
                                            <td>{{formatNumber($produit->quantite)}}</td>
                                            <td>{{formatNumber($produit->montant)}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr class="bg-secondary text-white">
                                        <td colspan="2"><strong>total</strong></td>
                                        <td><strong>{{ $transaction->total_quantite }}</strong></td>
                                        <td><strong>{{ number_format($transaction->total_montant, 0, ',', ' ') }}</strong></td>
                                    </tr>
                                    </tfoot>
                                </table>
                            @endforeach
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="versementModal" tabindex="-1" role="dialog" aria-labelledby="versementModalLabel" aria-hidden="true">
    <form action="{{route('recouvrements.paiement')}}" method="post">
        @csrf
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="versementModalLabel">Effectuer un versement</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="id_agent" id="versement_id_agent">
                    <input type="hidden" name="entrepot_id" value="{{$entrepotID}}">

                    <div class="form-group">
                        <label>Montant à verser</label>
                        <input type="text" class="form-control" id="montant_a_verser" readonly>
                    </div>

                    <div class="form-group">
                        <label>Montant versé</label>
                        <input type="number" class="form-control" name="montant_verse" required min="0">
                    </div>

                    <div class="form-group">
                        <label>Date de versement</label>
                        <input type="date" class="form-control" name="date_versement" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Valider</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>


    $(document).on("click", ".delete-btn", function() {
        let agent = $(this).data("agent");
        let montant = $(this).data("montant");
        $("#versement_id_agent").val(agent);
        $("#montant_a_verser").val(montant.toLocaleString());

        $("#versementModal").modal("show");
    });

</script>
