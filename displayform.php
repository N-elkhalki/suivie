<?php $pageTitle = "Liste des formulaires"; ?>
<?php
include 'db_connection.php';
include 'session.php';
include 'sidebar.php';


?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des formulaires</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
</head>
<body>
<div id="layoutSidenav_content">
    <main>
        <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
            <div class="container-xl px-4">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i data-feather="plus-square"></i></div>
                                Liste des formulaires
                            </h1>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <div class="container-fluid px-4">
            <div class="card mt-n10">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="col-md-4 text-right"></div>
                                <?php
                                if (isset($_SESSION['username'])) {
                                    if ($privilege == 1) {
                                        $stmt = $connection->prepare('SELECT * FROM formulaire');
                                    } else {
                                        $stmt = $connection->prepare('SELECT * FROM formulaire WHERE username = ?');
                                        $stmt->bind_param('s', $_SESSION['username']);
                                    }
                                    $stmt->execute();

                                    $result = $stmt->get_result();
                                    if ($result && mysqli_num_rows($result) > 0) {
                                        ?>
                                        <table id="datatablesSimple" class="table table-striped">
                                            <button id="exporttable" class="btn btn-primary" onclick="exportTableToExcel()">Export</button>
                                            <thead>
                                            <tr>
                                                <th>Nom</th>
                                                <th>Date</th>
                                                <th>Bureau</th>
                                                <th>Dossier</th>
                                                <th>Infos</th>
                                                <th>Taches</th>
                                                <th>Heures de travail</th>
                                                <th>Commentaires</th>
                                                <th>Statut</th>
                                                <th>Date de Livraison</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            while ($row = $result->fetch_assoc()) {
                                                echo '<tr>';
                                                echo '<td>' . $row['Name'] . '</td>';
                                                echo '<td>' . $row['Date'] . '</td>';
                                                echo '<td>' . $row['Office'] . '</td>';
                                                echo '<td>' . $row['Folder'] . '</td>';
                                                echo '<td>' . $row['Infos'] . '</td>';
                                                echo '<td>' . $row['Tasks'] . '</td>';
                                                echo '<td>' . $row['Work_hours'] . '</td>';
                                                echo '<td>' . $row['Commentaires'] . '</td>';
                                                echo '<td>' . $row['Statut'] . '</td>';
                                                echo '<td>' . $row['Livred'] . '</td>';
                                                echo '<td>
                                                        <form action="delete_forms.php" method="POST" class="d-inline">
                                                            <button class="btn btn-rounded btn-danger" type="submit" name="forms_delete_btn" value="' . $row['Id_formulaire'] . '" onclick="return confirmDelete()">Supprimer</button>
                                                        </form>
                                                      </td>';
                                                echo '</tr>';
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                        <?php
                                    } else {
                                        echo 'Aucun formulaire trouvé.';
                                    }
                                    $stmt->close();
                                    $connection->close();
                                } else {
                                    echo 'Nom d utilisateur de la session non défini.';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<link href="https://cdn.datatables.net/v/bs5/dt-2.0.7/af-2.7.0/b-3.0.2/b-html5-3.0.2/r-3.0.2/datatables.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/v/bs5/dt-2.0.7/af-2.7.0/b-3.0.2/b-html5-3.0.2/r-3.0.2/datatables.min.js"></script>

<script>
function exportTableToExcel() {
    var table = document.getElementById('datatablesSimple');
    var wb = XLSX.utils.table_to_book(table, {sheet: "Sheet 1"});
    XLSX.writeFile(wb, 'TableData.xlsx');
}

function confirmDelete() {
    return confirm('Are you sure you want to delete this record?');
}

document.addEventListener('DOMContentLoaded', function() {
    const dataTable = new simpleDatatables.DataTable("#datatablesSimple", {
        perPage: 1000,
        perPageSelect: [1000, 5000, 20000]
    });
});

$(document).ready(function() {
    $.fn.dataTable.moment('DD/MM/YYYY');

    $('#datatablesSimple').DataTable({
        pageLength: 1000,
        lengthMenu: [1000, 5000, 20000],
        columnDefs: [
            { type: 'date', targets: 1 }
        ]
    });
});

$.fn.dataTable.moment = function ( format, locale ) {
    var types = $.fn.dataTable.ext.type;
    types.detect.unshift(function (d) {
        return moment(d, format, locale, true).isValid() ? 'moment-'+format : null;
    });
    types.order[ 'moment-'+format+'-pre' ] = function (d) {
        return moment(d, format, locale, true).unix();
    };
};
</script>
</body>
</html>
