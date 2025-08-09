<style>
    .promotion-input {
        width: 100%;
        box-sizing: border-box;
    }

</style>

<div class="card shadow mb-4 mt-2" id="mouvementCharger">
    <div class="card-header bg-primary py-3">
        <span class="m-0 font-weight-bold text-white text-uppercase">Liste des produits {{$entrepotName}} ({{$produits->count()}})</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <input type="text" id="searchTableInput" class="form-control mb-3" placeholder="Rechercher un produit...">
            <table class="table table-striped" id="dataTable">
                <thead class="bg-primary">
                <tr class="text-white">
                    <th></th>
                    <th>description</th>
                    <th class="text-center">prix <br> distr</th>
                    <th class="text-center">prix <br> dmg</th>
                    <th class="text-center">prix <br> det</th>
                    <th class="text-center">Actions</th>
                </tr>
                </thead>
                <tbody id="catalogueTableBody">
                @php($n = 1)
                @foreach($produits as $produit)
                    <tr>
                        <td>{{ $n }}</td>
                        <td>{{ $produit->description }}</td>
                        <td class="prix-cell text-center" data-id="{{ $produit->id_avoir_produit }}">
                            <span class="prix-text">{{ $produit->prix_revient }}</span>
                            <input
                                type="number"
                                name="prix_revient[{{ $produit->id_avoir_produit }}]"
                                value="{{ $produit->prix_revient }}"
                                class="form-control form-control-sm col-5 prix-input"
                                min="0"
                                style="display: none;"
                            >
                        </td>
                        <td class="dmg-cell text-center" data-id="{{ $produit->id_avoir_produit }}">
                            <span class="dmg-text">{{ $produit->prix_dmg }}</span>
                            <input
                                type="number"
                                name="prix_dmg[{{ $produit->id_avoir_produit }}]"
                                value="{{ $produit->prix_dmg }}"
                                class="form-control form-control-sm col-5 dmg-input"
                                min="0"
                                style="display: none;"
                            >
                        </td>
                        <td class="commercial-cell text-center" data-id="{{ $produit->id_avoir_produit }}">
                            <span class="commercial-text">{{ $produit->prix_commercial }}</span>
                            <input
                                type="number"
                                name="prix_commercial[{{ $produit->id_avoir_produit }}]"
                                value="{{ $produit->prix_commercial }}"
                                class="form-control form-control-sm col-5 commercial-input"
                                min="0"
                                style="display: none;"
                            >
                        </td>
                        <td>
                            <a href="{{ route('produits.edit', $produit->id_avoir_produit) }}" class="btn btn-sm btn-outline-info">Modifier</a>
                            <form action="{{ route('produits.destroy', $produit->id_avoir_produit) }}" method="post" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    @php($n++)
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Filtrer la table en tapant dans la recherche
    document.getElementById('searchTableInput').addEventListener('keyup', function () {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#catalogueTableBody tr');

        rows.forEach(row => {
            let description = row.querySelector('td:nth-child(2)').innerText.toLowerCase(); // 2ème colonne = description
            row.style.display = description.includes(filter) ? '' : 'none';
        });
    });

    // Activation édition promotion
    function makeCellEditable(cellClass, spanClass, inputClass) {
        document.querySelectorAll(`.${cellClass}`).forEach(cell => {
            const span = cell.querySelector(`.${spanClass}`);
            const input = cell.querySelector(`.${inputClass}`);

            span.addEventListener('click', () => {
                span.style.display = 'none';
                input.style.display = 'inline-block';
                input.focus();
            });

            input.addEventListener('blur', () => {
                const newValue = input.value.trim();
                const oldValue = span.textContent.trim();
                if (newValue === oldValue) {
                    input.style.display = 'none';
                    span.style.display = 'inline';
                    return;
                }
                span.textContent = newValue;
                span.style.display = 'inline';
                input.style.display = 'none';
                let field = '';
                if (cell.classList.contains('prix-cell')) {
                    field = 'prix_revient';
                } else if (cell.classList.contains('dmg-cell')) {
                    field = 'prix_dmg';
                } else if (cell.classList.contains('commercial-cell')) {
                    field = 'prix_commercial';
                }

                sendUpdate(cell.dataset.id,field, newValue);
            });

            input.addEventListener('keydown', e => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    input.blur();
                }
            });
        });
    }

    function sendUpdate(productId, field, value) {
        fetch('{{route('produits.ajax')}}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({
                id_avoir_produit: productId,
                field: field,
                value: value
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Mise à jour réussie', data);
                } else {
                    alert('Erreur lors de la mise à jour');
                }
            })
            .catch(error => {
                console.error('Erreur AJAX', error);
            });
    }

    makeCellEditable('prix-cell', 'prix-text', 'prix-input');
    makeCellEditable('dmg-cell', 'dmg-text', 'dmg-input');
    makeCellEditable('commercial-cell', 'commercial-text', 'commercial-input');



</script>


