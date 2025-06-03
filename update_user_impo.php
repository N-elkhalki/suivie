<?php
include 'db_connection.php';
include 'session.php';


// Check if the necessary POST parameters are provided
if (isset($_POST['id']) && isset($_POST['column']) && isset($_POST['value'])) {
    $id = $_POST['id'];
    $column = $_POST['column'];
    $value = $_POST['value'];

    // Prepare the update statement based on the column
    if ($column === 'Preparateur' || $column === 'Revision') {
        $stmt = $connection->prepare("UPDATE impo SET $column = ? WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param('si', $value, $id);
            $stmt->execute();
            $stmt->close();
            // You can send a success response if needed
            echo 'Success'; // You can customize this message
        } else {
            // Handle the case where the statement preparation failed
            echo 'Error preparing statement'; // You can customize this error message
        }
    } else {
        // Handle the case where the provided column is not valid
        echo 'Invalid column'; // You can customize this error message
    }
} else {
    // Handle the case where the required POST parameters are not provided
    echo 'Invalid parameters'; // You can customize this error message
}

// Close the database connection
$connection->close();
?>










