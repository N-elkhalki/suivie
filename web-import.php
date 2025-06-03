<?php
include 'db_connection.php'; // Ensure this file has the correct database connection details

$messages = [];

function numeroExists($connection, $numero) {
    $stmt = $connection->prepare("SELECT COUNT(*) FROM impo WHERE Numero = ?");
    $stmt->bind_param('i', $numero);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_array();
    return $row[0] > 0;
}

if (isset($_POST['submit'])) {
    if (is_uploaded_file($_FILES['file']['tmp_name'])) {
        $file = fopen($_FILES['file']['tmp_name'], 'r');
        fgetcsv($file, 0, ";"); // Skip the header row

        $stmt = $connection->prepare("INSERT INTO impo (Numero, Nom, Date, Superviseur, Preparateur, Nr_page, Statut, Date_liv, Revision, Date_rev, commentaire) VALUES (?, ?, CURDATE(), ?, ?, ?, ?, ?, ?, ?, ?)");
        $existingNumeros = [];

        while (($row = fgetcsv($file, 0, ";")) !== FALSE) {
            $numero = $row[0];
            if (!numeroExists($connection, $numero)) {
                $nom = $row[4] . ' ' . $row[5];
                $stmt->bind_param('isssisssss', $numero, $nom, $row[8], $row[9], $row[10], $row[11], $row[12], $row[13], $row[14], $row[15]);
                $stmt->execute();
            } else {
                $existingNumeros[] = $numero;
            }
        }

        fclose($file);

        if (count($existingNumeros) > 0) {
            $messages[] = "The following Numero values already exist and were not imported: " . implode(", ", $existingNumeros);
        }
        $messages[] = "CSV file successfully imported!";
    } else {
        $messages[] = "Error: Please upload a CSV file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Import CSV to Database</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>CSV File Import</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <input type="file" name="file" accept=".csv" class="form-control">
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Import</button>
        </form>

        <?php foreach ($messages as $message): ?>
            <div class="alert alert-info mt-4"><?php echo $message; ?></div>
        <?php endforeach; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
