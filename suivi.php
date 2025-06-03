<?php $pageTitle = "Suivi de dossiers"; ?>
<?php
// Check if the form is submitted
include 'db_connection.php';
include 'session.php';
include 'sidebar.php';

$successMessage = "";
$errorMessage = "";
$privilege = $_SESSION['privilege'];

$privilege = $_SESSION['privilege'];
if ($privilege == 2 || $privilege == 3) {
    // Redirect to an unauthorized access page or display an error message
    header("Location: index.php");
    exit;
}

// Insert data into the database
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Prepare the SQL statement
    $stmt = $connection->prepare("INSERT INTO forms (Bureau, Nom_doss, Associer, Personne_RS, Num_dossier, Projet, Fin_annee, Date_recep, Priorite, Budget_heure, Preparateur, Statut, Date_liv, Revision, Date_rev, Type_dossier) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // Bind the form data and additional data to the SQL statement parameters
    $stmt->bind_param('ssssssssssssssss', $_POST['foffice'], $_POST['doss'], $_POST['associer'], $_POST['personne'], $_POST['num'], $_POST['projet'], $_POST['fin_yrs'], $_POST['recep'], $_POST['priorite'], $_POST['bud'], $_POST['prept'], $_POST['statut'], $_POST['livraison'], $_POST['revision'], $_POST['revisi'], $_POST['type']);
  
    // Execute the SQL statement
    if ($stmt->execute()) {
      $successMessage = "Suivi ajouté avec succès !";
    } else {
      $errorMessage = "Erreur lors de l'ajout du suivi : " . $connection->error;
    }
  
    // Close the statement
    $stmt->close();
}

?>
    <?php
        $sql = "SELECT DISTINCT Name_bureau FROM bureau";
        $result = $connection->query($sql);
        $offices = [];
        while($row = $result->fetch_assoc()) {
            $offices[] = $row['Name_bureau'];
        }

        $sql = "SELECT DISTINCT Associer FROM additional";
        $result = $connection->query($sql);
        $associer = [];
        while($row = $result->fetch_assoc()) {
            $associer[] = $row['Associer'];
        }

        $sql = "SELECT DISTINCT Personne_RS FROM additional";
        $result = $connection->query($sql);
        $personne = [];
        while($row = $result->fetch_assoc()) {
            $personne[] = $row['Personne_RS'];
        }

        $sql = "SELECT DISTINCT Name FROM user";
        $result = $connection->query($sql);
        $revision = [];
        while($row = $result->fetch_assoc()) {
            $revision[] = $row['Name'];
        }

        $sql = "SELECT DISTINCT Name FROM user";
        $result = $connection->query($sql);
        $prept = [];
        while($row = $result->fetch_assoc()) {
            $prept[] = $row['Name'];
        }

        $sql = "SELECT DISTINCT Name_folder FROM folders";
        $result = $connection->query($sql);
        $folders = [];
        while($row = $result->fetch_assoc()) {
            $folders[] = $row['Name_folder'];
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
	  <div id="layoutSidenav_content">
         <main>
            <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
               <div class="container-xl px-4">
                  <div class="page-header-content pt-4">
                     <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                           <h1 class="page-header-title">
                              <div class="page-header-icon"><i data-feather="plus-square"></i></div>
                              Suivi de dossiers
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
						<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" ><br>
						
							<?php if ($successMessage): ?>
								<div class="alert alert-success"><?php echo $successMessage; ?></div>
							<?php endif; ?>
							<?php if ($errorMessage): ?>
								<div class="alert alert-danger"><?php echo $errorMessage; ?></div>
							<?php endif; ?>
						<div class="form-row">                       
                            <div class="col">
								<label for="offi">Bureau :</label>
								<input type="text" class="form-control" id="foffice" name="foffice" 
									autocomplete="off" list="officesList" required>
								<datalist id="officesList">
									<?php foreach ($offices as $office) { ?>
									<option value="<?php echo $office; ?>">
										<?php } ?>
								</datalist>
							</div>
                            <div class="col">
                                <label for="fold">Nom de dossier :</label>
                                <input type="text" class="form-control" id="doss" name="doss" 
									autocomplete="folder" list="foldersList" required>
								<datalist id="foldersList">
									<?php foreach ($folders as $folder) { ?>
									<option value="<?php echo $folder; ?>">
										<?php } ?>
								</datalist>
                            </div>	
						</div><br>
							<div class="form-row">
                            <div class="col">
                            <label for="associe">Associé :</label>
                                <input type="text" class="form-control" id="associer" name="associer" 
                                        autocomplete="asso" list="associerList" required>
                                    <datalist id="associerList">
                                        <?php foreach ($associer as $associe) { ?>
                                        <option value="<?php echo $associe; ?>">
                                            <?php } ?>
                                    </datalist>
                            </div>
							<div class="col">
                                <label for="perso">Personne_RS :</label>
                                <input class="form-control" type="text" id="personne" name="personne" 
                                        autocomplete="personne" list="personneList" required>
                                        <datalist id="personneList">
                                        <?php foreach ($personne as $pers) { ?>
                                        <option value="<?php echo $pers; ?>">
                                            <?php } ?>
                                    </datalist>
                            </div>

							</div><br>
							<div class="form-row">
                            <div class="col">
                                <label for="numd">Numéro de dossier :</label>
                                <input class="form-control" type="text" id="num" name="num" >
                            </div>
							<div class="col">
                                <label for="project">Projet :</label>
                                <input class="form-control" type="text" id="projet" name="projet" >
                            </div>
                            
							</div><br>
                            <div class="form-row">
                            <div class="col">
                                <label for="fann">Fin d'année :</label>
                                <input class="form-control" type="date" id="fin_yrs" name="fin_yrs" >
                            </div>
                            <div class="col">
                                <label for="drecep">Date de réception :</label>
                                <input class="form-control" type="date" id="recep" name="recep" >
                            </div>
                            </div><br>
                            <div class="form-row">
                            <div class="col">
                                <label for="pri">Priorité :</label>
                                <select class="form-control" id="priorite" name="priorite" required>
                                    <option value="Normal">Normal</option>
                                    <option value="Faible">Faible</option>
                                    <option value="Haute">Haute</option>
                                    
                                </select>
                            </div>
                            
                            <div class="col">
                                <label for="budh">Budget d'heures :</label>
                                <input class="form-control" type="text" id="bud" name="bud" >
                            </div>
                            </div><br>
                            <div class="form-row">
                            <div class="col">
                            <label for="associe">Préparateur :</label>
                                <input type="text" class="form-control" id="prept" name="prept" 
                                        autocomplete="prep" list="prepList" required>
                                    <datalist id="prepList">
                                        <?php foreach ($prept as $pre) { ?>
                                        <option value="<?php echo $pre; ?>">
                                            <?php } ?>
                                    </datalist>
                            </div>
							<div class="col">
                                <label for="rev">Réviseur :</label>
                                <input class="form-control" type="text" id="revision" name="revision" 
                                        autocomplete="revi" list="revisionList" required>
                                        <datalist id="revisionList">
                                        <?php foreach ($revision as $revis) { ?>
                                        <option value="<?php echo $revis; ?>">
                                            <?php } ?>
                                    </datalist>
                            </div>

							</div><br>
                            <div class="form-row">
                            <div class="col">
                                <label for="sta">Statut :</label>
                                    <select class="form-control" id="statut" name="statut" required>
                                        <option value="Non commencé">Non commencé</option>
                                        <option value="En cours">En cours</option>
                                        <option value="Préparation terminé">Préparation terminé</option>
                                        <option value="Révision terminé">Révision terminé</option>
                                        <option value="Livré">Livré</option>
                                        <option value="En attente">En attente</option>
                                    </select>
                            </div>
                            <div class="col">
                                <label for="typed">Type de dossier :</label>
                                    <select class="form-control" id="type" name="type" required>
                                        <option value="Normal">Normal</option>
                                        <option value="Faible">Faible</option>
                                        <option value="Compliqué">Compliqué</option>
                                        <option value="Autre">Autre</option>
                                    </select>
                            </div>
                           
                              
                                <input hidden class="form-control" type="date" id="livraison" name="livraison"  >
                            
                            </div><br>
                            <div class="form-row">
                            <div class="col-md-6">
                                
                                <input hidden class="form-control" type="date" id="revisi" name="revisi">
                            </div>
                            </div><br>
								<div class="button-container">
									<button class="btn btn-rounded btn-success">Ajouter</button>
									<button class="btn btn-rounded btn-warning" id="clean-btn">Vider</button>
									<button class="btn btn-rounded btn-danger" onclick="document.location='list_folders.php'">Annuler</button>
								</div>
							</div>
						
						</form>
                </div>
              </div>
            </div>
          </div>
</div>
			
</div>

   
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
      <script>
        $(document).ready(function () {
          $('#clean-btn').on('click', function (e) {
            e.preventDefault();
            $('#foffice').val('');
            $('#doss').val('');
            $('#associer').val('');
            $('#personne').val('');
            $('#num').val('');
            $('#projet').val('');
            $('#fin_yrs').val('');
            $('#recep').val('');
            $('#bud').val('');
            $('#prept').val('');
            $('#revision').val('');
            $('#livraison').val('');
            $('#revisi').val('');
          });
        });
      </script>
     
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
   $(document).ready(function () {
    // Function to set today's date in the specified input
    function setTodayDateInInput(inputId) {
        const today = new Date();
        const formattedDate = today.toISOString().split('T')[0]; // Format: YYYY-MM-DD
        $(inputId).val(formattedDate);
    }

    // Event handler for when "Statut" select input changes
    $('#statut').on('change', function () {
        const selectedStatut = $(this).val();
        
        if (selectedStatut === 'Révision terminé') {
            setTodayDateInInput('#revisi');
        } else if (selectedStatut === 'Préparation terminé') {
            setTodayDateInInput('#livraison');
        }
        // No else condition needed to leave the current value if other options are selected
    });

    // Initial check on page load (if "Statut" has a value)
    const initialStatut = $('#statut').val();
    if (initialStatut === 'Révision terminé') {
        setTodayDateInInput('#revisi');
    } else if (initialStatut === 'Préparation terminé') {
        setTodayDateInInput('#livraison');
    }
});



</script>

</body>
</html>
