<?php $pageTitle = "Modifier un bureau"; ?>
<?php
include 'db_connection.php';

include 'session.php';
include 'sidebar.php';


$privilege = $_SESSION['privilege'];
if ($privilege == 3) {
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
                              Modifier un bureau
                           </h1>
                        </div>
                     </div>
                  </div>
               </div>
            </header>
            <div class="container-xl px-4 mt-n10">
               <div class="row">
                  <div class="col">
                     <div class="card mb-4">
                        <div class="card-body">
<?php


// Check if the bureau ID is provided in the URL query parameter
if (isset($_GET['id'])) {
    $bureauId = $_GET['id'];

    // Retrieve the bureau data from the database
    $query = "SELECT * FROM bureau WHERE Id_Bureau = $bureauId";
    $result = mysqli_query($connection, $query);

    // Check if the bureau exists
    if ($result && mysqli_num_rows($result) > 0) {
        $bureauData = mysqli_fetch_assoc($result);
        $bureauName = $bureauData['Name_bureau'];

        // Check if the form is submitted
        if (isset($_POST['submit'])) {
            // Retrieve the updated bureau name from the form
            $updatedbureauName = $_POST['bureau_name'];

            // Prepare the update statement
            $stmt = $connection->prepare('UPDATE bureau SET Name_bureau = ? WHERE Id_bureau = ?');

            // Bind the parameters to the statement
            $stmt->bind_param('si', $updatedbureauName, $bureauId);

            // Execute the update statement
            if ($stmt->execute()) {
                echo '<div class="alert alert-success" role="alert">
                Dossier modifié avec succès.
            </div>';
            } else {
                echo '<div class="alert alert-danger" role="alert">
                Erreur de modification du dossier.
            </div> ' . $connection->error;
            }

            // Close the statement
            $stmt->close();
        }

        // Display the bureau edit form
        echo '<form method="POST" action="editb.php?id=' . $bureauId . '">';
        echo '<div class="form-row">';
        echo '<div class="col">';
        echo '<label for="bureau_name">Nom du dossier :</label><br>';
        echo '<input class="form-control" type="text" name="bureau_name" value="' . $bureauName . '">';
        echo '</div>';
        echo '</div>';
        echo '</br>';
        echo '<div class="form-row">';
        echo '<div class="button-container d-flex justify-content-between">';
        echo '<button class="btn btn-rounded btn-success" type="submit" name="submit">Modifier</button>';
        echo '<button  class="btn btn-rounded btn-danger"  type="button" name="cancel" onclick="redirectToDisplaybureau()">Annuler</button>';
        echo'</div>';
        echo '</form>';
    } else {
        echo 'Dossier introuvable.';
    }
} else {
    echo 'ID de dossier invalide.';
}

// Close the database connection
mysqli_close($connection);
?>
</div>
</div>
</div>
</div>
<script src="bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

</body>
</html>
