<?php $pageTitle = " Gestion des Congés"; ?>
<?php
include 'session.php';
require_once 'db_connection.php';


include 'sidebar.php';
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Get user data from database including company
$stmt = $connection->prepare('SELECT Name, Privilege, company FROM user WHERE Id_user = ?');
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();

$currentUser = [
    'id' => $_SESSION['id'],
    'name' => $userData['Name'],
    'privilege' => $userData['Privilege'],
    'company' => $userData['company'] // Add company to user data
];

$stmt->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Congés</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/lucide-icons@0.344.0/font/lucide.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #d69309;
            --secondary-color: #323232;
        }

        .bg-primary-custom {
            background-color: var(--primary-color);
        }

        .bg-secondary-custom {
            background-color: var(--secondary-color);
        }

        .text-primary-custom {
            color: var(--primary-color);
        }

        .btn-primary-custom {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .btn-primary-custom:hover {
            background-color: #b47a07;
            border-color: #b47a07;
            color: white;
        }

        .header {
            background: linear-gradient(to right, var(--secondary-color), var(--primary-color));
            color: white;
            padding: 1rem;
        }

        .form-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin: 2rem auto;
            max-width: 1200px;
            padding: 2rem;
        }

        .nav-tabs .nav-link.active {
            color: var(--primary-color);
            border-color: var(--primary-color);
            border-bottom-color: transparent;
        }

        .nav-tabs .nav-link {
            color: var(--secondary-color);
        }

        @media print {
            .no-print {
                display: none;
            }
            .print-only {
                display: block;
            }
        }
    </style>
</head>
<body class="bg-light">
   
	      <div id="layoutSidenav_content">
         <main>
            <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
               <div class="container-xl px-4">
                  <div class="page-header-content pt-4">
                     <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                           <h1 class="page-header-title">
                              <div class="page-header-icon"><i data-feather="plus-square"></i></div>
                              Gestion des Congés
                           </h1>
						   <div class="d-flex align-items-center">
                    
                    
                </div>
                        </div>
                     </div>
                  </div>
               </div>
            </header>
            <div class="container-xl px-4 mt-n10">
               <div class="row">
                  <div class="col">
                     <div class="card mb-4">
                        <div class="card-body">
    <div class="container mt-4">
        <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="card-header nav-link  active" id="new-request-tab" data-bs-toggle="tab" data-bs-target="#new-request" type="button" role="tab">
                    <i class="lucide-file-plus"></i> Nouvelle Demande
                </button>
            </li>
            <li class=" nav-item" role="presentation">
                <button class="card-header nav-link" id="my-requests-tab" data-bs-toggle="tab" data-bs-target="#my-requests" type="button" role="tab">
                    <i class="lucide-list"></i> Mes Demandes
                </button>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="new-request" role="tabpanel">
                <div class="form-container">
                    <form id="leaveRequestForm" action="submit_request.php" method="POST">
                        <div class="mb-4">
                            <label class="form-label">Type de demande</label>
                            <select name="requestType" class="form-select">
                                <option value="conge">Congé</option>
                                <option value="absence">Autorisation d'Absence</option>
                            </select>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header bg-secondary-custom text-white">
                                <h3 class="card-header mb-0">PARTIE EMPLOYE</h3>
                            </div>
                            <div class="card-body">
                                <!-- Leave Type -->
                                <div class="mb-3">
                                    <label class="form-label">Nature de congé</label>
                                    <select name="leaveType" class="form-select">
                                        <option value="Annuel">Annuel</option>
                                        <option value="Exceptionnel">Exceptionnel</option>
                                    </select>
                                </div>

                                <!-- Employee Name -->
                                <div class="mb-3">
                                    <label class="form-label">Nom et prénom</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($currentUser['name']); ?>" readonly>
                                    <input type="hidden" name="userId" value="<?php echo $currentUser['id']; ?>">
                                </div>

                                <!-- Year -->
                                <div class="mb-3">
                                    <label class="form-label">Année d'imputation</label>
                                    <select name="year" class="form-select">
                                        <?php
                                        $currentYear = date('Y');
                                        for ($year = $currentYear - 1; $year <= $currentYear + 1; $year++) {
                                            echo "<option value=\"$year\"" . ($year == $currentYear ? " selected" : "") . ">$year</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <!-- Leave Period -->
                                <div class="mb-3">
                                    <label class="form-label">Période du congé demandé</label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="form-label">Du</label>
                                            <input type="date" name="startDate" class="form-control" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Au</label>
                                            <input type="date" name="endDate" class="form-control" required>
                                        </div>
                                    </div>
                                    <div id="leaveDays" class="text-primary-custom mt-2"></div>
                                </div>

                                <!-- Return Date -->
                                <div class="mb-3">
                                    <label class="form-label">Date reprise</label>
                                    <input type="date" name="returnDate" class="form-control" readonly>
                                </div>

                                <!-- Digital Signature -->
                                <div class="mb-3">
                                    <label class="form-label">SIGNATURE NUMERIQUE DE L'INTERESSE</label>
                                    <input type="text" name="digitalSignature" class="form-control" value="<?php echo htmlspecialchars($currentUser['name']); ?>" required>
                                </div>

                                <!-- Comments -->
                                <div class="mb-3">
                                    <label class="form-label">Commentaire</label>
                                    <textarea name="comments" class="form-control" rows="3"></textarea>
                                    <small class="text-muted">*Requis pour les congés exceptionnels</small>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header bg-secondary-custom text-white">
                                <h3 class="card-header mb-0">PARTIE RESERVEE A LA DIRECTION DE LA SOCIETE</h3>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th colspan="8" class="text-center">Suivi de l'utilisation du droit à congé</th>
                                        </tr>
                                        <tr>
                                            <th colspan="4">Droits au congé</th>
                                            <th colspan="2">Congé demandé</th>
                                            <th rowspan="2">Solde</th>
                                        </tr>
                                        <tr>
                                            <th>Acquis N-1</th>
                                            <th>Acquis N</th>
                                            <th>Pris</th>
                                            <th>Reste</th>
                                            <th>Annuel</th>
                                            <th>Exceptionnel</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>

                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <div class="border p-3">
                                            <h4 class="text-center">Visa Service Ressources Humaines</h4>
                                            <div style="height: 100px;"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="border p-3">
                                            <h4 class="text-center">Décision de la Direction Générale</h4>
                                            <div style="height: 100px;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary-custom">
                                <i class="lucide-save"></i> Soumettre la demande
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="tab-pane fade" id="my-requests" role="tabpanel">
                <!-- This section will be populated by AJAX -->
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Calculate leave days and return date
            function calculateDays() {
                const startDate = new Date($('input[name="startDate"]').val());
                const endDate = new Date($('input[name="endDate"]').val());
                
                if (startDate && endDate && startDate <= endDate) {
            // Calculate return date (end date + 1, skip Sunday)
            let returnDate = new Date(endDate);
            returnDate.setDate(returnDate.getDate() + 1);
            
            // If return date falls on Sunday, move to Monday
            if (returnDate.getDay() === 0) { // 0 = Sunday
                returnDate.setDate(returnDate.getDate() + 1);
            }
            
            // Format the date as YYYY-MM-DD
            const returnDateStr = returnDate.toISOString().split('T')[0];
            $('input[name="returnDate"]').val(returnDateStr);
            
                    
                    // Calculate working days
                    let days = 0;
                    const currentDate = new Date(startDate);
                    while (currentDate <= endDate) {
                        if (currentDate.getDay() !== 0) { // Skip Sundays
                            days++;
                        }
                        currentDate.setDate(currentDate.getDate() + 1);
                    }
                    
                    // Display the number of days
                    $('#leaveDays').text(`Nombre de jours ouvrables: ${days} (dimanches exclus)`);
                }
            }

            $('input[name="startDate"], input[name="endDate"]').on('change', calculateDays);

            // Load requests list
            function loadRequests() {
                $.get('get_requests.php', function(data) {
                    $('#my-requests').html(data);
                });
            }

            // Initial load
            loadRequests();

            // Form submission
            $('#leaveRequestForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        alert('Demande soumise avec succès');
                        loadRequests();
                        $('#leaveRequestForm')[0].reset();
                    },
                    error: function() {
                        alert('Erreur lors de la soumission de la demande');
                    }
                });
            });

            // Add company selection handler
            $('#companySelect').change(function() {
                const company = $(this).val();
                $.post('update_user_company.php', { company: company }, function(response) {
                    if (response.success) {
                        location.reload();
                    }
                });
            });
        });
    </script>
</body>
</html>