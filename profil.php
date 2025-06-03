<?php $pageTitle = "Modifier le profil"; ?>
<?php
include 'db_connection.php';
ob_start(); // Start output buffering
include 'session.php';
include 'sidebar.php';

$userId = $_SESSION['id'];
$privilege = $_SESSION['privilege'];
$queryProfile = "SELECT Name, Phone_number, Image FROM user WHERE Id_user = '$userId'";
$resultProfile = mysqli_query($connection, $queryProfile);
$userProfile = mysqli_fetch_assoc($resultProfile);

// Check for the 'image_updated' session variable and update the image if necessary
if (isset($_SESSION['image_updated']) && $_SESSION['image_updated'] === true) {
    echo '<script>';
    echo 'const profileImages = document.querySelectorAll(".profile-image");';
    echo 'profileImages.forEach(img => img.src = "' . $_SESSION['new_image_path'] . '");';
    echo '</script>';

    // Reset the session variables to prevent further changes on subsequent reloads
    unset($_SESSION['image_updated']);
    unset($_SESSION['new_image_path']);
}

// Function to handle image upload
function uploadProfileImage($image)
{
    // Check if the image was uploaded without errors
    if ($image['error'] === UPLOAD_ERR_OK) {
        $tmpName = $image['tmp_name'];
        $imageName = $image['name'];
        
        
        // Create the "uploads" folder if it doesn't exist
        if (!file_exists('uploads')) {
            mkdir('uploads', 0777, true);
        }

        // Move the uploaded image to the "uploads" folder with a unique name
        $uploadPath = 'uploads/' . uniqid() . '_' . $imageName;
        move_uploaded_file($tmpName, $uploadPath);

        return $uploadPath;
    }

    return null; // Return null if there was an error during image upload
}

// Check if the form is submitted
if (isset($_POST['submit_ph'])) {
    // Check if an image is uploaded
    if (isset($_FILES['imageInput']) && $_FILES['imageInput']['error'] === UPLOAD_ERR_OK) {
        // Handle the image upload and get the file path
        $imagePath = uploadProfileImage($_FILES['imageInput']);

        if ($imagePath !== null) {
            // Update the user's profile image path in the database
            $query = "UPDATE user SET Image = '$imagePath' WHERE Id_user = $userId";
            $result = mysqli_query($connection, $query);

				if ($result) {
				// Image update successful
				$_SESSION['image_updated'] = true;
				$_SESSION['new_image_path'] = $imagePath;
				
				header('Location: profil.php'); // Redirect to profile.php
				exit;
				}  else {
                // Image update failed
                echo '<script>';
                echo 'alert("Erreur lors de la mise à jour de l image de profil: ' . mysqli_error($connection) . '");';
                echo '</script>';
            }
        } else {
            // Image upload failed
            $errorMessage = "Erreur lors du téléchargement de l image de profil";
        }
    } else {
        // User did not upload a new image
        $errorMessage = "Sélectionnez une image !!";
    }
}

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
  
    // Prepare the SQL statement to fetch the name from the 'user' table
    $stmt = $connection->prepare('SELECT name FROM user WHERE login = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
  
    if ($result && $result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $sessionName = $row['name'];
    } else {
      // Handle case when username is not found in the 'user' table
      $sessionName = 'Unknown';
    }
  
  } else {
    // Handle case when session username is not set
    $sessionName = 'Unknown';
  }
  ob_end_flush(); // End output buffering and send captured output to the browser

?>
<div id="layoutSidenav_content">
                <main>

					 <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
               <div class="container-xl px-4">
                  <div class="page-header-content pt-4">
                     <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                           <h1 class="page-header-title">
                              <div class="page-header-icon"><i data-feather="plus-square"></i></div>
                              Paramètres de Profile
                           </h1>
                        </div>
                     </div>
                  </div>
               </div>
            </header>
                    <!-- Main page content-->
                    <div class="container-xl px-4 mt-4">
                        <!-- Account page navigation-->

                        <hr class="mt-0 mb-4" />
                        <div class="row">
                            <div class="col-xl-4">
                                <!-- Profile picture card-->
                                <div class="card mb-4 mb-xl-0">
                                    <div class="card-header">Photo de Profile</div>
									 <?php if ($userProfile): ?>
                                    <div class="card-body text-center">
										<?php if ($userProfile['Image']): ?>
                                        <!-- Profile picture image-->
                                        <img class="img-account-profile rounded-circle mb-2" src="<?php echo $userProfile['Image']; ?>" alt="" />
										<?php else: ?>
										<img class="img-account-profile rounded-circle mb-2" src="img/default_user_image.jpg" alt="" />
										<?php endif; ?>
                                        <!-- Profile picture help block-->
                                        <!-- Display success and error messages if any -->
								<?php if (isset($successMessage)): ?>
									<div class="alert alert-success"><?php echo $successMessage; ?></div>
								<?php endif; ?>

								<?php if (isset($errorMessage)): ?>
									<div class="alert alert-danger"><?php echo $errorMessage; ?></div>
								<?php endif; ?>
                                <h4 class="profile-name"><?php echo $sessionName; ?></h4>
                                <p class="profile-phone"><?php echo $userProfile['Phone_number']; ?></p>
								<div class="row">
                                <div class="col-md-5 mb-3">
									<form id="uploadForm" enctype="multipart/form-data" method="post">
										<input type="file" id="imageInput" name="imageInput" accept="image/*" style="display: none;">
										<label for="imageInput" class="btn btn-primary">importer</label>
                                </div>
                                        <div class="col-md-5 mb-3">
										<button  type="submit" class="btn btn-success" name="submit_ph">Enregistrer</button>
                                        </div>
                                    </form>
                                        
								</div>
                            <?php else: ?>
                                <p>Aucune information de profil disponible.</p>
                            <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        
            <!-- column -->
            <div class="col-xl-8">
            
              <div class="card mb-4">
              <div class="card-header">Détails du compte</div>
              <div class="card-body">
                  

                    <?php

                    $successMessage = "";
                    $errorMessage = "";

                    // Check if the user ID is provided in the URL query parameter
                    // if (isset($_GET['id'])) {
                        $userId = $_SESSION['id'];
						

                        // Check if the form is submitted
                        if (isset($_POST['submit_data'])) {
                            $newLogin = $_POST['login'];
                            $newPassword = $_POST['password'];
                            
                            $newDate = $_POST['date'];
                            $phoneNumber = $_POST['phone'];
                            $hashedpassword = password_hash($newPassword, PASSWORD_DEFAULT);

                            // Prepare the update statement
                            $stmt = $connection->prepare('UPDATE user SET Login = ?, Password = ?,  Date = ?, Phone_number = ? WHERE Id_user = ' . $userId);

                            // Bind the parameters to the statement
                            $stmt->bind_param('ssss',  $newLogin, $hashedpassword, $newDate, $phoneNumber);

                            // Execute the update statement
                            if ($stmt->execute()) {
                                $successMessage = "Profil modifié avec succès !";
                                
                            } else {
                                $errorMessage = "Erreur de modification du profil: " . $connection->error;
                            }

                            // Close the statement
                            $stmt->close();
                        }

                        // Retrieve the user data from the database based on the user ID
                        // $userId = $_SESSION['id'];
                        $query = "SELECT * FROM user WHERE Id_user = " . $userId;
                        $result = mysqli_query($connection, $query);

                        // Check if the user is found
                        if ($result && mysqli_num_rows($result) > 0) {
                            $row = mysqli_fetch_assoc($result);
                            $Login = $row['Login'];
                            $Password = $row['Password'];
                            $Name = $row['Name'];
                            $Number = $row['Phone_number'];
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
                            echo '<div class="row gx-3 mb-3">';
							echo '<div class="col-md-6">';
                            echo '<label for="login">Email :</label>';
                            echo '<input class="form-control" type="text" name="login" value="' . $Login . '">';
                            echo '</div>';
                            echo '<div class="col-md-6">';
                            echo '<label for="password">Mot de passe :</label>';
                            echo '<input class="form-control" type="password" name="password" required >';
                            echo '</div>';
							echo '</div>';
                            
                            echo '<div class="row gx-3 mb-3">';
							echo '<div class="col-md-6">';
                            echo '<label for="datn">Date de naissance :</label>';
                            echo '<input class="form-control" type="date" name="date" value="' . $Date . '">';
                            echo '</div>';
                            echo '<div class="col-md-6">';
                            echo '<label for="num">Numéro de téléphone :</label>';
                            echo '<input class="form-control" type="text" name="phone" value="' . $Number . '">';
                            echo '</div>';
							echo '</div>';
							echo '<div class="row">';
							echo '<div class="col-md-5 mb-3">';
                            echo '<button class="btn btn-rounded btn-success" type="submit" name="submit_data">Modifier</button>';
                           
                            echo '</div>';
							echo '</div>';
                            echo '</form>';
                        } else {
                            echo 'Utilisateur introuvable.';
                        }
                    // }

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