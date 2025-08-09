@extends('layout')
@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item text-primary font-weight-bold text-uppercase active" aria-current="page">Gestion des produits</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-end align-items-baseline">
        <h6 class="text-gray-800 mr-2 text-uppercase">Entrepots</h6>
        <div class="mr-2">
            <select class="custom-select text-uppercase" name="entrepot" id="choisirEntrepot">
                @foreach($entrepots as $entrepot)
                    <option {{ isset($editClient) && $editClient->entrepot_id == $entrepot->id_entrepot ? 'selected' : '' }} value="{{ $entrepot->id_entrepot.';'.$entrepot->nom }}">
                        {{ $entrepot->nom }}
                    </option>
                @endforeach
            </select>
        </div>

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

    <div class="card shadow mb-4 mt-1">
        <div class="card-header bg-primary py-3">
            <span class="m-0 font-weight-bold text-white">{{ isset($editClient) ? 'Modifier un produit' : 'Ajouter un nouveau produit' }}</span>
        </div>
        <div class="card-body">
            <form action="{{ isset($editClient) ? route('produits.update', $editClient->id_avoir_produit) : route('produits.store') }}" method="post">
                @csrf
                @if(isset($editClient))
                    @method('PUT')
                @endif
                <input type="hidden" name="entrepotID" id="entrepotID" value="">
                <div class="form-row">
                    <div class="form-group col-md-2">
                        <label>Famille</label>
                        <select class="form-control" required name="famille">
                            <option value="Pressea" {{ isset($editClient) && $editClient->famille == 'Pressea' ? 'selected' : '' }}>Pressea</option>
                            <option value="Eau" {{ isset($editClient) && $editClient->famille == 'Eau' ? 'selected' : '' }}>Eau</option>
                            <option value="CSD" {{ isset($editClient) && $editClient->famille == 'CSD' ? 'selected' : '' }}>CSD</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Description</label>
                        <input type="text" required class="form-control" name="description" value="{{ isset($editClient) ? $editClient->description : '' }}">
                    </div>
                    <div class="form-group col-md-2">
                        <label>Prix usine</label>
                        <input type="number" class="form-control" name="prix" value="{{ isset($editClient) ? $editClient->prix_revient : '' }}">
                    </div>
                    <div class="form-group col-md-2">
                        <label>Prix dmg</label>
                        <input type="number" class="form-control" name="prix_dmg" value="{{ isset($editClient) ? $editClient->prix_dmg : '' }}">
                    </div>
                    <div class="form-group col-md-2">
                        <label>Prix commercial</label>
                        <input type="number" class="form-control" name="prix_commercial" value="{{ isset($editClient) ? $editClient->prix_commercial : '' }}">
                    </div>
                </div>

                <div align="right">
                    <a href="{{ route('produits.index') }}" class="btn btn-outline-danger">Annuler</a>
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-save"></i> {{ isset($editClient) ? 'Mettre à jour' : 'Enregistrer' }}
                    </button>
                </div>
            </form>
        </div>

    </div>



    <div>
        <div id="spinner" class="d-none text-center">
            <div class="spinner-border text-primary text-center" style="width: 4rem; height: 4rem;"
                 role="status">
            </div>
        </div>
        <h2 id="spinner-text"
            class="d-none text-uppercase font-weight-light text-center text-primary mt-2">veuillez
            patienter s'il vous plaît</h2>
    </div>

    <div id="mouvementCharger">

    </div>


@endsection
@section('js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#choisirEntrepot').on('change', function () {
                const inputHidden = document.getElementById('entrepotID');
                let entrepotId = $('select[name="entrepot"]').val();
                let idEntrepot = entrepotId.split(';')[0];
                inputHidden.value = idEntrepot;
                $.ajax({
                    url: "{{route('produits.consulter')}}",
                    type: "POST",
                    cache: false,
                    data: {entrepotId: entrepotId,_token:'{{csrf_token()}}'},
                    beforeSend: function () {
                        $("#mouvementCharger").hide();
                        $('#spinner').removeClass("d-none").show();
                        $('#spinner-text').removeClass("d-none").show();
                    },
                    success: function (response) {
                        $("#mouvementCharger").html(response);
                    },
                    complete: function (response) {
                        $("#mouvementCharger").show();
                        $('#spinner').addClass("d-none");
                        $('#spinner-text').addClass("d-none");
                        $('#choisirEntrepot').prop('disabled', false);
                    }
                });

            }).trigger('change');
        });
    </script>
@endsection
