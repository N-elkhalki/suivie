<?php
// Include the database connection file (db_connection.php)
include 'db_connection.php';
include 'session.php';

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the dossier ID and the new date value from the request
    $id = $_POST['id'];
    $newDateRevision = $_POST['dateRevision'];

    // Prepare the update query
    $updateQuery = "UPDATE forms SET Date_rev = ? WHERE id = ?";
    $stmt = $connection->prepare($updateQuery);

    if ($stmt === false) {
        die("Error: Unable to prepare the update query.");
    }

    // Bind parameters and execute the query
    $stmt->bind_param('si', $newDateRevision, $id);
    $success = $stmt->execute();

    // Check if the update was successful
    if ($success) {
        // Return a success response to the client
        echo "success";
    } else {
        // Return an error response to the client
        echo "error";
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$connection->close();
?>
