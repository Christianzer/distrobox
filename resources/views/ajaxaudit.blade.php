<div class="table-responsive">
    <table class="table table-striped" id="dataTable">
        <thead class="bg-primary">
        <tr class="text-white">
            <th>Acteurs</th>
            <th>Action</th>
            <th>Date</th>
            <th>Heure</th>

        </tr>
        </thead>
        <tbody>
        @foreach($results as $result)
            <tr>


                <td>{{$result->name}}</td>
                <td>{{$result->description}}</td>
                <td>{{date("d-m-Y",strtotime($result->created_at))}}</td>
                <td>{{date("H:i:s",strtotime($result->created_at))}}</td>


            </tr>
        @endforeach
        </tbody>
    </table>
</div>
<script>
    $(document).ready(function () {
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
</script>

