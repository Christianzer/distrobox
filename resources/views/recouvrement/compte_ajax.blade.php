<div class="card shadow mb-4 mt-2" id="mouvementCharger">
    <div class="card-header bg-primary py-3">
        <span class="m-0 font-weight-bold text-white text-uppercase">COMPTES {{$entrepotName}}</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <input type="text" id="searchTableInput" class="form-control mb-3" placeholder="Rechercher un compte...">

            <table class="table table-striped" id="dataTable">
                <thead class="bg-primary">
                <tr class="text-white" >
                    <th>categorie</th>
                    <th>compte</th>
                    <th class="text-right">debit</th>
                    <th class="text-right">credit</th>
                    <th class="text-right">solde</th>
                </tr>
                </thead>
                <tbody id="catalogueTableBody">
                @foreach($comptes as $compte)
                    <tr>
                        <td class="text-uppercase">{{ $compte['categorie'] }}</td>
                        <td class="text-uppercase">{{ $compte['comptes'] }}</td>
                        <td class="text-right">{{ formatNumber($compte['debit']) }}</td>
                        <td class="text-right">{{ formatNumber($compte['credit']) }}</td>
                        <td class="text-right">{{ formatNumber($compte['solde']) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
