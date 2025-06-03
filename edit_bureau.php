<?php $pageTitle = "Liste des Bureaux"; ?>
<?php
include 'db_connection.php';
include 'session.php';
include 'sidebar.php';

$privilege = $_SESSION['privilege'];
if ($privilege == 3 ) {
    // Redirect to an unauthorized access page or display an error message
    header("Location: index.php");
    exit;
}
?>
<style>
.form-row {
  display: flex;
  flex-wrap: wrap;
  margin-right: -5px;
  margin-left: -5px;
}

.form-row > .col,
.form-row > [class*="col-"] {
  padding-right: 5px;
  padding-left: 5px;
}
	  </style>
	      <link href="https://unpkg.com/bootstrap-table@1.22.1/dist/bootstrap-table.min.css" rel="stylesheet">
<div id="layoutSidenav_content">
         <main>
            <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
               <div class="container-xl px-4">
                  <div class="page-header-content pt-4">
                     <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                           <h1 class="page-header-title">
                              <div class="page-header-icon"><i data-feather="plus-square"></i></div>
                              Liste des Bureaux
                           </h1>
                        </div>
                     </div>
                  </div>
               </div>
            </header>
            <div class="container-fluid px-4">
			<div class="card mt-n10">
          <!-- ============================================================== -->
          <!-- Start Page Content -->
          <!-- ============================================================== -->
          <div class="row">
            <!-- column -->
            <div class="col-sm-12">
              <div class="card">
                <div class="card-body">
            <?php
            if (isset($_SESSION['username'])) {
                // Retrieve the bureau data from the database
                $query = "SELECT * FROM bureau";
                $result = mysqli_query($connection, $query);

                // Check if any bureau are found
                if ($result && mysqli_num_rows($result) > 0) {
                    ?>
                    <div  style="margin:5px auto;width:80%">
                      <table id="bootstrap-table" class="table table-bordered">
                      <thead>
                          <tr>

                            <th width="100" data-sortable="true">Nom du Bureau</th>
                            <th width="100" data-sortable="true">Action</th>

                          </tr>
                        </thead>
                      <tbody>
                      <?php 
                        // Loop through each bureau and display the data
                        while ($row = mysqli_fetch_assoc($result)) {
                            $Id_Bureau = $row['Id_Bureau'];
                            $bureau = $row['Name_bureau'];

                            // Display the bureau data in the table
                            echo '<tr>';

                            echo '<td>' . $bureau . '</td>';
                            echo '<td><button class="btn btn-rounded btn-success" type="submit"><a href="editb.php?id=' . $Id_Bureau . '"><font color="white">Modifier</font></a></button>';
                            echo '<form action = "delete_bureau_admin.php" method="POST" class="d-inline">';
                            echo '<button class="btn btn-rounded btn-danger" type="submit" name="bureau_delete_btn" value=' . $Id_Bureau . '>Supprimer</button>';
                            echo '</form></td>';
                    
                            echo '</tr>';
                        }

                        echo '</tbody>';
                        echo '</table>';
                        echo '</div>';
                    } else {
                        echo 'Aucun dossier trouvé.';
                    }

                    // Close the database connection
                    mysqli_close($connection);
                } else {
                    echo 'Nom d utilisateur de la session non défini.';
                }
            ?>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.12.1/bootstrap-table.min.js"></script>

<script>
var $table = $('#bootstrap-table');
 $().ready(function(){
	            $table.bootstrapTable({
	                toolbar: ".toolbar",
	                clickToSelect: true,
	                search: true,
	                showToggle: false,
	                showColumns: false,
	                pagination: true,
	                searchAlign: 'left',
	                pageSize: 5,
	                clickToSelect: false,


	                formatShowingRows: function(pageFrom, pageTo, totalRows){
	                    //do nothing here, we don't want to show the text "showing x of y from..."
	                },
	                formatRecordsPerPage: function(pageNumber){
	                    return pageNumber + " rows visible";
	                }
	            });
				$(window).resize(function () {
	                $table.bootstrapTable('resetView');
	            });
 });
</script>

</body>
</html>
