<?php $pageTitle = "Modifier un utilisateur"; ?>
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
                              Ajouter un utilisateur
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

$successMessage = "";
$errorMessage = "";

// Check if the user ID is provided in the URL query parameter
if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    // Check if the form is submitted
    if (isset($_POST['submit'])) {
        $newLogin = $_POST['login'];
        $newPassword = $_POST['password'];
        
        $newPrivilege = $_POST['privilege'];
        $newDate = $_POST['date'];
        $hashedpassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Prepare the update statement
        $stmt = $connection->prepare('UPDATE user SET Login = ?, Password = ?, Privilege = ?, Date = ? WHERE Id_user = ' . $userId);

        // Bind the parameters to the statement
        $stmt->bind_param('ssss', $newLogin, $hashedpassword, $newPrivilege, $newDate);

        // Execute the update statement
        if ($stmt->execute()) {
            $successMessage = "Utilisateur modifié avec succès !";
        } else {
            $errorMessage = "Erreur de modification de l'utilisateur: " . $connection->error;
        }

        // Close the statement
        $stmt->close();
    }

    // Retrieve the user data from the database based on the user ID
    $query = "SELECT * FROM user WHERE Id_user = " . $userId;
    $result = mysqli_query($connection, $query);

    // Check if the user is found
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $Login = $row['Login'];
        $Password = $row['Password'];
       
        $Privilege = $row['Privilege'];
        $Date = $row['Date'];
        // Determine the user privilege text based on the privilege value
        $privilegeText = '';
        if ($Privilege == 1) {
            $privilegeText = 'Admin';
        } elseif ($Privilege == 2) {
            $privilegeText = 'Superviseur';
        } else {
            $privilegeText = 'User';
        }

        // Display the form with pre-filled user data
        
        echo '<form method="post">';
        echo '<div class="form-row">';
        echo '<div class="col">';
        echo '<label for="login">Email :</label>';
        echo '<input class="form-control" type="text" name="login" value="' . $Login . '">';
        echo '</div>';
        echo '<div class="col">';
        echo '<label for="password">Mot de passe :</label>';
        echo '<input class="form-control" type="password" name="password" required>';
        echo '</div>';
        echo '</div>';
        
        echo '<div class="form-row">';
        echo '<div class="col">';
        echo '<label for="datn">Date de naissance :</label>';
        echo '<input class="form-control" type="date" name="date" value="' . $Date . '">';
        echo '</div>';
        echo '<div class="col">';
        echo '<label for="privilege">Privilège:</label><br>';
        echo '<select class="form-control" name="privilege">';
        echo '<option value="1" ' . ($Privilege == 1 ? 'selected' : '') . '>Administrateur</option>';
        echo '<option value="2" ' . ($Privilege == 2 ? 'selected' : '') . '>Superviseur</option>';
        echo '<option value="3" ' . ($Privilege == 3 ? 'selected' : '') . '>Utilisateur</option>';
        echo '</select>';
        echo '</div>';
        echo '</div>';
        echo '</br>';
        echo '<div class="form-group col-md-2">';
        echo '<div class="button-container d-flex justify-content-between">';
        echo '<button class="btn btn-rounded btn-success" type="submit" name="submit">Modifier</button>';
        echo '<button class="btn btn-rounded btn-danger" type="button" name="cancel" onclick="redirectToDisplayUser()">Annuler</button>';
        echo '</div>';
        echo '</div>';
        echo '</form>';
    } else {
        echo 'Utilisateur introuvable.';
    }
}

// Close the database connection
mysqli_close($connection);
?>
                    <?php if ($successMessage): ?>
                        <div class="alert alert-success"><?php echo $successMessage; ?></div>
                    <?php endif; ?>
                    <?php if ($errorMessage): ?>
                        <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
                    <?php endif; ?>
</div>
</div>
</div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>