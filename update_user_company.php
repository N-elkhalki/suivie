<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['username'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if (isset($_POST['company'])) {
    $stmt = $connection->prepare("UPDATE user SET company = ? WHERE Id_user = ?");
    $stmt->bind_param("si", $_POST['company'], $_SESSION['id']);
    
    $success = $stmt->execute();
    
    header('Content-Type: application/json');
    echo json_encode(['success' => $success]);
    
    $stmt->close();
}

$connection->close();
?>