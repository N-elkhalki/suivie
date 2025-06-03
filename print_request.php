<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['username'])) {
    exit('Unauthorized');
}

if (isset($_GET['id'])) {
    $stmt = $connection->prepare("
        SELECT r.*, u.Name as employee_name, u.company 
        FROM request_periode r 
        JOIN user u ON r.user_id = u.Id_user 
        WHERE r.id = ?
    ");
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $request = $result->fetch_assoc();
    
    if (!$request) {
        exit('Request not found');
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Demande de Congé - Impression</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        
        .print-buttons {
            text-align: center;
            margin: 20px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        
        .print-buttons button {
            margin: 0 10px;
            padding: 8px 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        
        .section {
            margin: 20px 0;
            padding: 20px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }
        
        .signature-box {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #dee2e6;
            min-height: 80px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
        }
        
        .company-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
            padding: 20px;
        }
        
        .company-logo {
            max-width: 150px;
            height: auto;
        }
        
        .company-info {
            text-align: right;
        }
        
        @media print {
            .print-buttons {
                display: none;
            }
            
            body {
                padding: 0;
            }
            
            .section {
                break-inside: avoid;
            }
            
            .company-header {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="print-buttons">
        <button class="btn btn-primary" onclick="window.print()">
            <i class="bi bi-printer"></i> Imprimer
        </button>
        <button class="btn btn-secondary" onclick="window.close()">
            <i class="bi bi-x-lg"></i> Fermer
        </button>
    </div>

    <div class="company-header">
        <img src="assets/img/logo.png" alt="Company Logo" class="company-logo">
        <div class="company-info">
            <h2><?php echo htmlspecialchars($request['company']); ?></h2>
            <p>Système de Gestion des Congés</p>
        </div>
    </div>

    <div class="header">
        <h1>DEMANDE DE CONGÉ</h1>
        <h2><?php echo htmlspecialchars($request['company']); ?></h2>
    </div>

    <div class="section">
        <h3>PARTIE EMPLOYE</h3>
        <table class="table table-bordered">
            <tr>
                <th>Type de demande:</th>
                <td><?php echo $request['request_type'] === 'conge' ? 'Congé' : 'Autorisation d\'absence'; ?></td>
                <th>Nature de congé:</th>
                <td><?php echo htmlspecialchars($request['leave_type']); ?></td>
            </tr>
            <tr>
                <th>Nom et prénom:</th>
                <td><?php echo htmlspecialchars($request['employee_name']); ?></td>
                <th>Année:</th>
                <td><?php echo htmlspecialchars($request['year']); ?></td>
            </tr>
            <tr>
                <th>Date de début:</th>
                <td><?php echo date('d/m/Y', strtotime($request['start_date'])); ?></td>
                <th>Date de fin:</th>
                <td><?php echo date('d/m/Y', strtotime($request['end_date'])); ?></td>
            </tr>
            <tr>
                <th>Date de reprise:</th>
                <td><?php echo date('d/m/Y', strtotime($request['return_date'])); ?></td>
                <th>Nombre de jours:</th>
                <td><?php echo htmlspecialchars($request['leave_days']); ?></td>
            </tr>
        </table>

        <?php if ($request['comments']): ?>
        <div class="mt-4">
            <strong>Commentaire:</strong><br>
            <div class="p-3 bg-light rounded">
                <?php echo nl2br(htmlspecialchars($request['comments'])); ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="mt-4">
            <strong>Signature de l'employé:</strong>
            <div class="signature-box">
                <?php echo htmlspecialchars($request['digital_signature']); ?>
            </div>
        </div>
    </div>

    <div class="section">
        <h3>PARTIE RESERVEE A LA DIRECTION DE LA SOCIETE</h3>
        <table class="table table-bordered">
            <tr>
                <th colspan="7" class="text-center">Suivi de l'utilisation du droit à congé</th>
            </tr>
            <tr>
                <th>Acquis N-1</th>
                <th>Acquis N</th>
                <th>Pris</th>
                <th>Reste</th>
                <th>Annuel</th>
                <th>Exceptionnel</th>
                <th>Solde</th>
            </tr>
            <tr>
                <td><?php echo htmlspecialchars($request['previous_year_credit'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($request['current_year_credit'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($request['used_credit'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($request['remaining_credit'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($request['annual_leave'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($request['exceptional_leave'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($request['balance'] ?? ''); ?></td>
            </tr>
        </table>

        <div class="row mt-4">
            <div class="col-md-6">
                <strong>Visa Service Ressources Humaines:</strong>
                <div class="signature-box">
                    <?php echo nl2br(htmlspecialchars($request['hr_approval'] ?? '')); ?>
                </div>
            </div>
            <div class="col-md-6">
                <strong>Décision de la Direction Générale:</strong>
                <div class="signature-box">
                    <?php echo nl2br(htmlspecialchars($request['management_decision'] ?? '')); ?>
                </div>
            </div>
        </div>

        <div class="mt-4 text-center">
            <div class="alert <?php 
                echo $request['status'] == 'approved' ? 'alert-success' : 
                    ($request['status'] == 'rejected' ? 'alert-danger' : 'alert-warning'); 
                ?>">
                <strong>Statut de la demande: </strong>
                <?php
                $status = '';
                switch($request['status']) {
                    case 'approved':
                        $status = 'APPROUVÉE';
                        break;
                    case 'rejected':
                        $status = 'REFUSÉE';
                        break;
                    default:
                        $status = 'EN ATTENTE';
                }
                echo $status;
                ?>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Document généré le <?php echo date('d/m/Y'); ?></p>
        <p><?php echo htmlspecialchars($request['company']); ?> - Système de Gestion des Congés</p>
    </div>
</body>
</html>
<?php $connection->close(); ?>