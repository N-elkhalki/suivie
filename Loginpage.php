<?php
// 1. Include database connection
include 'db_connection.php';

// 2. Start the session
session_start();

// 3. Set the default content type for the page
header('Content-Type: text/html; charset=utf-8');

// 4. Initialize default values
$errorMessage = "";

// 5. Handle the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["pwd"];

    $stmt = $connection->prepare('SELECT * FROM user WHERE Login = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['Password'])) {
            $_SESSION['username'] = $username;
            $_SESSION['privilege'] = $row['Privilege'];
            $_SESSION['id'] = $row['Id_user'];

            // Redirect based on privilege level
            switch ($_SESSION['privilege']) {
                case 1:
                case 3:
                    header("Location: index.php");
                    break;
                case 2:
                    header("Location: add_folder.php");
                    break;
            }
            exit;
        } else {
            $errorMessage = "Email ou mot de passe invalide !";
        }
    } else {
        $errorMessage = "Email ou mot de passe invalide !";
    }

    $stmt->close();
}

// 6. Close the database connection
$connection->close();
?>

<!-- Rest of the code -->


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Page</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
  <style>
    .blue-button {
      background-color: blue;
      color: white;
    }
    .login-container {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .login-form {
      max-width: 400px;
      width: 100%;
      padding: 20px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    .form-group {
      margin-bottom: 15px;
    }
    .form-control {
      height: 40px;
      font-size: 14px;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="container">
      <div class="row">
        <div class="col-md-12 d-flex justify-content-center">
          <div class="login-form">
            <h2 class="text-center">Login</h2>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
              <label for="user" >Email : </label><br>
              <input type="text" class="form-control" placeholder="Entrez votre email" id="username" name="username" required>
          </div>
              <div class="form-group">
              <label for="mp" >Mot de passe :</label><br>
              <input type="password" class="form-control" placeholder="Entrez votre mot de passe" id="pwd" name="pwd" required>
          </div>
              <?php if ($errorMessage): ?>
                                <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
                            <?php endif; ?>
              <div class="form-group text-center">
              <input class="btn btn-primary blue-button" type="submit" value="Se connecter">
               
              </div>
              <a href="forgot_password.php">Forgot Password?</a>
            </form>
          </div>
              </div>
              </div>
              </div>
              </div>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

</body>
</html>
