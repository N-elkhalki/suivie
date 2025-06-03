<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['username'])) {
    header('Content-Type: application/json');
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

try {
    if (!isset($_GET['id'])) {
        throw new Exception('Request ID is required');
    }

    $stmt = $connection->prepare("
        SELECT r.*, u.Name as employee_name, u.company,
               r.previous_year_credit, r.current_year_credit, r.used_credit,
               r.remaining_credit, r.annual_leave, r.exceptional_leave,
               r.balance, r.hr_approval, r.management_decision
        FROM request_periode r 
        JOIN user u ON r.user_id = u.Id_user 
        WHERE r.id = ?
    ");
    
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // Only allow modification if user is admin and request is pending
        $canModify = $_SESSION['privilege'] == 1 && $row['status'] === 'pending';
        
        // Format dates for JSON
        $row['start_date'] = date('Y-m-d', strtotime($row['start_date']));
        $row['end_date'] = date('Y-m-d', strtotime($row['end_date']));
        $row['return_date'] = date('Y-m-d', strtotime($row['return_date']));
        $row['request_date'] = date('Y-m-d', strtotime($row['request_date']));
        
            echo json_encode($row);
    } else {
        throw new Exception('Request not found');
    }
    
    $stmt->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$connection->close();
?>