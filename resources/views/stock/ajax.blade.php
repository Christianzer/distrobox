<div class="card shadow mb-4 mt-2" id="mouvementCharger">
    <div class="card-header bg-primary py-3">
        <span class="m-0 font-weight-bold text-white text-uppercase">Mouvement Stock {{$entrepotName}}</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <div class="d-flex justify-content-start gap-2 mb-2 mr-2">
                <button class="btn btn-outline-primary w-auto mr-3 text-uppercase" data-toggle="modal" data-target="#clientSelectionModal">
                    <i class="fas fa-plus-circle"></i> Restockage
                </button>
            </div>
            <input type="text" id="searchTableInput" class="form-control mb-3" placeholder="Rechercher un produit...">

            @php
                use Carbon\Carbon;
            @endphp

            <table class="table table-striped" id="dataTable">
                <thead class="bg-primary">
                <tr class="text-white" >
                    <th>description</th>
                    @if (auth()->user()->groupe != 'GE')
                        <th class="text-center">prix <br> distr</th>
                        <th class="text-center">prix <br> dmg</th>
                        <th class="text-center">prix <br> det</th>
                    @endif
                    @foreach($lastDates as $date)
                        <th class="text-right font-weight-bold position-relative">
                            <div>
                                @foreach(str_split($date->code_bl, 4) as $part)
                                    {{ $part }}<br>
                                @endforeach
                                {{ \Carbon\Carbon::parse($date->date_stockage)->format('d/m/Y') }}
                            </div>

                            @php
                                $dateStockageDernier = Carbon::parse($date->date_stockage);
                                $dateTransaction = Carbon::parse($derniereTransaction);
                            @endphp

                            @if(Carbon::parse($date->date_stockage)->format('Y-m-d') === $latestDate)
                                @if($dateTransaction->lt($dateStockageDernier))
                                    <button
                                        type="button"
                                        class="btn btn-link text-danger p-0 position-absolute"
                                        style="top: 5px; right: 5px;"
                                        data-toggle="modal"
                                        data-target="#deleteModal"
                                        data-code="{{ $date->code_bl }}"
                                        data-date="{{ \Carbon\Carbon::parse($date->date_stockage)->format('d/m/Y') }}"
                                        title="Supprimer"
                                    >
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                @endif

                            @endif
                        </th>
                    @endforeach
                    <th class="text-right">Total</th>
                </tr>
                </thead>
                <tbody id="catalogueTableBody">
                @php
                    $totauxParDate = [];
                    $totalGeneral = 0;
                @endphp
                @foreach($catalogues as $catalogue)
                    <tr>
                        <td>{{ $catalogue['description'] }}</td>
                        @if (auth()->user()->groupe != 'GE')
                            <td class="text-right"> {{ $catalogue['prix'] }}</td>
                            <td class="text-right"> {{ $catalogue['prix_dmg'] }}</td>
                            <td class="text-right"> {{ $catalogue['prix_commercial'] }}</td>
                        @endif
                        @php
                            $rowTotal = 0;
                        @endphp
                        @foreach($lastDates as $date)
                            @php
                                $currentDate = Carbon::parse($date->date_stockage)->format('Y-m-d');
                                $quantite = $catalogue['dates'][$currentDate]['quantite'] ?? 0;
                                $rowTotal += $quantite;
                                        if (!isset($totauxParDate[$currentDate])) {
            $totauxParDate[$currentDate] = 0;
        }
        $totauxParDate[$currentDate] += $quantite;

        $totalGeneral += $quantite;
                            @endphp
                            <td class="text-right"
                                data-id="{{$catalogue['dates'][Carbon::parse($date->date_stockage)->format('Y-m-d')]['id_catalogue'] ?? '' }}"
                                data-date="{{ Carbon::parse($date->date_stockage)->format('Y-m-d') ?? '' }}"
                                data-row-id="{{$catalogue['dates'][Carbon::parse($date->date_stockage)->format('Y-m-d')]['id_catalogue'] ?? '' }}"
                                data-value="{{ $catalogue['dates'][Carbon::parse($date->date_stockage)->format('Y-m-d')]['quantite'] ?? 0}}">
                                {{ $catalogue['dates'][Carbon::parse($date->date_stockage)->format('Y-m-d')]['quantite'] ?? 0 }}
                            </td>
                        @endforeach
                        <td class="text-right font-weight-bold total-cell">{{ $rowTotal }}</td>
                    </tr>
                @endforeach
                </tbody>
                <tr class="bg-light font-weight-bold text-danger">
                    <td>Total</td>
                    @if (auth()->user()->groupe != 'GE')
                        <td></td>
                        <td></td>
                        <td></td>
                    @endif
                    @foreach($lastDates as $date)
                        @php
                            $currentDate = Carbon::parse($date->date_stockage)->format('Y-m-d');
                        @endphp
                        <td class="text-right">
                            {{ $totauxParDate[$currentDate] ?? 0 }}
                        </td>
                    @endforeach
                    <td class="text-right">{{ $totalGeneral }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="clientSelectionModal" tabindex="-1" aria-labelledby="clientModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white text-uppercase">Restockage de {{$entrepotName}}</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" action="{{route('stock.store')}}" id="stockForm-{{ $entrepotID }}">
                @csrf
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="restock_date">Date de restockage</label>
                            <input type="date" class="form-control" name="restock_date" id="restock_date" required>
                        </div>
                        <div class="form-group">
                            <label for="restock_date">Code bon de livraison</label>
                            <input type="text" class="form-control" name="code_bl" id="code_bl" required>
                        </div>
                        <input type="hidden" value="{{$entrepotID}}" name="entrepotID">
                        <div class="form-group">
                            <input type="text" id="searchInput" class="form-control" placeholder="Rechercher un produit...">
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped" id="dataTablePerso">
                                <thead class="bg-primary">
                                <tr class="text-white">
                                    <th>Produit</th>
                                    <th width="20%">Quantité</th>
                                    <th width="20%">Date limite</th>
                                </tr>
                                </thead>
                                <tbody id="productTableBody">
                                @foreach($produits as $produit)
                                    <tr>
                                        <td>
                                            {{ $produit->description }}
                                            <input type="hidden" name="produits[{{ $produit->code_produit }}][code_produit]" value="{{ $produit->code_produit }}">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" name="produits[{{ $produit->code_produit }}][quantite]" class="form-control" min="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="date" name="produits[{{ $produit->code_produit }}][date_limite]" class="form-control">
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Valider le stock pour {{$entrepotName}}</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Annuler</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Modal de confirmation -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{route('stock.delete')}}"> {{-- Remplace par ta vraie route --}}
            @csrf
            <input type="hidden" name="code_bl" id="modalCodeBl">
            <input type="hidden" name="date_stockage" id="modalDateStockage">
            <input type="hidden" name="entrepotID" value="{{$entrepotID}}">

            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmation</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fermer">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="deleteMessage">Voulez-vous vraiment supprimer ce bon de livraison ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </div>
            </div>
        </form>
    </div>
</div>


<script>
    document.getElementById('searchInput').addEventListener('keyup', function () {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#productTableBody tr');

        rows.forEach(row => {
            let description = row.cells[0].innerText.toLowerCase();
            row.style.display = description.includes(filter) ? '' : 'none';
        });
    });

    document.getElementById('searchTableInput').addEventListener('keyup', function () {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#catalogueTableBody tr');

        rows.forEach(row => {
            let description = row.querySelector('td:first-child').innerText.toLowerCase();
            row.style.display = description.includes(filter) ? '' : 'none';
        });
    });


    document.addEventListener('click', function (e) {
        const cell = e.target.closest('.editable-cell');

        // Si on clique à l'intérieur d'une cellule éditable
        if (cell) {
            if (cell.querySelector('input')) return; // Déjà en édition

            const oldValue = cell.dataset.value || '';
            const input = document.createElement('input');
            input.type = 'number';
            input.value = oldValue;
            input.className = 'form-control form-control-sm';
            input.style.width = '80px';
            input.style.margin = '0 auto';
            input.style.display = 'block';

            // Remplacer contenu par input
            cell.innerHTML = '';
            cell.appendChild(input);
            input.focus();

            // Gérer le blur (perte de focus) pour restaurer la valeur ou enregistrer
            input.addEventListener('blur', function () {
                const dataId = cell.dataset.id;
                const dataDate = cell.dataset.date;
                const newValue = input.value;
                cell.dataset.value = newValue;
                cell.textContent = newValue;

                fetch("{{ route('stock.update') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        id: dataId,
                        date: dataDate,
                        quantite: newValue
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const row = cell.closest('tr');
                            let total = 0;

                            for (let i = 2; i < row.cells.length - 1; i++) {
                                const val = parseFloat(row.cells[i].dataset.value || 0);
                                total += val;
                            }

                            row.querySelector('.total-cell').textContent = total;
                            alert("✅ Quantité mise à jour avec succès !");
                        } else {
                            alert("⚠️ Erreur : " + (data.message || "Une erreur s'est produite."));
                        }
                    })
                    .catch(error => {
                        console.error("Erreur :", error);
                        alert("❌ Une erreur s'est produite lors de la requête.");
                    });

            });

            // Bonus : on peut aussi gérer Entrée pour valider plus vite
            input.addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    input.blur(); // Déclenche le blur pour sauvegarder
                }
            });
        }
    });


    $('#deleteModal').on('show.bs.modal', function (event) {
        let button = $(event.relatedTarget);
        let codeBl = button.data('code');
        let dateStockage = button.data('date');

        let modal = $(this);
        modal.find('#modalCodeBl').val(codeBl);
        modal.find('#modalDateStockage').val(dateStockage);
        modal.find('#deleteMessage').text(`Voulez-vous vraiment supprimer le bon de livraison ${codeBl} de ${dateStockage} ?`);
    });


    document.addEventListener('DOMContentLoaded', function () {
        const stockForms = document.querySelectorAll('form[id^="stockForm-"]');

        stockForms.forEach(function (form) {
            form.addEventListener('submit', function (e) {
                let isValid = true;

                const quantiteInputs = form.querySelectorAll('input[name^="produits["][name$="[quantite]"]');

                quantiteInputs.forEach(function (quantiteInput) {
                    const quantite = parseFloat(quantiteInput.value || 0);
                    const name = quantiteInput.name;

                    const match = name.match(/^produits\[(.*?)\]\[quantite\]$/);
                    if (!match) return;

                    const codeProduit = match[1];
                    const dateInput = form.querySelector(`input[name="produits[${codeProduit}][date_limite]"]`);

                    if (quantite > 0 && (!dateInput.value || dateInput.value.trim() === '')) {
                        isValid = false;
                        dateInput.classList.add('is-invalid');

                        if (!dateInput.nextElementSibling || !dateInput.nextElementSibling.classList.contains('invalid-feedback')) {
                            const error = document.createElement('div');
                            error.classList.add('invalid-feedback');
                            error.innerText = 'La date limite est obligatoire si la quantité est > 0.';
                            dateInput.parentNode.appendChild(error);
                        }
                    } else {
                        dateInput.classList.remove('is-invalid');

                        if (dateInput.nextElementSibling && dateInput.nextElementSibling.classList.contains('invalid-feedback')) {
                            dateInput.nextElementSibling.remove();
                        }
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    alert("Veuillez corriger les erreurs avant de soumettre le formulaire.");
                }
            });
        });
    });



</script>





