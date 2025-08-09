@extends('layout')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active text-uppercase" >Piste d'Audit</li>
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
    <br>
    <div class="card shadow mb-2">
        <form action="" method="POST" id="formID">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-3">
                        <label for="" class="col-form-label">Chosir la date</label>
                        <input type="date" class="form-control" required max="<?php echo date('Y-m-d'); ?>" value="<?php echo date('Y-m-d'); ?>" name="dateDebut" id="dateDebut">
                    </div>
                </div>

                <div align="right">
                    <input type="submit" name="typeInfo" value="consulter" class="btn btn-primary text-uppercase">
                </div>
            </div>
        </form>
    </div>

    <div class="card shadow mb-5">
        <div class="card-header bg-primary py-3">
            <span class="m-0 font-weight-bold text-white ">Listes des actions des utilisateurs</span>
        </div>
        <div class="card-body">
            <!-- spinner -->
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

            <!-- position tableau de donnée -->
            <div id="table-data-audit">
            </div>

        </div>
    </div>


@endsection

@section('js')
    <script type="text/javascript">
        $(document).ready(function() {
                      $('#dataTable').DataTable({
                "ordering": false,
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

        $(function(){
            $('#formID').submit(function(e){
                e.preventDefault();
                var dateDebut = $("#dateDebut").val();
                $.ajax({
                    url: "{{route('req_audit')}}",
                    type: "POST",
                    cache: false,
                    data: {dateDebut: dateDebut,_token:'{{csrf_token()}}'},
                    beforeSend: function () {
                        $("#table-data-audit").hide();
                        $('#spinner').removeClass("d-none").show();
                        $('#spinner-text').removeClass("d-none").show();
                    },
                    success: function (response) {
                        $("#table-data-audit").html(response);
                    },
                    complete: function (response) {
                        $("#table-data-audit").show();
                        $('#spinner').addClass("d-none");
                        $('#spinner-text').addClass("d-none");
                    }
                });
            });
        });

    </script>
@endsection
