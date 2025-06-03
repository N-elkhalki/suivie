<?php
session_start();
require_once 'db_connection.php';

// Include PHPMailer classes
require_once 'PHPMailer/src/Exception.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

if (!isset($_SESSION['username']) || $_SESSION['privilege'] != 1) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    if (!isset($_POST['id'])) {
        throw new Exception('Missing request ID');
    }

    // Get request and user details first
    $detailsStmt = $connection->prepare("
        SELECT r.*, u.Name as employee_name, u.Login as employee_email 
        FROM request_periode r 
        JOIN user u ON r.user_id = u.Id_user 
        WHERE r.id = ?
    ");
    $detailsStmt->bind_param("i", $_POST['id']);
    $detailsStmt->execute();
    $requestDetails = $detailsStmt->get_result()->fetch_assoc();

    // Handle approval/rejection action
    if (isset($_POST['action'])) {
        $stmt = $connection->prepare("
            UPDATE request_periode 
            SET status = ?
            WHERE id = ? AND status = 'pending'
        ");

        $status = $_POST['action'] === 'approve' ? 'approved' : 'rejected';
        $stmt->bind_param("si", $status, $_POST['id']);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                // Send email notification to employee
                try {
                    $mail = new PHPMailer(true);
                    
                    // Server settings
                    $mail->isSMTP();
                    $mail->Host = 'mail.osourcing.net';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'no-reply@osourcing.net';
                    $mail->Password = 'Osourcing@123';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;
                    
                    // Recipients
                    $mail->setFrom('no-reply@osourcing.net', 'Système de Gestion des Congés');
                    $mail->addAddress($requestDetails['employee_email']);
                    
                    // Content
                    $mail->isHTML(true);
                    $mail->CharSet = 'UTF-8';
                    
                    $requestType = $requestDetails['request_type'] === 'conge' ? 'Congé' : 'Autorisation d\'absence';
                    $mail->Subject = "Décision de demande de congé";
                    
                    // Format dates
                    $startDate = date('d/m/Y', strtotime($requestDetails['start_date']));
                    $endDate = date('d/m/Y', strtotime($requestDetails['end_date']));
                    $returnDate = date('d/m/Y', strtotime($requestDetails['return_date']));
                    
                    $statusText = $status === 'approved' ? 'APPROUVÉE' : 'REFUSÉE';
                    $statusColor = $status === 'approved' ? '#28a745' : '#dc3545';
                    
                    $mail->Body = "
                        <html>
                        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
                            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                                <h2 style='color: {$statusColor};'>Votre demande a été {$statusText}</h2>
                                
                                <div style='background-color: #f7fafc; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                                    <p><strong>Employé:</strong> {$requestDetails['employee_name']}</p>
                                    <p><strong>Type:</strong> {$requestType} {$requestDetails['leave_type']}</p>
                                    <p><strong>Période:</strong> Du {$startDate} au {$endDate}</p>
                                    <p><strong>Date de reprise:</strong> {$returnDate}</p>
                                    <p><strong>Nombre de jours:</strong> {$requestDetails['leave_days']}</p>
                                </div>
                                
                                <div style='text-align: center; padding: 15px; background-color: {$statusColor}; color: white; border-radius: 5px;'>
                                    <h3 style='margin: 0;'>Statut: {$statusText}</h3>
                                </div>
                            </div>
                        </body>
                        </html>";
                    
                    $mail->send();
                    echo json_encode(['success' => true, 'message' => 'Status updated and notification sent']);
                } catch (Exception $e) {
                    error_log("Email sending failed: " . $e->getMessage());
                    echo json_encode(['success' => true, 'message' => 'Status updated but notification failed']);
                }
            } else {
                echo json_encode(['error' => 'No changes made. Request may already be processed.']);
            }
        } else {
            throw new Exception('Failed to update status');
        }
    }
    // Handle saving management data
    else if (isset($_POST['saveData'])) {
        $stmt = $connection->prepare("
            UPDATE request_periode 
            SET previous_year_credit = ?,
                current_year_credit = ?,
                used_credit = ?,
                remaining_credit = ?,
                annual_leave = ?,
                exceptional_leave = ?,
                balance = ?,
                hr_approval = ?,
                management_decision = ?
            WHERE id = ? AND status = 'pending'
        ");
        
        $stmt->bind_param(
            "sssssssssi",
            $_POST['previousYearCredit'],
            $_POST['currentYearCredit'],
            $_POST['usedCredit'],
            $_POST['remainingCredit'],
            $_POST['annualLeave'],
            $_POST['exceptionalLeave'],
            $_POST['balance'],
            $_POST['hrApproval'],
            $_POST['managementDecision'],
            $_POST['id']
        );

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                // Send email notification
                try {
                    $mail = new PHPMailer(true);
                    
                    // Server settings
                    $mail->isSMTP();
                    $mail->Host = 'mail.osourcing.net';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'no-reply@osourcing.net';
                    $mail->Password = 'Osourcing@123';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;
                    
                    // Recipients
                    $mail->setFrom('no-reply@osourcing.net', 'Système de Gestion des Congés');
                    $mail->addAddress('nelkhalki@gmail.com');

                    
                    // Content
                    $mail->isHTML(true);
                    $mail->CharSet = 'UTF-8';
                    
                    $requestType = $requestDetails['request_type'] === 'conge' ? 'Congé' : 'Autorisation d\'absence';
                    $mail->Subject = "Mise à jour des données administratives - {$requestDetails['employee_name']}";
                    
                    // Format dates
                    $startDate = date('d/m/Y', strtotime($requestDetails['start_date']));
                    $endDate = date('d/m/Y', strtotime($requestDetails['end_date']));
                    $returnDate = date('d/m/Y', strtotime($requestDetails['return_date']));
                    
                    $mail->Body = "
                        <html>
                        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
                            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                                <h2 style='color: #2c5282;'>Nouvelle Demande de {$requestType}</h2>
                                
                                <div style='background-color: #f7fafc; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                                    <p><strong>Employé:</strong> {$requestDetails['employee_name']}</p>
                                    <p><strong>Type:</strong> {$requestType} {$requestDetails['leave_type']}</p>
                                    <p><strong>Période:</strong> Du {$startDate} au {$endDate}</p>
                                    <p><strong>Date de reprise:</strong> {$returnDate}</p>
                                    <p><strong>Nombre de jours:</strong> {$requestDetails['leave_days']}</p>
                                </div>
                                
                                <div style='background-color: #f7fafc; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                                    <h3 style='color: #2c5282;'>Partie Administrative:</h3>
                                    <p><strong>Acquis N-1:</strong> {$_POST['previousYearCredit']}</p>
                                    <p><strong>Acquis N:</strong> {$_POST['currentYearCredit']}</p>
                                    <p><strong>Pris:</strong> {$_POST['usedCredit']}</p>
                                    <p><strong>Reste:</strong> {$_POST['remainingCredit']}</p>
                                    <p><strong>Annuel:</strong> {$_POST['annualLeave']}</p>
                                    <p><strong>Exceptionnel:</strong> {$_POST['exceptionalLeave']}</p>
                                    <p><strong>Solde:</strong> {$_POST['balance']}</p>
                                    <p><strong>Visa RH:</strong> {$_POST['hrApproval']}</p>
                                    <p><strong>Décision Direction:</strong> {$_POST['managementDecision']}</p>
                                </div>
                                
                                <p style='color: #718096; font-size: 0.9em;'>
                                    Cette demande nécessite votre attention. Veuillez vous connecter au système de gestion des congés pour l'examiner.
                                </p>
                            </div>
                        </body>
                        </html>";
                    
                    $mail->send();
                    echo json_encode(['success' => true, 'message' => 'Data saved and notification sent']);
                } catch (Exception $e) {
                    error_log("Email sending failed: " . $e->getMessage());
                    echo json_encode(['success' => true, 'message' => 'Data saved but notification failed']);
                }
            } else {
                echo json_encode(['error' => 'No changes made. Request may be locked or not found.']);
            }
        } else {
            throw new Exception('Failed to save data');
        }
    } else {
        throw new Exception('Invalid operation');
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

$connection->close();
