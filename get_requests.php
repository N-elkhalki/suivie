<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['username'])) {
    exit('Unauthorized');
}

$isAdmin = $_SESSION['privilege'] == 1;
$userId = $_SESSION['id'];

// Modified query to only show all requests for admin, and user's own requests for others
$query = "SELECT r.*, u.Name as employee_name, u.company 
          FROM request_periode r 
          JOIN user u ON r.user_id = u.Id_user 
          WHERE 1=1 ";

// Add user filter for non-admin users
if (!$isAdmin) {
    $query .= "AND r.user_id = ? ";
}

$query .= "ORDER BY r.request_date DESC";

$stmt = $connection->prepare($query);
if (!$isAdmin) {
    $stmt->bind_param("i", $userId);
}
$stmt->execute();
$result = $stmt->get_result();

// Check if there are any requests
$hasRequests = $result->num_rows > 0;
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
<style>
    .btn-sm i {
        font-size: 1rem;
    }
    .btn-primary-custom {
        background-color: #d69309;
        border-color: #d69309;
        color: white;
    }
    .btn-primary-custom:hover {
        background-color: #b47a07;
        border-color: #b47a07;
        color: white;
    }
    .modal-header {
        background-color: #d69309;
        color: white;
    }
    .modal-content {
        max-width: 800px;
        margin: 0 auto;
    }
    .request-details {
        padding: 20px;
    }
    .request-details h6 {
        color: #333;
        border-bottom: 2px solid #d69309;
        padding-bottom: 8px;
        margin-bottom: 15px;
    }
    .request-details .row {
        margin-bottom: 20px;
    }
    .request-details strong {
        color: #555;
    }
    .management-section {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin-top: 20px;
    }
    .management-table {
        width: 100%;
        margin-bottom: 1rem;
    }
    .management-table th,
    .management-table td {
        padding: 8px;
        border: 1px solid #dee2e6;
    }
    .management-table th {
        background-color: #f8f9fa;
    }
    .readonly-field {
        background-color: #e9ecef;
        padding: 8px;
        border-radius: 4px;
        border: 1px solid #dee2e6;
    }
    .alert-success-custom {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        display: none;
    }
    #successToast {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        background-color: #28a745;
        color: white;
        padding: 15px 25px;
        border-radius: 4px;
        display: none;
    }
</style>

<div class="form-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Liste des Demandes</h3>
        <?php if ($hasRequests): ?>
        <select class="form-select w-auto" id="statusFilter">
            <option value="all">Tous les statuts</option>
            <option value="pending">En attente</option>
            <option value="approved">Approuvé</option>
            <option value="rejected">Refusé</option>
        </select>
        <?php endif; ?>
    </div>

    <!-- Success Toast -->
    <div id="successToast" class="shadow">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-2"></i>
            <span>La demande a été mise à jour avec succès</span>
        </div>
    </div>

    <?php if (!$hasRequests): ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i>
        <?php echo $isAdmin ? 
            "Aucune demande n'a été soumise." : 
            "Vous n'avez pas encore soumis de demande."; ?>
    </div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Employé</th>
                    <th>Type</th>
                    <th>Période</th>
                    <th>Jours</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr data-request-id="<?php echo $row['id']; ?>">
                    <td><?php echo date('d/m/Y', strtotime($row['request_date'])); ?></td>
                    <td><?php echo htmlspecialchars($row['employee_name']); ?></td>
                    <td><?php echo $row['request_type'] == 'conge' ? 'Congé' : 'Absence'; ?> <?php echo $row['leave_type']; ?></td>
                    <td>
                        <?php 
                        echo date('d/m/Y', strtotime($row['start_date'])) . ' - ' . 
                             date('d/m/Y', strtotime($row['end_date']));
                        ?>
                    </td>
                    <td><?php echo $row['leave_days']; ?></td>
                    <td>
                        <span class="badge <?php 
                            echo $row['status'] == 'approved' ? 'bg-success' : 
                                ($row['status'] == 'rejected' ? 'bg-danger' : 'bg-warning');
                        ?>">
                            <?php 
                            echo $row['status'] == 'approved' ? 'Approuvé' : 
                                ($row['status'] == 'rejected' ? 'Refusé' : 'En attente');
                            ?>
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-primary-custom view-request" 
                                data-bs-toggle="modal" 
                                data-bs-target="#requestModal"
                                data-id="<?php echo $row['id']; ?>">
                            <i class="bi bi-eye"></i>
                        </button>
                        
                        

                        <?php if ($row['status'] == 'approved'): ?>
                        <button class="btn btn-sm btn-secondary print-request" 
                                onclick="window.open('print_request.php?id=<?php echo $row['id']; ?>', '_blank', 'width=800,height=600')">
                            <i class="bi bi-printer"></i>
                        </button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<!-- Request Details Modal -->
<div class="modal fade" id="requestModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Détails de la Demande</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <?php if ($isAdmin): ?>
                <button type="button" class="btn btn-primary" id="saveDataBtn">
                    <i class="bi bi-save"></i> Sauvegarder les données
                </button>
                <button type="button" class="btn btn-success" id="approveBtn">
                    <i class="bi bi-check-lg"></i> Approuver
                </button>
                <button type="button" class="btn btn-danger" id="rejectBtn">
                    <i class="bi bi-x-lg"></i> Refuser
                </button>
                <?php endif; ?>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    let currentRequestId = null;

    // Handle view request button click
    $('.view-request').on('click', function() {
        currentRequestId = $(this).data('id');
        
                // Load request details via AJAX
        $.get('get_request_details.php', { id: currentRequestId }, function(data) {
            if (data.error) {
                alert('Error loading request details');
                return;
            }
            
            // Build the modal content
            let modalContent = `
                <div class="request-details">
                    <h6 class="mb-3">Informations de l'employé</h6>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><strong>Nom:</strong> ${data.employee_name}</p>
                            <p><strong>Type:</strong> ${data.request_type === 'conge' ? 'Congé' : 'Absence'} ${data.leave_type}</p>
                            <p><strong>Entreprise:</strong> ${data.company}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Date de début:</strong> ${new Date(data.start_date).toLocaleDateString()}</p>
                            <p><strong>Date de fin:</strong> ${new Date(data.end_date).toLocaleDateString()}</p>
                            <p><strong>Jours:</strong> ${data.leave_days}</p>
                        </div>
                    </div>
                    
                    <h6 class="mb-3">Détails de la demande</h6>
                    <div class="row mb-4">
                        <div class="col-12">
                            <p><strong>Commentaires:</strong></p>
                            <p class="bg-light p-2 rounded">${data.comments || 'Aucun commentaire'}</p>
                        </div>
                    </div>
                    
                    <h6 class="mb-3">Partie Administrative</h6>
                    <div class="management-section">
                        <table class="management-table">
                            <thead>
                                <tr>
                                    <th colspan="7">Suivi de l'utilisation du droit à congé</th>
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
                            </thead>
                            <tbody>
                                <tr>
                                    ${data.status === 'pending' ? `
                                        <td><input type="text" class="form-control" name="previousYearCredit" value="${data.previous_year_credit || ''}"></td>
                                        <td><input type="text" class="form-control" name="currentYearCredit" value="${data.current_year_credit || ''}"></td>
                                        <td><input type="text" class="form-control" name="usedCredit" value="${data.used_credit || ''}"></td>
                                        <td><input type="text" class="form-control" name="remainingCredit" value="${data.remaining_credit || ''}"></td>
                                        <td><input type="text" class="form-control" name="annualLeave" value="${data.annual_leave || ''}"></td>
                                        <td><input type="text" class="form-control" name="exceptionalLeave" value="${data.exceptional_leave || ''}"></td>
                                        <td><input type="text" class="form-control" name="balance" value="${data.balance || ''}"></td>
                                    ` : `
                                        <td class="readonly-field">${data.previous_year_credit || '-'}</td>
                                        <td class="readonly-field">${data.current_year_credit || '-'}</td>
                                        <td class="readonly-field">${data.used_credit || '-'}</td>
                                        <td class="readonly-field">${data.remaining_credit || '-'}</td>
                                        <td class="readonly-field">${data.annual_leave || '-'}</td>
                                        <td class="readonly-field">${data.exceptional_leave || '-'}</td>
                                        <td class="readonly-field">${data.balance || '-'}</td>
                                    `}
                                </tr>
                            </tbody>
                        </table>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h6>Visa Service Ressources Humaines</h6>
                                ${data.status === 'pending' ? `
                                    <textarea class="form-control" name="hrApproval" rows="3">${data.hr_approval || ''}</textarea>
                                ` : `
                                    <div class="readonly-field">${data.hr_approval || '-'}</div>
                                `}
                            </div>
                            <div class="col-md-6">
                                <h6>Décision de la Direction Générale</h6>
                                ${data.status === 'pending' ? `
                                    <textarea class="form-control" name="managementDecision" rows="3">${data.management_decision || ''}</textarea>
                                ` : `
                                    <div class="readonly-field">${data.management_decision || '-'}</div>
                                `}
                            </div>
                        </div>
                    </div>

                    ${data.status === 'approved' ? `
                        <div class="alert alert-success mt-4">
                            <i class="bi bi-check-circle"></i> Cette demande a été approuvée
                        </div>
                    ` : data.status === 'rejected' ? `
                        <div class="alert alert-danger mt-4">
                            <i class="bi bi-x-circle"></i> Cette demande a été refusée
                        </div>
                    ` : `
                        <div class="alert alert-warning mt-4">
                            <i class="bi bi-clock"></i> Cette demande est en attente d'approbation
                        </div>
                    `}
                </div>
            `;
            
            // Update modal content
            $('.modal-body').html(modalContent);
            
            // Show/hide action buttons based on status
            if (data.status === 'pending') {
                $('#approveBtn, #rejectBtn, #saveDataBtn').show();
            } else {
                $('#approveBtn, #rejectBtn, #saveDataBtn').hide();
            }
        });
    });

    // Handle save data button
    $('#saveDataBtn').on('click', function() {
        const formData = {
            id: currentRequestId,
            saveData: true,
            previousYearCredit: $('input[name="previousYearCredit"]').val(),
            currentYearCredit: $('input[name="currentYearCredit"]').val(),
            usedCredit: $('input[name="usedCredit"]').val(),
            remainingCredit: $('input[name="remainingCredit"]').val(),
            annualLeave: $('input[name="annualLeave"]').val(),
            exceptionalLeave: $('input[name="exceptionalLeave"]').val(),
            balance: $('input[name="balance"]').val(),
            hrApproval: $('textarea[name="hrApproval"]').val(),
            managementDecision: $('textarea[name="managementDecision"]').val()
        };
        
        $.ajax({
            url: 'update_request.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert('Données sauvegardées avec succès');
                } else {
                    alert('Erreur lors de la sauvegarde: ' + (response.error || 'Erreur inconnue'));
                }
            },
            error: function(xhr, status, error) {
                alert('Erreur lors de la sauvegarde: ' + error);
            }
        });
    });

    // Handle approve/reject buttons
    $('#approveBtn, #rejectBtn').on('click', function() {
        const action = $(this).attr('id') === 'approveBtn' ? 'approve' : 'reject';
        
        $.ajax({
            url: 'update_request.php',
            type: 'POST',
            data: {
                id: currentRequestId,
                action: action
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Erreur lors de la mise à jour: ' + (response.error || 'Erreur inconnue'));
                }
            },
            error: function(xhr, status, error) {
                alert('Erreur lors de la mise à jour: ' + error);
            }
        });
    });

    // Handle status filter
    $('#statusFilter').on('change', function() {
        const status = $(this).val();
        $('tbody tr').each(function() {
            const rowStatus = $(this).find('.badge').text().trim().toLowerCase();
            if (status === 'all' || rowStatus === status) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
});
</script>

<?php
$connection->close();
?>