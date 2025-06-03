<?php
include 'db_connection.php';
include 'session.php';
include 'sidebar.php';

$privilege = $_SESSION['privilege'];
?>
<title>Liste des congés</title>
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
</style>
			<div class="page-breadcrumb">
                <div class="row align-items-center">
                    <div class="col-md-6 col-8 align-self-center">
                        <h3 class="page-title mb-0 p-0">Liste des congés</h3>
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
					// Prepare the SQL statement
					if ($privilege == 1) {
						// Level 1 user: Load all records
						$stmt = $connection->prepare('SELECT Demande_id,today_date,Nom FROM demande');
					} else {
						// Other users: Load only their own records
						$stmt = $connection->prepare('SELECT Demande_id,today_date,Nom FROM demande WHERE username = ?');
						$stmt->bind_param('s', $_SESSION['username']);
					}
					$stmt->execute();

					$result = $stmt->get_result();
					if ($result && mysqli_num_rows($result) > 0) {
			?>
					 <table id="bootstrap-table" class="table table-bordered">			 
					 <thead >
					  <tr>
						<th width="100" data-sortable="true">Nom</th>
						<th data-sortable="true" scope="col">Date</th>
							
						<th width="10%" class="comments" scope="col">Action</th>
							
					  </tr>
					</thead>
					  <tbody>
					<?php 
					while ($row = $result->fetch_assoc()) {
					$id_dem = $row['Demande_id'];
					$Name = $row['Nom'];
					$Date = $row['today_date'];

					echo '<tr scope="row">';
					echo '<td>' . $Name . '</td>';
					echo '<td>' . $Date . '</td>';
					echo '<td><a href="conge.php?id=' . $id_dem . '" target="_blank" class="btn btn-rounded btn-danger">Imprimer</a></td>';
								echo '</tr>';
								}
								echo '</tbody>';
								echo '</table>';
								echo '</div>';
							} else {
								echo 'Aucun congé trouvé.';
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
</script>


</body>
</html>
