<div class="row">
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-header bg-primary py-3">
                <span class="m-0 font-weight-bold text-white text-uppercase">STOCK {{$entrepotName}}</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6 mb-1">
                        <div class="p-1 border rounded text-center">
                            <strong class="small font-weight-bold">STOCK INITIAL</strong><br>
                            <span class="small font-weight-bold text-danger">{{formatNumber($data['quantite_totale_initial'])}} PCS <br>
                                 @if (auth()->user()->groupe != 'GE')
                                    {{formatNumber($data['prix_total_initial'])}} CFA
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="col-6 mb-1">
                        <div class="p-1 border rounded text-center">
                            <strong class="small font-weight-bold">TOTAL ENTREE</strong><br>
                            <span class="small font-weight-bold text-danger">{{formatNumber($data['quantite_totale_apres_entrees'])}} PCS <br>
                                 @if (auth()->user()->groupe != 'GE')
                                    {{formatNumber($data['prix_total_apres_entrees'])}} CFA
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="col-6 mb-1">
                        <div class="p-1 border rounded text-center">
                            <strong class="small font-weight-bold">TOTAL SORTIE</strong><br>
                            <span class="small font-weight-bold text-danger">{{formatNumber($data['total_sorties_pcs'])}} PCS <br>
                                 @if (auth()->user()->groupe != 'GE')
                                    {{formatNumber($data['total_sorties_prix'])}} CFA
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="col-6 mb-1">
                        <div class="p-1 border rounded text-center">
                            <strong class="small font-weight-bold">STOCK FINAL THEO</strong><br>
                            <span class="small font-weight-bold text-danger">{{formatNumber($data['stock_final_theorique_pcs'])}} PCS <br>
                                 @if (auth()->user()->groupe != 'GE')
                                    {{formatNumber($data['stock_final_theorique_prix'])}} CFA
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="col-6 mb-1">
                        <div class="p-1 border rounded text-center">
                            <strong class="small font-weight-bold">STOCK FINAL REEL</strong><br>
                            <span class="small font-weight-bold text-danger">{{formatNumber($data['stock_final_reel_pcs'])}} PCS <br>
                                 @if (auth()->user()->groupe != 'GE')
                                    {{formatNumber($data['stock_final_reel_prix'])}} CFA
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="col-6 mb-1">
                        <div class="p-1 border rounded text-center">
                            <strong class="small font-weight-bold">ECART STOCK</strong><br>
                            <span class="small font-weight-bold text-danger">{{formatNumber($data['ecart_stock_pcs'])}} PCS <br>
                                 @if (auth()->user()->groupe != 'GE')
                                    {{formatNumber($data['ecart_stock_prix'])}} CFA
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-header bg-primary py-3">
                <span class="m-0 font-weight-bold text-white text-uppercase">COMPTES COMMERCIAL {{$entrepotName}}</span>
            </div>
            <div class="card-body">
                <div class="row g-1">
                    <div class="col-4 mb-1">
                        <div class="p-1 border rounded text-center">
                            <strong class="small font-weight-bold">TOTAL <br> ENLEVEMENT</strong><br>
                            <span class="small font-weight-bold text-danger">{{formatNumber($data['total_enlevement'])}} CFA</span>
                        </div>
                    </div>
                    <div class="col-4 mb-1">
                        <div class="p-1 border rounded text-center">
                            <strong class="small font-weight-bold">TOTAL <br> VERSEMENT</strong><br>
                            <span class="small font-weight-bold text-danger">{{formatNumber($data['total_versement'])}} CFA</span>
                        </div>
                    </div>
                    <div class="col-4 mb-1">
                        <div class="p-1 border rounded text-center">
                            <strong class="small font-weight-bold">TOTAL <br>CREDIT</strong><br>
                            <span class="small font-weight-bold text-danger">{{formatNumber($data['total_credit'])}} CFA</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-header bg-primary py-3">
                <span class="m-0 font-weight-bold text-white text-uppercase">CAISSES {{$entrepotName}}</span>
            </div>
            <div class="card-body">
                <div class="row g-1">
                    <div class="col-4 mb-1">
                        <div class="p-1 border rounded text-center">
                            <strong class="small font-weight-bold">ENTREE <br> CAISSE</strong><br>
                            <span class="small font-weight-bold text-danger">{{formatNumber($data['entree_caisse'])}} CFA</span>
                        </div>
                    </div>
                    <div class="col-4 mb-1">
                        <div class="p-1 border rounded text-center">
                            <strong class="small font-weight-bold">SORTIE <br> CAISSE</strong><br>
                            <span class="small font-weight-bold text-danger">{{formatNumber($data['sortie_caisse'])}} CFA</span>
                        </div>
                    </div>
                    <div class="col-4 mb-1">
                        <div class="p-1 border rounded text-center">
                            <strong class="small font-weight-bold">SOLDE <br>CAISSE</strong><br>
                            <span class="small font-weight-bold text-danger">{{formatNumber($data['solde_caisse'])}} CFA</span>
                        </div>
                    </div>
                    @if (auth()->user()->groupe != 'GE')
                        <div class="col-4 mb-1">
                            <div class="p-1 border rounded text-center">
                                <strong class="small font-weight-bold">TOTAL <br>MARE BRUTE</strong><br>
                                <span class="small font-weight-bold text-danger">{{formatNumber($data['marge_brute'])}} CFA</span>
                            </div>
                        </div>
                        <div class="col-4 mb-1">
                            <div class="p-1 border rounded text-center">
                                <strong class="small font-weight-bold">TOTAL <br> MARGE NET</strong><br>
                                <span class="small font-weight-bold text-danger">{{formatNumber($data['marge_nette'])}} CFA</span>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

