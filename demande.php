<?php
// Check if the form is submitted
include 'db_connection.php';
include 'session.php';
include 'sidebar.php';

$successMessage = "";
$errorMessage = "";
$privilege = $_SESSION['privilege'];

// Insert data into the database
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Prepare the SQL statement
    $stmt = $connection->prepare("INSERT INTO demande (Nom, Type, Nature, today_date, Fonction, StartDate, EndDate, Jours, Reprise) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
  
    // Get the user's full name from the session
    $fullName = $sessionName;
    // Get today's date
    $todaysDate = date('Y-m-d');
  
    // Bind the form data and additional data to the SQL statement parameters
    $stmt->bind_param('sssssssss', $fullName, $_POST['demande'], $_POST['nature'], $todaysDate, $_POST['fonction'], $_POST['startDate'], $_POST['endDate'], $_POST['dateDiff'], $_POST['reprise']);
  
    // Execute the SQL statement
    if ($stmt->execute()) {
      $_SESSION['selectedNature'] = $_POST['nature'];
      $successMessage = "Demande ajoutée avec succès !";
    } else {
      $errorMessage = "Erreur lors de l'ajout de la demande : " . $connection->error;
    }
  
    // Close the statement
    $stmt->close();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une demande</title>
    <link href="css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="vendor/fontawesome-free/css/all.css" rel="stylesheet">
  <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
	  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<style>

    .btn-primary{
        background-color: #4CAF50;
        
        
    }
    
</style>
</head>

<body>

            <div class="page-breadcrumb">
                <div class="row align-items-center">
                    <div class="col-md-6 col-8 align-self-center">
                        <h3 class="page-title mb-0 p-0">Demande</h3>
                    </div>
                </div>
			</div>
			<div class="container-fluid">
            <div class="col-sm-12">
              <div class="card">
                <div class="card-body">
						<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" ><br>
						
							<?php if ($successMessage): ?>
								<div class="alert alert-success"><?php echo $successMessage; ?></div>
							<?php endif; ?>
							<?php if ($errorMessage): ?>
								<div class="alert alert-danger"><?php echo $errorMessage; ?></div>
							<?php endif; ?>
						<div class="form-row">                       
                                <div class="col">
                                    <label for="name">Nom :</label>
                                    <input disabled class="form-control" id="fname" name="fname" value="<?php echo $sessionName; ?> " 
                                        autocomplete="off" list="namesList">
                                    <datalist id="namesList">
                                        <?php foreach ($names as $name) { ?>
                                        <option value="<?php echo $name; ?>">
                                            <?php } ?>
                                    </datalist>
                                </div>
                            
                            <div class="col">
                                <label for="fold">Type de demande :</label>
                                <select class="form-control" id="demande" name="demande" required>
                                    <option value="" disabled selected>Choisissez le type de demande</option>
                                    <option value="option1">Option 1</option>
                                    <option value="option2">Option 2</option>
                                    <option value="option3">Option 3</option>
                                    
                                </select>
                            </div>	
						</div><br>
							<div class="form-row">
                            <div class="col">
                                <label for="fold">Nature :</label>
                                <select class="form-control" id="nature" name="nature" required>
                                    <option value="" disabled selected>Choisissez la nature de votre demande</option>
                                    <option value="Annuel">Annuel</option>
                                    <option value="Maladie">Maladie</option>
                                    <option value="Non payé">Non payé</option>
                                    <option value="Exceptionnel">Exceptionnel</option>
                                </select>
                            </div>
							<div class="col">
                                <label for="datePicker">Date :</label>
                                <input class="form-control" type="date" id="datePicker" name="fdate" required>
                            </div>

                            <script>
                                // Get today's date as a string in the format "YYYY-MM-DD"
                                const today = new Date().toISOString().split('T')[0];

                                // Set the value of the date input field to today's date
                                document.getElementById('datePicker').value = today;

                                // Disable the date input field to prevent selecting another date
                                document.getElementById('datePicker').disabled = true;
                            </script>

							</div><br>
							<div class="form-row">
                            <div class="col">
                                <label for="datePicker">Date de début:</label>
                                <input class="form-control" type="date" id="startDate" name="startDate" required>
                            </div>
							<div class="col">
                                <label for="datePicker">Date de fin:</label>
                                <input class="form-control" type="date" id="endDate" name="endDate" required>
                            </div>
                            
							</div><br>
                            <div class="form-row">
                                <input class="form-control" type="hidden" id="dateDiff" name="dateDiff" readonly>
                            <div class="col">
                                <label for="repris">Date reprise:</label>
                                <input class="form-control" type="date" id="reprise" name="reprise" required>
                            </div>
                            <div class="col">
                                <label for="fold">Fonction :</label>
                                <select class="form-control" id="fonction" name="fonction" required>
                                    <option value="" disabled selected>Choisissez la fonction</option>
                                    <option value="option1">Option 1</option>
                                    <option value="option2">Option 2</option>
                                    <option value="option3">Option 3</option>
                                    
                                </select>
                            </div>
                            </div><br>
								<div class="button-container">
									<button class="btn btn-rounded btn-success">Ajouter</button>
									<button class="btn btn-rounded btn-warning" id="clean-btn">Vider</button>
                                    <?php if($privilege == 2) {?>
                                        <button class="btn btn-rounded btn-danger" onclick="document.location='add_folder.php'">Annuler</button>
                                    <?php } else { ?>
									<button class="btn btn-rounded btn-danger" onclick="document.location='index.php'">Annuler</button>
                                    <?php } ?>
								</div>
							</div>
						
						</form>
                </div>
              </div>
            </div>
          </div>
</div>
			
    </div>

   
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

      <script>
        $(document).ready(function () {
          $('#clean-btn').on('click', function (e) {
            e.preventDefault();
            $('#demande').val('');
            $('#nature').val('');
            $('#datePicker').val('');
            $('#startDate').val('');
            $('#endDate').val('');
            $('#dateDiff').val('');
            $('#fonction').val('');
            $('#reprise').val('');
          });
        });
      </script>
     
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
    const publicHolidays = [
        // Add your public holidays as strings in the format 'YYYY-MM-DD'
        '2023-08-14',
        '2023-01-01',
        // ... add more holidays as needed
    ];
    </script>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const startDateInput = document.getElementById("startDate");
        const endDateInput = document.getElementById("endDate");
        const dateDiffInput = document.getElementById("dateDiff");

        startDateInput.addEventListener("change", updateDateDiff);
        endDateInput.addEventListener("change", updateDateDiff);

        function isWeekend(date) {
            const day = date.getDay();
            return day === 0 || day === 6;
        }

        function isPublicHoliday(date) {
            const dateString = date.toISOString().split('T')[0];
            return publicHolidays.includes(dateString);
        }

        function updateDateDiff() {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);

            if (!isNaN(startDate) && !isNaN(endDate)) {
                let currentDate = new Date(startDate);
                let daysDiff = 0;

                while (currentDate <= endDate) {
                    if (!isWeekend(currentDate) && !isPublicHoliday(currentDate)) {
                        daysDiff++;
                    }
                    currentDate.setDate(currentDate.getDate() + 1);
                }

                dateDiffInput.value = daysDiff + " jours";
            } else {
                dateDiffInput.value = "";
            }
        }
    });
</script>

</body>

</html>
