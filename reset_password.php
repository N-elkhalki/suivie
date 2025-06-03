<?php
include 'db_connection.php';
session_start();

$response = ['success' => false, 'message' => ''];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_GET['token'];
    $new_password = $_POST['new_password'];
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Validate token and check expiry
    $stmt = $connection->prepare("SELECT Id_user FROM user WHERE password_reset_token = ? AND token_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Token valid, update password
        $update = $connection->prepare("UPDATE user SET Password = ?, password_reset_token = NULL, token_expiry = NULL WHERE password_reset_token = ?");
        $update->bind_param("ss", $hashed_password, $token);
        $update->execute();
        $response['success'] = true;
        $response['message'] = "Password has been reset.";
    } else {
        $response['message'] = "Invalid or expired token.";
    }
    $connection->close();
    echo json_encode($response);
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Réinitialiser le mot de passe</title>
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <link rel="icon" type="image/x-icon" href="assets/img/favicon.png" />
        <script data-search-pseudo-elements defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/js/all.min.js" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js" crossorigin="anonymous"></script></head>
    <body class="bg-primary">
    <div id="layoutAuthentication">
            <div id="layoutAuthentication_content">
                <main>
                    <div class="container-xl px-4">
                        <div class="row justify-content-center">
                            <div class="col-lg-5">
                                <!-- Basic forgot password form-->
                                <div class="card shadow-lg border-0 rounded-lg mt-5">
                                    <div class="card-header justify-content-center"><h3 class="fw-light my-4">Récupération de mot de passe</h3></div>
                                    <div class="card-body">
                                        <div class="small mb-3 text-muted">Entrez votre adresse e-mail et nous vous enverrons un lien pour réinitialiser votre mot de passe.</div>
                                        <!-- Forgot password form-->
                                        <form id="passwordResetForm" method="POST" action="reset_password.php?token=<?php echo $_GET['token']; ?>">
                                            <!-- Form Group (email address)-->
                                             <div class="mb-3">
                                                    <label class="small mb-1" for="newPassword">Nouveau mot de passe</label>
                                                    <input class="form-control" id="newPassword" type="password" name="new_password" required placeholder="Enter le nouveau mot de passe" />
                                                </div>
                                                <div class="mb-3">
                                                    <label class="small mb-1" for="confirmPassword">Confirmer le mot de passe</label>
                                                    <input class="form-control" id="confirmPassword" type="password" required placeholder="Confirmer le nouveau mot de passe" />
                                                </div>
                                                <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                                    <input class="btn btn-primary" type="submit" value="Reset Password">
                                            </div>
                                        </form>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
    </div>
   <script>
$(document).ready(function() {
    $('#passwordResetForm').on('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission

        var newPassword = $('#newPassword').val();
        var confirmPassword = $('#confirmPassword').val();

        // Check if passwords match
        if (newPassword !== confirmPassword) {
            alert('Passwords do not match.');
            return;
        }

        // Check for password strength
        if (!isPasswordStrong(newPassword)) {
            alert('Le mot de passe doit comporter au moins 8 caractères et contenir un chiffre');
            return;
        }

        // If the password is strong and matches, proceed with AJAX submission
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json', // Expect a JSON response
            success: function(response) {
                if(response.success) {
                    // Replace the form with the success message
                    $('#layoutAuthentication_content').html('<div class="container-xl px-4"><div class="row justify-content-center"><div class="col-lg-5"><div class="card shadow-lg border-0 rounded-lg mt-5"><div class="card-body text-center p-5"><div class="spinner-border mb-3" role="status"><span class="visually-hidden">Chargement...</span></div><p class="mb-0">' + response.message + '</p><p class="mb-0">Vous rediriger vers la page de connexion...</p></div></div></div></div></main>');

                    // Redirect after 30 seconds
                    setTimeout(function() {
                        window.location.href = 'Loginpage.php';
                    }, 3000);
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Error resetting password. Please try again.');
            }
        });
    });

    function isPasswordStrong(password) {
        // Example: Password should be at least 8 characters long and contain a number
        var strongRegex = new RegExp("^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9]+){8,}$");
        return strongRegex.test(password);
    }
});
</script>



</body>
</html>
