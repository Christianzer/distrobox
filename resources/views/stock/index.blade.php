@extends('layout')
@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item text-primary font-weight-bold text-uppercase active" aria-current="page">Gestion du stock</li>
        </ol>
    </nav>



    <div class="d-flex justify-content-end align-items-baseline">
        <h6 class="text-gray-800 mr-2 text-uppercase">Entrepots</h6>
        <div class="mr-2">
            <select class="custom-select text-uppercase" name="entrepot" id="choisirEntrepot">
                @foreach($entrepots as $entrepot)
                    <option
                        @if(session('entrepotID') == $entrepot->id_entrepot) selected @endif
                        value="{{ $entrepot->id_entrepot.';'.$entrepot->nom }}">
                        {{ $entrepot->nom }}
                    </option>
                @endforeach
            </select>
        </div>

    </div>



    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show mt-1" role="alert">
            {{ session()->get('message') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show mt-1" role="alert">
            {{ session()->get('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif



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
                let entrepotId = $('select[name="entrepot"]').val();
                $.ajax({
                    url: "{{route('stock.consulter')}}",
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


