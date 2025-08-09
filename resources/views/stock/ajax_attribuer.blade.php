<div class="card shadow mb-4 mt-2" id="mouvementChargerUpdate">
    <div class="card-header bg-primary py-3">
        <span class="m-0 font-weight-bold text-white text-uppercase">Stock {{$entrepotName}}</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <div class="d-flex justify-content-start gap-2 mb-2 mr-2">
                <button class="btn btn-outline-primary w-auto mr-3 text-uppercase" data-toggle="modal" data-target="#clientSelectionModal">
                    <i class="fas fa-plus-circle"></i> Attribuer les produits selectionnés
                </button>
            </div>
            <input type="text" id="searchTableInput" class="form-control mb-3" placeholder="Rechercher un produit...">

            <table class="table table-striped" id="dataTable">
                <thead class="bg-primary">
                <tr class="text-white" >
                    <th>description</th>
                    @if (auth()->user()->groupe != 'GE')
                        <th class="text-center">prix <br> distr</th>
                    @endif
                    <th class="text-center">prix <br> dmg</th>
                    <th class="text-center">prix <br> det</th>
                    <th class="text-center">date <br> limite</th>
                    <th class="text-right">STDep</th>
                    <th class="text-right">STVen</th>
                    <th class="text-right">STFin</th>
                    <th></th>
                </tr>
                </thead>
                @php
                    $total_depart = 0;
                    $total_vente = 0;
                    $total_final = 0;
                @endphp

                @foreach($produits as $produit)
                    @php
                        $total_depart += $produit['quantite_depart'];
                        $total_vente += $produit['quantite_sortie'];
                        $total_final += $produit['total_quantite'];
                    @endphp
                    <tr>
                        <td>{{ $produit['description'] }}
                            <input type="hidden" name="produits[{{ $produit['code_produit'] }}][description]" value="{{ $produit['description'] }}">
                        </td>
                        @if (auth()->user()->groupe != 'GE')
                            <td class="text-right">{{ $produit['prix'] }}
                                <input type="hidden" name="produits[{{ $produit['code_produit'] }}][prix]" value="{{ $produit['prix'] }}">
                            </td>
                        @endif
                        <td class="text-right">{{ $produit['prix_dmg'] }}
                            <input type="hidden" name="produits[{{ $produit['code_produit'] }}][prix_dmg]" value="{{ $produit['prix_dmg'] }}">
                        </td>
                        <td class="text-right">{{ $produit['prix_commercial'] }}
                            <input type="hidden" name="produits[{{ $produit['code_produit'] }}][prix_commercial]" value="{{ $produit['prix_commercial'] }}">
                        </td>
                        <td class="text-right text-danger font-weight-bold">
                            {{ \Carbon\Carbon::parse($produit['date_limite'])->format('d/m/Y') }}
                        </td>
                        <td class="text-right text-primary font-weight-bold">
                            {{ $produit['quantite_depart'] }}
                        </td>
                        <td class="text-right text-danger font-weight-bold">
                            {{ $produit['quantite_sortie'] }}
                        </td>
                        <td class="text-right text-success font-weight-bold">
                            {{ $produit['total_quantite'] }}
                        </td>
                        <td class="text-center">
                            @if($produit['total_quantite'] > 0)
                                <input type="checkbox"
                                       style="width: 20px; height: 20px;"
                                       name="produits[{{ $produit['code_produit'] }}][selected]" value="1">
                            @endif
                        </td>
                    </tr>
                @endforeach

                <tr class="bg-secondary text-white font-weight-bold">
                    <td>Total</td>
                    @if (auth()->user()->groupe != 'GE')
                        <td></td> {{-- Colonne prix usi vide --}}
                    @endif
                    <td></td> {{-- prix com vide --}}
                    <td></td> {{-- prix com vide --}}
                    <td></td> {{-- prix dmg vide --}}
                    <td class="text-right">{{ $total_depart }}</td>
                    <td class="text-right">{{ $total_vente }}</td>
                    <td class="text-right">{{ $total_final }}</td>
                    <td></td> {{-- Colonne checkbox vide --}}
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<form method="POST" action="{{route('stock.enregistrer')}}">
    @csrf
    <div class="modal fade" id="clientSelectionModal" tabindex="-1" aria-labelledby="clientModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white text-uppercase">Attribution du stock</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="commercial_id" class="text-uppercase font-weight-bold">Sélectionner un commercial ou un client</label>
                            <select name="commercial_id" id="commercial_id" class="form-control" required>
                                <option value="">-- Choisir un commercial ou un client --</option>
                                @foreach($commercials as $commercial)
                                    <option value="{{ $commercial->id }}" data-type="{{ $commercial->groupe }}">{{ $commercial->personnel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="prix_vente_id" class="text-uppercase font-weight-bold">Sélectionner le prix a appliquer</label>
                            <select name="prix_vente_id" id="prix_vente_id" class="form-control text-uppercase" required>
                                <option value="">-- Choisir le prix a appliquer --</option>
                                <option value="DET" data-type="DET">Prix detaillant</option>
                                <option value="DMG" data-type="DMG">Prix DMG</option>
                            </select>

                        </div>
                        <input type="hidden" value="{{$entrepotID}}" name="entrepotID">
                        <div class="table-responsive">
                            <table class="table table-striped" id="dataTablePerso">
                                <thead class="bg-primary">
                                <tr class="text-white">
                                    <th>Produit</th>
                                    <th>Prix</th>
                                    <th width="20%">Quantité</th>
                                    <th width="20%">Prix total</th>
                                </tr>
                                </thead>
                                <tbody id="productTableBody">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Attribuer</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Annuler</button>
                </div>

            </div>
        </div>
    </div>
</form>

<script>

    let productDataStore = {};
    function getPrixByCommercial(groupe, prix_com, prix_dmg) {
        if (!groupe || groupe === '') {
            return prix_com; // Prix par défaut
        }

        // Déterminer le prix selon le groupe
        switch(groupe) {
            case 'dmg':
            case 'DMG':
                return prix_dmg;
            default:
                return prix_com; // Prix par défaut si groupe non reconnu
        }
    }
    function storeProductData(code, prix_com, prix_dmg) {
        productDataStore[code] = {
            prix_com: prix_com,
            prix_dmg: prix_dmg
        };
    }
    function getProductDataByCode(code) {
        return productDataStore[code];
    }
    function updateAllProductPrices() {
        const select = document.getElementById('prix_vente_id');
        const selectedOption = select.options[select.selectedIndex];

        const type = selectedOption.getAttribute('data-type'); // client, commercial, etc.

        const tbody = document.getElementById('productTableBody');
        const rows = tbody.querySelectorAll('tr:not(#global-total-row)');

        rows.forEach(row => {
            const prixInput = row.querySelector('input[name*="[prix]"]');
            const quantiteInput = row.querySelector('input[name*="[quantite]"]');
            const totalSpan = row.querySelector('span[id*="total-"]');

            if (prixInput && quantiteInput && totalSpan) {
                // Récupérer les prix depuis les données stockées
                const code = prixInput.name.match(/\[(.*?)\]/)[1];
                const productData = getProductDataByCode(code);


                if (productData) {
                    const newPrix = getPrixByCommercial(type, productData.prix_com, productData.prix_dmg);
                    prixInput.value = newPrix.toFixed(0);

                    console.log(newPrix)

                    // Mettre à jour le total
                    const quantite = parseFloat(quantiteInput.value) || 0;
                    totalSpan.textContent = (newPrix * quantite).toFixed(0);
                }
            }
        });

        // Mettre à jour le total général
        updateGlobalTotal();
    }
    document.getElementById('searchTableInput').addEventListener('keyup', function () {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#catalogueTableBody tr');

        rows.forEach(row => {
            let description = row.querySelector('td:first-child').innerText.toLowerCase();
            row.style.display = description.includes(filter) ? '' : 'none';
        });
    });
    document.querySelector('[data-target="#clientSelectionModal"]').addEventListener('click', function (e) {
        const selectedRows = document.querySelectorAll('input[type="checkbox"]:checked');
        const tbody = document.getElementById('productTableBody');
        tbody.innerHTML = ''; // Nettoie d'abord
        productDataStore = {}; // Reset du store

        if (selectedRows.length === 0) {
            alert("Veuillez sélectionner au moins un produit avant d’attribuer le stock.");
            e.stopPropagation(); // Empêche l’ouverture du modal
            return;
        }

        const select = document.getElementById('prix_vente_id');
        const selectedOption = select.options[select.selectedIndex];
        const type = selectedOption.getAttribute('data-type'); // client, commercial, etc.



        selectedRows.forEach(checkbox => {
            const row = checkbox.closest('tr');
            const code = checkbox.name.match(/\[(.*?)\]/)[1];
            const description = row.cells[0].innerText.trim();
            @if (auth()->user()->groupe != 'GE')
            const prix_usine = parseFloat(row.cells[1].innerText.trim());
            const prix_dmg = parseFloat(row.cells[2].innerText.trim());
            const prix_com = parseFloat(row.cells[3].innerText.trim());
            const quantitemax = parseFloat(row.cells[7].innerText.trim());
            @else
            const prix_dmg = parseFloat(row.cells[1].innerText.trim());
            const prix_com = parseFloat(row.cells[2].innerText.trim());
            const quantitemax = parseFloat(row.cells[6].innerText.trim());
            @endif

            // Stocker les données du produit
            storeProductData(code, prix_com, prix_dmg);


            // Déterminer le prix selon le groupe du commercial sélectionné
            const prix = getPrixByCommercial(type, prix_com, prix_dmg);


            const newRow = document.createElement('tr');
            const prixId = `prix-${code}`;
            const quantiteId = `quantite-${code}`;
            const totalId = `total-${code}`;

            newRow.innerHTML = `
                <td>
                    ${description}
                    <input type="hidden" name="produits[${code}][code_produit]" value="${code}">
                    <input type="hidden" name="produits[${code}][description]" value="${description}">
                </td>
               <td>
                   <input type="number" id="${prixId}" name="produits[${code}][prix]" class="form-control text-right text-danger font-weight-bold" min="0" value="${prix.toFixed(0)}" readonly>
                </td>
                <td class="text-center">
                    <input type="number" id="${quantiteId}" name="produits[${code}][quantite]" class="form-control" min="1" max="${quantitemax.toFixed(0)}" value="0"
                           oninput="updateTotal('${prixId}', '${quantiteId}', '${totalId}')">
                </td>
                <td class="text-center">
                    <span id="${totalId}">0</span>
                </td>
            `;
            tbody.appendChild(newRow);
        });

        const totalRow = document.createElement('tr');
        totalRow.id = 'global-total-row';
        totalRow.innerHTML = `
    <td colspan="2" class="text-right font-weight-bold text-danger">Total Général</td>
    <td class="text-center font-weight-bold text-danger" id="global-total-qt">0</td>
    <td class="text-center font-weight-bold text-danger" id="global-total-prix">0</td>
`;
        tbody.appendChild(totalRow);
    });
    function updateTotal(prixId, quantiteId, totalId) {
        const prixInput = document.getElementById(prixId);
        const quantiteInput = document.getElementById(quantiteId);
        const totalSpan = document.getElementById(totalId);

        const prix = parseFloat(prixInput.value) || 0;
        const quantite = parseFloat(quantiteInput.value) || 0;
        const total = prix * quantite;

        totalSpan.textContent = total.toFixed(0);

        // Mise à jour du total global
        updateGlobalTotal();
    }
    function updateGlobalTotal() {
        let globalTotalPrix = 0;
        let globalTotalQt = 0;

        // Somme des totaux prix
        document.querySelectorAll('span[id^="total-"]').forEach(span => {
            const val = parseFloat(span.textContent) || 0;
            globalTotalPrix += val;
        });

        // Somme des quantités
        document.querySelectorAll('input[id^="quantite-"]').forEach(input => {
            const val = parseFloat(input.value) || 0;
            globalTotalQt += val;
        });

        document.getElementById('global-total-prix').textContent = globalTotalPrix.toFixed(0);
        document.getElementById('global-total-qt').textContent = globalTotalQt.toFixed(0);
    }
    document.getElementById('prix_vente_id').addEventListener('change', function() {
        updateAllProductPrices();
    });
</script>



