<?php
$pageTitle = "Importer des dossiers depuis un fichier CSV";
include 'db_connection.php';
include 'session.php';
include 'sidebar.php';

$privilege = $_SESSION['privilege'];
if ($privilege != 1) {
    header("Location: unauthorized_access.php");
    exit;
}

$successMessage = "";
$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES['csv_file'])) {
    $fileName = $_FILES['csv_file']['tmp_name'];
    if ($_FILES['csv_file']['size'] > 0) {
        $file = fopen($fileName, "r");
        while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
            $folder_name = $column[0];

            $stmt = $connection->prepare("INSERT INTO folders (Name_folder) VALUES (?)");
            $stmt->bind_param("s", $folder_name);
            if (!$stmt->execute()) {
                $errorMessage = "Erreur lors de l'importation: " . $connection->error;
                break;
            }
        }
        fclose($file);
        if (empty($errorMessage)) {
            $successMessage = "Dossiers importés avec succès !";
        }
    }
}
?>

<!-- HTML and form for file upload -->
	  <div id="layoutSidenav_content">
         <main>
            <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
               <div class="container-xl px-4">
                  <div class="page-header-content pt-4">
                     <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                           <h1 class="page-header-title">
                              <div class="page-header-icon"><i data-feather="plus-square"></i></div>
                              Ajouter une tâche
                           </h1>
                        </div>
                     </div>
                  </div>
               </div>
            </header>
            <div class="container-fluid px-4">
			<div class="card mt-n10">
          <!-- ============================================================== -->
          <!-- Start Page Content -->
          <!-- ============================================================== -->
          <div class="row">
            <!-- column -->
            <div class="col-sm-12">
              <div class="card">
                <div class="card-body">
    <?php if ($successMessage): ?>
        <div class="alert alert-success"><?php echo $successMessage; ?></div>
    <?php endif; ?>
    <?php if ($errorMessage): ?>
        <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="csv_file" required>
        <button type="submit">Importer</button>
    </form>
</div>