<?php
include 'db_connection.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Vérifier si l'email existe
    $stmt = $connection->prepare("SELECT Id_user FROM user WHERE Login = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // L'utilisateur existe, générer un token
        $token = bin2hex(random_bytes(50));
        $expiry = date("Y-m-d H:i:s", strtotime("+1 hour")); // Le token expire dans 1 heure

        // Mettre à jour l'enregistrement de l'utilisateur avec le token et sa date d'expiration
        $update = $connection->prepare("UPDATE user SET password_reset_token = ?, token_expiry = ? WHERE Login = ?");
        $update->bind_param("sss", $token, $expiry, $email);
        $update->execute();

        // Envoyer l'email de réinitialisation
        $to = $email;
        $subject = "Lien de réinitialisation du mot de passe";
        $message = "Voici votre lien de reinitialisation de mot de passe : <a href='https://suivie.osourcing.net/reset_password.php?token=$token'>Reinitialiser le mot de passe</a>";
        $headers = "From: no-reply@osourcing.net\r\n";
        $headers .= "Content-type: text/html\r\n";
        mail($to, $subject, $message, $headers);
        $feedback = "Un lien de réinitialisation du mot de passe a été envoyé à votre email, Le lien expire dans 1 heure";
    } else {
        $feedback = "Email non trouvé.";
    }

    $connection->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Mot de passe oublié - SB Admin Pro</title>
        <link href="css/styles.css" rel="stylesheet" />
        <link rel="icon" type="image/x-icon" href="assets/img/favicon.png" />
        <script data-search-pseudo-elements defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/js/all.min.js" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js" crossorigin="anonymous"></script>
    </head>
    <body class="bg-primary">
        <div id="layoutAuthentication">
            <div id="layoutAuthentication_content">
                <main>
                    <div class="container-xl px-4">
                        <div class="row justify-content-center">
                            <div class="col-lg-5">
                                <div class="card shadow-lg border-0 rounded-lg mt-5">
                                    <div class="card-header justify-content-center"><h3 class="fw-light my-4">Récupération de mot de passe</h3></div>
                                    <div class="card-body">
                                        <div class="small mb-3 text-muted">Entrez votre adresse email et nous vous enverrons un lien pour réinitialiser votre mot de passe.</div>
                                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                            <div class="mb-3">
                                                <label class="small mb-1" for="email">Entrez votre email :</label>
                                                <input class="form-control" id="email" type="email" placeholder="Entrez votre adresse email" name="email" required />
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                                <a class="small" href="Loginpage.php">Retour à la connexion</a>
                                                <input class="btn btn-primary" type="submit" value="Réinitialiser le mot de passe">
                                            </div>
                                        </form>
                                        <?php if(isset($feedback)): ?>
                                            <div class="alert alert-info mt-3">
                                                <?php echo $feedback; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
            <div id="layoutAuthentication_footer">
                <footer class="footer-admin mt-auto footer-dark">
                    <div class="container-xl px-4">
                        <div class="row">
                            <div class="col-md-6 small">Droit d'auteur &copy; Osourcing 2023</div>
                            <div class="col-md-6 text-md-end small">
                                <a href="#!">Politique de confidentialité</a>
                                &middot;
                                <a href="#!">Conditions générales</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
    </body>
</html>
