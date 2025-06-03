<?php
include 'db_connection.php';
include 'session.php';

if (isset($_POST['folder_id']) && isset($_POST['delivery_date'])) {
    $Id_folder = $_POST['folder_id'];
    $delivery_date = $_POST['delivery_date'];

    // Update the Statut and Livred columns for the matching Folder in the formulaire table
    $query = "UPDATE formulaire SET Statut = 'dossier livré', Livred = ? WHERE Folder = (SELECT Name_folder FROM folders WHERE Id_folder = ?)";
    if ($stmt = mysqli_prepare($connection, $query)) {
        mysqli_stmt_bind_param($stmt, "si", $delivery_date, $Id_folder);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    mysqli_close($connection);

    // Return a successful response
    echo json_encode(['status' => 'success']);
    exit;
} else {
    // Return an error response
    echo json_encode(['status' => 'error']);
    exit;
}
?>
