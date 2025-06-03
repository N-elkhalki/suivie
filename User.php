<?php $pageTitle = "Ajouter un utilisateur"; ?>
<?php
include 'db_connection.php';
include 'session.php';
include 'sidebar.php';

// Load Composer's autoloader (if using Composer)
// Include PHPMailer classes
require_once 'PHPMailer/src/Exception.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
?>
<?php
$privilege = $_SESSION['privilege'];
if ($privilege == 2 || $privilege == 3) {
    header("Location: index.php");
    exit;
}

$successMessage = "";
$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $privilege = $_POST["privilege"];
    $date = $_POST["date"];

    $hashedpassword = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO user (Name, Login, Password, Privilege, Date) VALUES ('$name', '$email', '$hashedpassword','$privilege','$date')";
    
    if ($connection->query($sql) === TRUE) {
        // Send email using PHPMailer
        $mail = new PHPMailer(true);
        
        try {
            // Server settings
                    $mail->isSMTP();
                    $mail->Host = 'mail.osourcing.net';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'no-reply@osourcing.net';
                    $mail->Password = 'Osourcing@123';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

            // Sender & Recipient
            $mail->setFrom('no-reply@osourcing.net', 'Identifiant de demande de conge');
            $mail->addAddress($email, $name);

            // Email Content
            $mail->isHTML(true);
            $mail->Subject = 'Vos informations de connexion';
            $mail->Body    = "
                <h2>Bonjour $name,</h2>
                <p>Votre compte a été créé avec succès.</p>
                <p><strong>Informations de connexion :</strong></p>
                <ul>
                    <li><strong>Email :</strong> $email</li>
                    <li><strong>Mot de passe :</strong> $password</li>
                </ul>
                <p>Connectez-vous ici : <a href='https://suivie.osourcing.net/'>https://suivie.osourcing.net/</a></p>
                <p>Cordialement,<br>L'équipe d'administration</p>
            ";
            $mail->AltBody = "Bonjour $name,\n\nVotre compte a été créé.\n\nEmail: $email\nMot de passe: $password\n\nConnectez-vous: https://suivie.osourcing.net/";

            $mail->send();
            $successMessage = "Utilisateur ajouté avec succès et email envoyé !";
        } catch (Exception $e) {
            $successMessage = "Utilisateur ajouté, mais l'email n'a pas pu être envoyé. Erreur: " . $mail->ErrorInfo;
        }
    } else {
        $errorMessage = "Erreur d'ajout de l'utilisateur: " . $connection->error;
    }
}

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
                    <?php if ($successMessage): ?>
                        <div class="alert alert-success"><?php echo $successMessage; ?></div>
                    <?php endif; ?>
                    <?php if ($errorMessage): ?>
                        <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
                    <?php endif; ?>
                    <form id="userForm" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                      <div class="form-row">
                              <div class="col">
								<label for="name">Nom :</label>
								<input class="form-control" type="text" id="name" name="name" required>
							  </div>
                              <div class="col">
								<label for="dat">Date de naissance :</label>
								<input class="form-control" type="date" placeholder="Date" id="date" name="date" required>
							  </div>
                      </div><br>
                      <div class="form-row">
                              <div class="col">
								<label for="email">Email :</label>
								<input class="form-control" type="email" id="email" name="email" required>
								</div>
                              <div class="col">
								<label for="privilege">Privilège :</label><br>
								<select class="form-control" name="privilege" id="privilege">
								  <option value="1">Administrateur</option>
								  <option value="2">Superviseur</option>
								  <option value="3">Utilisateur</option>
								</select>
							</div>
						</div><br>
                      <div class="form-row">
                              <div class="col">
								<label for="password">Mot de passe :</label>
								<input class="form-control" type="password" id="password" name="password" required>
								</div>
                              <div class="col">
								</div>
					</div><br>
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

