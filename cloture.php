
<?php $pageTitle = "Clôturer la période"; ?>
<?php
include 'db_connection.php';
include 'session.php';
include 'sidebar.php';

$privilege = $_SESSION['privilege'];
if ($privilege == 2 || $privilege == 3) {
    // Redirect to an unauthorized access page or display an error message
    header("Location: index.php");
    exit;
}

$successMessage = "";
$errorMessage = "";
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['startDate']) && isset($_POST['endDate'])) {
    $startDate = new DateTime($_POST['startDate']);
    $endDate = new DateTime($_POST['endDate']);

    // Validate that startDate is not after endDate
    if ($startDate > $endDate) {
        $errorMessage = "La date de début ne peut pas être postérieure à la date de fin !!";
    }
    else{
    // Prepared statement to insert data
    $stmt = $connection->prepare("INSERT INTO date_entries (date) VALUES (?)");
    
    while ($startDate <= $endDate) {
        $dateToInsert = $startDate->format('Y-m-d');
        
        $stmt->bind_param("s", $dateToInsert);
        if (!$stmt->execute()) {
            $errorMessage = "Erreur lors de l'insertion de la date $dateToInsert: " . $stmt->error;
        }

        // Move to the next date
        $startDate->modify('+1 day');
    }
    
    $successMessage = "Dates ajoutées avec succès.";
    $stmt->close();
}
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
                              Clôture de la période
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
                              <form method="POST" action="" class="user" >
                                 <div class="form-group row">
                                    <div class="col-sm-3 mb-3 mb-sm-0">
                                       <input type="date" class="form-control form-control-user" id="startDate" name="startDate">
                                    </div>
                                    <div class="col-sm-3 mb-3 mb-sm-0">
                                       <input type="date" class="form-control form-control-user" id="endDate" name="endDate">
                                    </div>
                                    <div class="col-sm-3 mb-3 mb-sm-0">
                                       <button type="submit" class="btn btn-rounded btn-success">Entrer</button>
                                    </div>
                                 </div>
                                 <?php if ($successMessage): ?>
                                       <div class="alert alert-success"><?php echo $successMessage; ?></div>
                                 <?php endif; ?>
                                 <?php if ($errorMessage): ?>
                                       <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
                                 <?php endif; ?>
                              </form>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </body>
</html>
