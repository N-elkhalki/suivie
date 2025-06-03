<?php
session_start();
require_once 'db_connection.php';

// Include PHPMailer classes manually
require_once 'PHPMailer/src/Exception.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['username'])) {
    http_response_code(401);
    exit('Unauthorized');
}

try {
    // Get user's name from database
    $userStmt = $connection->prepare("SELECT Name FROM user WHERE Id_user = ?");
    $userStmt->bind_param("i", $_SESSION['id']);
    $userStmt->execute();
    $userResult = $userStmt->get_result();
    $userData = $userResult->fetch_assoc();
    $userName = $userData['Name'];
    $userStmt->close();

    $stmt = $connection->prepare("
        INSERT INTO request_periode (
            user_id, request_type, leave_type, year, 
            start_date, end_date, return_date, leave_days,
            digital_signature, request_date, comments, status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, 'pending')
    ");

    // Calculate leave days excluding Sundays
    $start = new DateTime($_POST['startDate']);
    $end = new DateTime($_POST['endDate']);
    $days = 0;
    
    while ($start <= $end) {
        if ($start->format('N') != 7) { // Skip Sundays
            $days++;
        }
        $start->modify('+1 day');
    }

    $stmt->bind_param(
        "isssssssss",
        $_SESSION['id'],
        $_POST['requestType'],
        $_POST['leaveType'],
        $_POST['year'],
        $_POST['startDate'],
        $_POST['endDate'],
        $_POST['returnDate'],
        $days,
        $_POST['digitalSignature'],
        $_POST['comments']
    );

    if ($stmt->execute()) {
        // Send email notification
        try {
            $mail = new PHPMailer(true);
            
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'mail.osourcing.net';
            $mail->SMTPAuth = true;
            $mail->Username = 'no-reply@osourcing.net';
            $mail->Password = 'Osourcing@123'; // Add your SMTP password here
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            
            // Recipients
            $mail->setFrom('no-reply@osourcing.net', 'Système de Gestion des Congés');
            $mail->addAddress('Finance-osourcing@osourcing.ca');
            $mail->addCC('baziz@osourcing.ca');
            
            // Content
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            
            $requestType = $_POST['requestType'] === 'conge' ? 'Congé' : 'Autorisation d\'absence';
            $mail->Subject = "Demande de {$requestType} par {$userName}";
            
            // Email body
            $startDate = date('d/m/Y', strtotime($_POST['startDate']));
            $endDate = date('d/m/Y', strtotime($_POST['endDate']));
            $returnDate = date('d/m/Y', strtotime($_POST['returnDate']));
            
            $mail->Body = "
                <html>
                <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
                    <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                        <h2 style='color: #2c5282;'>Nouvelle Demande de {$requestType}</h2>
                        
                        <div style='background-color: #f7fafc; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                            <p><strong>Employé:</strong> {$userName}</p>
                            <p><strong>Type:</strong> {$requestType} {$_POST['leaveType']}</p>
                            <p><strong>Période:</strong> Du {$startDate} au {$endDate}</p>
                            <p><strong>Date de reprise:</strong> {$returnDate}</p>
                            <p><strong>Nombre de jours:</strong> {$days}</p>
                            " . ($_POST['comments'] ? "<p><strong>Commentaires:</strong> {$_POST['comments']}</p>" : "") . "
                        </div>
                        
                        <p style='color: #718096; font-size: 0.9em;'>
                            Cette demande nécessite votre attention. Veuillez vous connecter au système de gestion des congés pour l'examiner.
                        </p>
                    </div>
                </body>
                </html>";
            
            $mail->send();
            $emailSent = true;
        } catch (Exception $e) {
            error_log("Email sending failed: " . $e->getMessage());
            $emailSent = false;
        }
        
        echo json_encode([
            'success' => true,
            'emailSent' => $emailSent
        ]);
    } else {
        throw new Exception('Failed to submit request');
    }

    $stmt->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

$connection->close();
