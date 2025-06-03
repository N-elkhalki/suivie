<?php
include 'db_connection.php';
include 'session.php';
include 'sidebar.php';

$successMessage = "";
$errorMessage = "";

// Check user privilege (adjust as needed)
$privilege = $_SESSION['privilege'];
if ($privilege == 3) {
    // Redirect to an unauthorized access page or display an error message
    header("Location: index.php");
    exit;
}

// Function to check if Numero already exists
function numeroExists($connection, $numero) {
    $stmt = $connection->prepare("SELECT COUNT(*) FROM impo WHERE Numero = ?");
    $stmt->bind_param('i', $numero);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_array();
    return $row[0] > 0;
}

// Insert data into the database
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero = $_POST['numero'];

    // Check if Numero already exists
    if (numeroExists($connection, $numero)) {
        $errorMessage = "Erreur: Le numéro existe déjà dans la base de données.";
    } else {
        // Prepare the SQL statement
        $stmt = $connection->prepare("INSERT INTO impo (Numero, Date, Nom, Superviseur, Preparateur, Nr_page, Statut, Revision, commentaire) VALUES (?, CURDATE(), ?, ?, ?, ?, ?, ?, ?)");

        // Bind the form data to the SQL statement parameters
        $stmt->bind_param('isssisss', $numero, $_POST['nom'], $_POST['superviseur'], $_POST['preparateur'], $_POST['nr_page'], $_POST['statut'], $_POST['Revision'], $_POST['commentaire']);

        // Execute the SQL statement
        if ($stmt->execute()) {
            $successMessage = "Enregistrement ajouté avec succès !";
        } else {
            $errorMessage = "Erreur lors de l'ajout de l'enregistrement : " . $connection->error;
        }

        // Close the statement
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi de dossiers</title>
    <link href="css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="vendor/fontawesome-free/css/all.css" rel="stylesheet">
  <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
	  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<style>

    .btn-primary{
        background-color: #4CAF50;
        
        
    }
    
</style>
</head>

<body>
    <?php
        
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

			$preparateurs = [];
			$Revision = [];
			$superviseurs = [];
			$statut = [];

			// Query for Preparateurs
			$sql = "SELECT DISTINCT Name FROM user"; // Adjust the query based on your database structure
			$result = $connection->query($sql);
			while($row = $result->fetch_assoc()) {
				$preparateurs[] = $row['Name'];
			}

			// Query for Revisions
			$sql = "SELECT DISTINCT Name FROM user"; // Adjust the query based on your database structure
			$result = $connection->query($sql);
			while($row = $result->fetch_assoc()) {
				$Revision[] = $row['Name'];
			}
			
				// Query for superviseurs
			$sql = "SELECT DISTINCT Name FROM user"; // Adjust the query based on your database structure
			$result = $connection->query($sql);
			while($row = $result->fetch_assoc()) {
				$superviseurs[] = $row['Name'];
			}

			// Query for statut - assuming you have predefined statuses
			$statut = ['Non commencé', 'En cours', 'Préparation terminé', 'Révision terminé', 'En attente'];


    ?>

            <div class="container-fluid">
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
                            <label for="numero">Numero:</label>
                            <input type="number" class="form-control" id="numero" name="numero" placeholder="Numero" required>
							</div>
                            <div class="col">
                            <label for="nom">Nom:</label>
                            <input type="text" class="form-control" id="nom" name="nom" placeholder="Nom" required>
                        </div>
						</div><br>

                        <div class="form-row">
                            <div class="col">
                            <label for="superviseur">Superviseur:</label>
                            <input type="text" class="form-control" id="superviseur" name="superviseur" list="superviseurList" required>
                            <datalist id="superviseurList">
                                <?php foreach ($superviseurs as $superviseur) { ?>
                                    <option value="<?php echo htmlspecialchars($superviseur); ?>">
                                <?php } ?>
                            </datalist>
							</div>
                            <div class="col">
                            <label for="preparateur">Préparateur:</label>
                            <input type="text" class="form-control" id="preparateur" name="preparateur" list="preparateurList" required>
                            <datalist id="preparateurList">
                                <?php foreach ($preparateurs as $preparateur) { ?>
                                    <option value="<?php echo htmlspecialchars($preparateur); ?>">
                                <?php } ?>
                            </datalist>
                        </div>
						</div><br>

                        <div class="form-row">
							<div class="col">
                            <label for="nr_page">Nr_page:</label>
                            <input type="number" class="form-control" id="nr_page" name="nr_page" placeholder="Nr_page">
							</div>
                            <div class="col">
                            <label for="statut">statut:</label>
                            <select class="form-control" id="statut" name="statut" required>
                                <?php foreach ($statut as $statut) { ?>
                                    <option value="<?php echo htmlspecialchars($statut); ?>"><?php echo htmlspecialchars($statut); ?></option>
                                <?php } ?>
                            </select>
							</div>
						</div><br>

                        <div class="form-row">
							<div class="col">
                            <label for="Revision">Réviseur:</label>
                            <input type="text" class="form-control" id="Revision" name="Revision" list="RevisionList" required>
                            <datalist id="RevisionList">
                                <?php foreach ($Revision as $Revision) { ?>
                                    <option value="<?php echo htmlspecialchars($Revision); ?>">
                                <?php } ?>
                            </datalist>
							</div>
                            <div class="col">
                            <label for="commentaire">Commentaire:</label>
                            <textarea class="form-control" id="commentaire" name="commentaire" placeholder="Commentaire"></textarea>
                        </div>
						</div><br>

                        <div class="button-container">
									<button type="submit" class="btn btn-rounded btn-success">Ajouter</button>
									
						</div>
                    </form>
                </div>
            </div>
        </div>
    </div>

   
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
     
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

</body>
</html>
