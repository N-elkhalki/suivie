<?php
include 'db_connection.php';
include 'session.php';
include 'sidebar.php';

$privilege = $_SESSION['privilege'];

if (isset($_SESSION['username'])) {
  $username = $_SESSION['username'];

  // Prepare the SQL statement to fetch the name from the 'user' table
  $stmt = $connection->prepare('SELECT name FROM user WHERE login = ?');
  $stmt->bind_param('s', $username);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $currentUsername = $row['name'];
  } else {
    // Handle case when username is not found in the 'user' table
    $currentUsername = 'Unknown';
  }

  // Close the statement
  $stmt->close();
} else {
  // Handle case when session username is not set
  $currentUsername = 'Unknown';
}
?>
<title>Liste des dossiers</title>
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.core.min.js" integrity="sha512-UhlYw//T419BPq/emC5xSZzkjjreRfN3426517rfsg/XIEC02ggQBb680V0VvP+zaDZ78zqse3rqnnI5EJ6rxA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<link href="https://unpkg.com/bootstrap-table@1.22.1/dist/bootstrap-table.min.css" rel="stylesheet">
<style>
.table {
    white-space: nowrap;
}

    .table td .comments {
        width: 200px; /* You can adjust this value as needed */
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .status-dropdown {
    border: 1px white;
    background-color: transparent;
}
.preparateur-dropdown {
    border: 1px white;
    background-color: transparent;
}
.revision-dropdown {
    border: 1px white;
    background-color: transparent;
}
</style>
			<div class="page-breadcrumb">
                <div class="row align-items-center">
                    <div class="col-md-6 col-8 align-self-center">
                        <h3 class="page-title mb-0 p-0">Liste des dossiers</h3>
                    </div>
                </div>
			</div>
			<div class="container-fluid">
          <!-- ============================================================== -->
          <!-- Start Page Content -->
          <!-- ============================================================== -->
          <div class="row">
            <!-- column -->
            <div class="col-sm-12">
              <div class="card">
                <div class="card-body">
				<div class="col-md-4 text-right">  </div>
				            <?php
				if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // Prepare the SQL statement to fetch records from the 'impo' table
    if ($privilege == 1) {
        // Admin level: Display all records
        $stmt = $connection->prepare('SELECT * FROM impo');
    } elseif ($privilege == 2 || $privilege == 3) {
        // Other privilege levels: Display records based on specific criteria
        $stmt = $connection->prepare("SELECT * FROM impo WHERE Preparateur = ? OR Reviseur = ?");
        $stmt->bind_param('ss', $username, $username);
    }

    $stmt->execute();
					$result = $stmt->get_result();
					if ($result && mysqli_num_rows($result) > 0) {
				?>
				<table id="bootstrap-table" class="table table-bordered">
					<button id="exporttable" class="btn btn-primary" onclick="exportTableToExcel()">Export</button>

                    <thead >
                        <tr>
                        <th width="100" data-sortable="true">Numero</th>
                        <th data-sortable="true" scope="col">Date</th>
                        <th data-sortable="true" scope="col">Nom</th>
                        <th data-sortable="true" scope="col">Superviseur</th>
                        <th data-sortable="true" scope="col">Préparateur</th>
                        <th data-sortable="true" scope="col">Statut</th>
                        <th data-sortable="true" scope="col">Réviseur</th>
						                        <th data-sortable="true" scope="col">Numéro de pages</th>
						<th data-sortable="true" scope="col">Date de livraison</th>
                        <th data-sortable="true" scope="col">Date de révision</th>
                        <th data-sortable="true" scope="col">Commentaire</th>
						<!-- <th width="10%" class="comments" scope="col">Action</th> -->
                      </tr>
                    </thead>
                    <tbody>
                    <?php 
					while ($row = $result->fetch_assoc()) {

					$id = $row['id'];
                    $Numero = $row['Numero'];
                    $Date = $row['Date'];
                    $Nom = $row['Nom'];
                    $Superviseur = $row['Superviseur'];
                    $Preparateur = $row['Preparateur'];
					$Page = $row['Nr_page'];
                    $Statut = $row['Statut'];
                    $Date_liv = $row['Date_liv'];
                    $Revision = $row['Revision'];
                    $Date_rev = $row['Date_rev'];
                    $Commentaire = $row['commentaire'];
					  // Assuming you have a database connection
                    $prepStmt = $connection->prepare('SELECT * FROM user ORDER BY Name ASC');
                    $prepStmt->execute();
                    $usersResult = $prepStmt->get_result();
                
                    echo '<tr scope="row">';
                    echo '<td>' . $Numero . '</td>';
                    echo '<td>' . $Date . '</td>';
                    echo '<td>' . $Nom . '</td>';
					echo '<td>' . $Superviseur . '</td>';
					
                    if($privilege == 2 || $privilege == 3){
                    echo '<td>' . $Preparateur . '</td>';
					
					
                      // Determine the CSS class for the "Priorité" cell based on the value
                      $statutClass = '';
                      switch ($Statut) {
                        case 'Non commencé':
                          $statutClass = 'badge badge-danger';
                          break;
                        case 'En cours':
                          $statutClass = 'badge badge-info';
                          break;
                        case 'Préparation terminé':
                          $statutClass = 'badge badge-success';
                          break;
                        case 'Révision terminé':
                          $statutClass = 'badge badge-success';
                          break;
                        case 'En attente':
                          $statutClass = 'badge badge-warning';
                          break;
                        default:
                          $statutClass = ''; // No special class for other cases
                      }
                      					
					  echo '<td><span class="' . $statutClass . '">' . $Statut . '</span></td>';
                      
					  echo '<td>' . $Revision . '</td>';
                    } else {
                      echo '<td>';
                    echo '<select class="user-dropdown preparateur-dropdown" data-id="' . $id . '">';
                    // Fetch and populate options from the database 'user' table
                    while ($userRow = $usersResult->fetch_assoc()) {
                        echo '<option value="' . $userRow['Name'] . '"' . ($Preparateur === $userRow['Name'] ? ' selected' : '') . '>' . $userRow['Name'] . '</option>';
                    }
                    echo '</select>';
                    echo '</td>';
                    // Determine the CSS class for the "Priorité" cell based on the value
                    $statutClass = '';
                    switch ($Statut) {
                      case 'Non commencé':
                        $statutClass = 'badge badge-light';
                        break;
                      case 'En cours':
                        $statutClass = 'badge badge-warning';
                        break;
                      case 'Préparation terminé':
                        $statutClass = 'badge badge-success';
                        break;
                      case 'Révision terminé':
                        $statutClass = 'badge badge-info';
                        break;
                      case 'En attente':
                        $statutClass = 'badge badge-danger';
                        break;
                      default:
                        $statutClass = ''; // No special class for other cases
                    }
                    echo '<td><span class="' . $statutClass . '">';
                    echo '<select id="statut" class="status-dropdown" data-id="' . $id . '">';
                    echo '<option value="Non commencé"' . ($Statut === 'Non commencé' ? ' selected' : '') . '>Non commencé</option>';
                    echo '<option value="En cours"' . ($Statut === 'En cours' ? ' selected' : '') . '>En cours</option>';
                    echo '<option value="Préparation terminé"' . ($Statut === 'Préparation terminé' ? ' selected' : '') . '>Préparation terminé</option>';
                    echo '<option value="Révision terminé"' . ($Statut === 'Révision terminé' ? ' selected' : '') . '>Révision terminé</option>';
                    echo '<option value="En attente"' . ($Statut === 'En attente' ? ' selected' : '') . '>En attente</option>';
                    echo '</select>';
                    echo '</td></span>';    
                    echo '<td>';
                    echo '<select class="user-dropdown revision-dropdown" data-id="' . $id . '">';
                    // Fetch and populate options from the database 'user' table
                    $usersResult->data_seek(0); // Reset result set pointer
                    while ($userRow = $usersResult->fetch_assoc()) {
                        echo '<option value="' . $userRow['Name'] . '"' . ($Revision === $userRow['Name'] ? ' selected' : '') . '>' . $userRow['Name'] . '</option>';
                    }
                    echo '</select>';
                    echo '</td>';
					echo '<td>' . $Page . '</td>';
                  }
					echo '<td data-livraison-date>' . $Date_liv . '</td>';
                    echo '<td data-revision-date>' . $Date_rev . '</td>';
					echo '<td data-revision-date>' . $Commentaire . '</td>';
                    echo '</tr>';
                    }
                    echo '</tbody>';
                    echo '</table>';
                    echo '
                    <div class="col-sm-1	text-right">
                      <select id="page-size-select" class="form-select">
                      <option value="5">5</option>
                      <option value="30">30</option>
                      <option value="50">50</option>
                      <option value="100">100</option>
                      </select>
                    </div>
                    ';
                    echo '</div>';
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.core.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.2/FileSaver.min.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.12.1/bootstrap-table.min.js"></script>
<script src="https://unpkg.com/tableexport@5.2.0/dist/js/tableexport.js"></script>
<script>
// Function to update the table with new selected values
function updateTableWithSelectedValues() {
    var table = document.getElementById('bootstrap-table');
    var rows = table.getElementsByTagName('tr');

    // Loop through the rows and update the cells with selected dropdown values
    for (var i = 1; i < rows.length; i++) {
        var row = rows[i];
        var preparateurCell = row.querySelector('.preparateur-dropdown');
        var statutCell = row.querySelector('.status-dropdown');
        var revisionCell = row.querySelector('.revision-dropdown');

        // Update the corresponding cells with the selected dropdown values
        row.cells[10].innerHTML = preparateurCell ? preparateurCell.value : '';
        row.cells[11].innerHTML = statutCell ? statutCell.value : '';
        row.cells[12].innerHTML = revisionCell ? revisionCell.value : '';
    }
}

// Function to trigger the export and auto-refresh
function exportTableToExcelWithSelectedValues() {
    // Update the table with new selected values
    updateTableWithSelectedValues();

    // Load the updated table data
    var table = document.getElementById('bootstrap-table');
    var clonedTable = table.cloneNode(true);

    // Create a new Workbook and add the cloned table to a new sheet
    var wb = XLSX.utils.book_new();
    var ws = XLSX.utils.table_to_sheet(clonedTable);
    XLSX.utils.book_append_sheet(wb, ws, "Sheet 1");

    // Save the file
    XLSX.writeFile(wb, 'TableData.xlsx');

    // Auto-refresh the page after a brief delay (e.g., 3 seconds)
    setTimeout(function() {
        location.reload();
    }, 500); // Change the delay as needed
}

// Event handler for export button click
document.getElementById('exporttable').addEventListener('click', exportTableToExcelWithSelectedValues);

</script>

<script>
  var $table = $('#bootstrap-table');
  var $pageSizeSelect = $('#page-size-select');

  $(document).ready(function() {
    var pageSizeOptions = [5, 10, 25, 50]; // Define the available page size options

    // Initialize the Bootstrap table with custom options
    $table.bootstrapTable({
      toolbar: ".toolbar",
      
      search: true,
      showToggle: false,
      showColumns: false,
      pagination: true,
      searchAlign: 'left',
      pageSize: pageSizeOptions[0], // Set the initial page size
      pageList: pageSizeOptions, // Set the available page size options
      clickToSelect: false,
      formatShowingRows: function(pageFrom, pageTo, totalRows) {
        return ''; // We don't want to show the default text "Showing x of y from..."
      },
      formatRecordsPerPage: function(pageNumber) {
        return pageNumber + " rows visible";
      }
    });

    // Handle the change event of the page size select input
    $pageSizeSelect.on('change', function() {
      var pageSize = parseInt($(this).val());
      $table.bootstrapTable('refreshOptions', {
        pageSize: pageSize
      });
    });

    $(window).resize(function() {
      $table.bootstrapTable('resetView');
    });
  });
  $(document).ready(function() {
  // Event handler for status dropdown changes
  $(document).on('change', '.status-dropdown', function() {
    var newStatus = $(this).val();
    var rowId = $(this).data('id');
    
    // Perform AJAX request to update the database
    $.ajax({
      url: 'update_stat_impo.php', // Replace with the actual file to handle the update
      type: 'POST',
      data: {
        id: rowId,
        status: newStatus
      },
      success: function(response) {
        // Update the UI or show a success message if needed
      },
      error: function(xhr, status, error) {
        console.error(error);
      }
    });
    // Auto-refresh the page after a brief delay (e.g., 3 seconds)
    setTimeout(function() {
        location.reload();
    }, 500); // Change the delay as needed
  });
});

</script>
<script>
$(document).ready(function () {
    // Event handler for user dropdown changes
    $(document).on('change', '.user-dropdown', function () {
        var newValue = $(this).val();
        var rowId = $(this).data('id'); // Define rowId within this scope

        // Determine the column to update based on the class of the dropdown
        var columnName;
        if ($(this).hasClass('preparateur-dropdown')) {
            columnName = 'Preparateur';
        } else if ($(this).hasClass('revision-dropdown')) {
            columnName = 'Revision';
        }

        // Perform AJAX request to update the database
        $.ajax({
            url: 'update_user_impo.php', // Replace with the actual file to handle the update
            type: 'POST',
            data: {
                id: rowId,
                column: columnName,
                value: newValue
            },
            success: function (response) {
                console.log(response);
                if (response === 'success') {
                    console.log('User updated successfully');
                } else {
                    console.error('Error updating user');
                }
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    });
});
// Event handler for status dropdown changes
$(document).on('change', '.status-dropdown', function() {
  var newStatus = $(this).val();
  var rowId = $(this).data('id');
  
  // Determine the cell that corresponds to "Date de livraison"
  var $dateLivCell = $(this).closest('tr').find('[data-livraison-date]');
  
  // Check if the new status is "Préparation terminé"
  if (newStatus === 'Préparation terminé') {
    // Get the current date
    var currentDate = new Date().toISOString().slice(0, 10); // Format: YYYY-MM-DD
    
    // Update the "Date de livraison" cell with the current date
    $dateLivCell.text(currentDate);
    
    // Perform AJAX request to update the database with the new "Date de livraison"
    $.ajax({
      url: 'update_date_de_livraison_impo.php', // Replace with the actual file to handle the update
      type: 'POST',
      data: {
        id: rowId,
        dateLivraison: currentDate
      },
      success: function(response) {
        // Update the UI or show a success message if needed
        console.log('Date de livraison updated successfully');
      },
      error: function(xhr, status, error) {
        console.error(error);
      }
    });
  }
});

// Event handler for status dropdown changes
$(document).on('change', '.status-dropdown', function() {
  var newStatus = $(this).val();
  var rowId = $(this).data('id');
  
  // Determine the cell that corresponds to "Date de révision"
  var $dateRevCell = $(this).closest('tr').find('[data-revision-date]');
  
  // Check if the new status is "Révision terminé"
  if (newStatus === 'Révision terminé') {
    // Get the current date
    var currentDate = new Date().toISOString().slice(0, 10); // Format: YYYY-MM-DD
    
    // Update the "Date de révision" cell with the current date
    $dateRevCell.text(currentDate);
    
    // Perform AJAX request to update the database with the new "Date de révision"
    $.ajax({
      url: 'update_date_de_revision_impo.php', // Replace with the actual file to handle the update
      type: 'POST',
      data: {
        id: rowId,
        dateRevision: currentDate
      },
      success: function(response) {
        // Update the UI or show a success message if needed
        console.log('Date de révision updated successfully');
      },
      error: function(xhr, status, error) {
        console.error(error);
      }
    });
  }
  
});

</script>
</body>
</html>