<?php
$pageTitle = "Ajouter un dossier";

include 'db_connection.php';
include 'session.php';
include 'sidebar.php';

$privilege = $_SESSION['privilege'];
if ($privilege == 3) {
    // Redirect to an unauthorized access page or display an error message
    header("Location: index.php");
    exit;
}

$successMessage = "";
$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $folder_name = $_POST["folder-name"];

    // Check if folder name already exists
    $check_stmt = $connection->prepare("SELECT Name_folder FROM folders WHERE Name_folder = ?");
    $check_stmt->bind_param("s", $folder_name);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $errorMessage = "Ce dossier existe déjà !";
    } else {
        // Prepare and bind for insertion
        $insert_stmt = $connection->prepare("INSERT INTO folders (Name_folder) VALUES (?)");
        $insert_stmt->bind_param("s", $folder_name);

        if ($insert_stmt->execute() === TRUE) {
            $successMessage = "Dossier ajouté avec succès !";
        } else {
            $errorMessage = "Erreur d'ajout du dossier: " . $connection->error;
        }

        $insert_stmt->close();
    }

    $check_stmt->close();
}

$connection->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $pageTitle; ?></title>
    <!-- Add your CSS links here -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Add your custom styles here */
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
</head>
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
                                Ajouter un dossier
                            </h1>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <div class="container-fluid px-4">
            <div class="card mt-n10">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <?php if ($successMessage): ?>
                                    <div class="alert alert-success"><?php echo $successMessage; ?></div>
                                <?php endif; ?>
                                <?php if ($errorMessage): ?>
                                    <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
                                <?php endif; ?>
                                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                    <div class="form-row">
                                        <div class="col">
                                            <label for="folder-name">Nom du dossier :</label><br>
                                            <input class="form-control" type="text" id="folder-name" name="folder-name" required>
                                        </div>
                                        <div class="col"></div>
                                    </div>
                                    <br>
                                    <div class="form-row">
                                        <div class="col">
                                            <button class="btn btn-rounded btn-success" type="submit">Ajouter</button>
                                            <button class="btn btn-rounded btn-danger" type="button" onclick="window.location.href='edit_folder.php'">Annuler</button>
                                        </div>
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
<!-- Add your JavaScript links here -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
