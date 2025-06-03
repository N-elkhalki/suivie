<?php
include 'db_connection.php';
include 'session.php';
include 'sidebar.php';
?>
<?php
$privilege = $_SESSION['privilege'];
if ($privilege == 2 || $privilege == 3) {
    // Redirect to an unauthorized access page or display an error message
    header("Location: index.php");
    exit;
}


$successMessage = "";
$errorMessage = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check if a file is uploaded
    if (isset($_FILES['fileUpload'])) {
        $fileTmpPath = $_FILES['fileUpload']['tmp_name'];
        $fileName = $_FILES['fileUpload']['name'];
        $fileSize = $_FILES['fileUpload']['size'];
        $fileType = $_FILES['fileUpload']['type'];

        // You can add additional checks for file size and type here

        // Process the file
        if ($fileType == 'text/csv' || $fileType == 'text/plain') {
            if (($handle = fopen($fileTmpPath, 'r')) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    // Assuming CSV columns are: name, email, password, privilege, date
                    $name = $data[0];
                    $email = $data[1];
                    $password = password_hash($data[2], PASSWORD_DEFAULT);
                    $privilege = $data[3];
                    $date = $data[4];

                    // SQL query to insert data
                    $sql = "INSERT INTO user (Name, Login, Password, Privilege, Date) VALUES ('$name', '$email', '$password', '$privilege', '$date')";
                    // Execute SQL query
					if ($connection->query($sql) === TRUE) {
						$successMessage = "Utilisateur ajouté avec succès !";
					} else {
						$errorMessage = "Erreur d'ajout de l'utilisateur: " . $connection->error;
					}
                    // Handle success or error
                }
                fclose($handle);
            }
        }
    }
}


// Close the connection
$connection->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un utilisateur</title>

    <link href="css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="vendor/fontawesome-free/css/all.css" rel="stylesheet">
    <style>

.btn-primary{
    background-color: #4CAF50;
    
    
}
</style>
</head>
<body>
        
              <div class="page-breadcrumb">
                <div class="row align-items-center">
                    <div class="col-md-6 col-8 align-self-center">
                        <h3 class="page-title mb-0 p-0">Ajouter un utilisateur</h3>
                    </div>
                </div>
			        </div>
            
            <div class="container-fluid">
            <div class="row">
            <!-- column -->
            <div class="col-sm-12 ">
            
              <div class="card">
              <div class="col-sm-8 ">
                <div class="card-body">
                    <?php if ($successMessage): ?>
                        <div class="alert alert-success"><?php echo $successMessage; ?></div>
                    <?php endif; ?>
                    <?php if ($errorMessage): ?>
                        <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
                    <?php endif; ?>
                    <form id="userForm" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
                      <div class="form-group col-md-4">
						<label for="fileUpload">Upload CSV/TXT File:</label>
						<input class="form-control" type="file" id="fileUpload" name="fileUpload" accept=".csv, .txt">
					</div>
					  
                      <div class="form-group col-md-4">
                      <div class="button-container">
                        <button class="btn btn-rounded btn-success" type="submit">Ajouter</button>
                        <button class="btn btn-rounded btn-danger" type="cancel" name="cancel" onclick="window.location.href='displayuser.php'">Annuler</button>
                      </div>
                    </div>
                
                    </form>
                    </div>
                    </div>
                    </div>
                    </div>
                    </div>
                    </div>
                    </div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>

