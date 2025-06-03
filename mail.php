<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mailer {
    private $mail;

    public function __construct() {
        $this->mail = new PHPMailer(true);
        
        // Server settings
        $this->mail->isSMTP();
        $this->mail->Host = 'mail.osourcing.net';
        $this->mail->SMTPAuth = true;
        $this->mail->Username = 'no-reply@osourcing.net';
        $this->mail->Password = ''; // Add your SMTP password here
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = 587;
        
        // Default settings
        $this->mail->isHTML(true);
        $this->mail->CharSet = 'UTF-8';
        $this->mail->setFrom('no-reply@osourcing.net', 'Système de Gestion des Congés');
    }

    public function sendLeaveRequestNotification($requestData, $userName) {
        try {
            $this->mail->addAddress('Finance-osourcing@osourcing.ca');
            $this->mail->addCC('baziz@osourcing.ca');
            
            // Set subject
            $requestType = $requestData['request_type'] === 'conge' ? 'Congé' : 'Autorisation d\'absence';
            $this->mail->Subject = "Demande de {$requestType} par {$userName}";
            
            // Create email body
            $body = $this->createEmailBody($requestData, $userName);
            $this->mail->Body = $body;
            
            // Send email
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email sending failed: " . $e->getMessage());
            return false;
        }
    }

    private function createEmailBody($requestData, $userName) {
        $startDate = date('d/m/Y', strtotime($requestData['startDate']));
        $endDate = date('d/m/Y', strtotime($requestData['endDate']));
        $returnDate = date('d/m/Y', strtotime($requestData['returnDate']));
        $requestType = $requestData['request_type'] === 'conge' ? 'Congé' : 'Autorisation d\'absence';
        
        return "
            <html>
            <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
                <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                    <h2 style='color: #2c5282;'>Nouvelle Demande de {$requestType}</h2>
                    
                    <div style='background-color: #f7fafc; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                        <p><strong>Employé:</strong> {$userName}</p>
                        <p><strong>Type:</strong> {$requestType} {$requestData['leaveType']}</p>
                        <p><strong>Période:</strong> Du {$startDate} au {$endDate}</p>
                        <p><strong>Date de reprise:</strong> {$returnDate}</p>
                        <p><strong>Nombre de jours:</strong> {$requestData['leaveDays']}</p>
                        
                        " . ($requestData['comments'] ? "<p><strong>Commentaires:</strong> {$requestData['comments']}</p>" : "") . "
                    </div>
                    
                    <p style='color: #718096; font-size: 0.9em;'>
                        Cette demande nécessite votre attention. Veuillez vous connecter au système de gestion des congés pour l'examiner.
                    </p>
                </div>
            </body>
            </html>";
    }
}