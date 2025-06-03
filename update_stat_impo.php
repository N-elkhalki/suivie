<?php
include 'db_connection.php';
include 'session.php';

if (isset($_POST['id']) && isset($_POST['status'])) {
    $id = $_POST['id'];
    $newStatus = $_POST['status'];
    
    // Update the database
    $stmt = $connection->prepare('UPDATE impo SET Statut = ? WHERE id = ?');
    $stmt->bind_param('si', $newStatus, $id);
    
    if ($stmt->execute()) {
        echo 'Success'; // Return success response to AJAX request
    } else {
        echo 'Error'; // Return error response to AJAX request
    }
    
    $stmt->close();
    $connection->close();
}
?>
