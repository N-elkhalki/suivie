<?php $pageTitle = "Ajouter un associé"; ?>
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
?>
<?php
// Initialize variables for success and error messages
$successMessage = "";
$errorMessage = "";

// If the form data is received, perform the office insertion
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $associer = $_POST["associe"];
    $pers = $_POST["pers"];

    // Perform the database insertion query
    $sql = "INSERT INTO additional (Associer, Personne_RS) VALUES ('$associer','$pers')";
    if ($connection->query($sql) === TRUE) {
        $successMessage = "Informations ajouté avec succès !";
    } else {
        $errorMessage = "Erreur d'ajout des informations : " . $connection->error;
    }
}

// Close the connection
$connection->close();
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
<div id="layoutSidenav_content">
         <main>
            <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
               <div class="container-xl px-4">
                  <div class="page-header-content pt-4">
                     <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                           <h1 class="page-header-title">
                              <div class="page-header-icon"><i data-feather="plus-square"></i></div>
                              Ajouter un associé ou une personne-ressource
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
                    <?php if ($successMessage): ?>
                        <div class="alert alert-success"><?php echo $successMessage; ?></div>
                    <?php endif; ?>
                    <?php if ($errorMessage): ?>
                        <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
                    <?php endif; ?>
                    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <div class="form-row">
						<div class="col">
                            <label for="associer">Associé :</label><br>
                            <input class="form-control" type="text" id="associe" name="associe" required>
                        </div>
                        <div class="col">
                            <label for="rs">Personne-Ressource :</label><br>
                            <input class="form-control" type="text" id="pers" name="pers" required>
                        </div>
						</div>
						</br>
                        <div class="form-row">
						<div class="col">
                            <button class="btn btn-rounded btn-success" type="submit">Ajouter</button>
                        </div>
						</div>
                    </form>
                </div>
            </div>
        </div>
        </div>
        </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
