<?php $pageTitle = "Formulaire"; ?>
<?php
   header('Content-Type: text/html; charset=utf-8');
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
     $stmt = $connection->prepare("INSERT INTO formulaire (Date, Work_hours, Commentaires, Office, Folder, Infos, Tasks, Name, username, todays_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
   
     // Bind the form data to the SQL statement parameters
     $stmt->bind_param('ssssssssss', $_POST['fdate'], $_POST['fhours'], $_POST['fcomment'], $_POST['foffice'], $_POST['ffolder'], $_POST['finfos'], $_POST['ftask'], $_POST['fname'], $_POST['fusername'], $_POST['current_date']);
   
     // Execute the SQL statement
     if ($stmt->execute()) {
       $successMessage = "Formulaire ajouté avec succès !";
     } else {
       $errorMessage = "Erreur d'ajout du formulaire : " . $connection->error;
   
     }
   
     // Close the statement
     $stmt->close();
   }
   if (isset($_SESSION['username'])) {
       $username = $_SESSION['username'];
     
       // Prepare the SQL statement to fetch the name from the 'user' table
       $stmt = $connection->prepare('SELECT name FROM user WHERE login = ?');
       $stmt->bind_param('s', $username);
       $stmt->execute();
       $result = $stmt->get_result();
     
       if ($result && $result->num_rows > 0) {
         $row = $result->fetch_assoc();
         $sessionName = $row['name'];
       } else {
         // Handle case when username is not found in the 'user' table
         $sessionName = 'Unknown';
       }
     
       // Close the statement
       $stmt->close();
     } else {
       // Handle case when session username is not set
       $sessionName = 'Unknown';
     }
   
     $date_ent = [];
   	$query = "SELECT date FROM date_entries";
   	$result = $connection->query($query);
   	while ($row = $result->fetch_assoc()) {
       $date_ent[] = $row['date'];
   	}
     ?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
      <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
	  <style>
	  .form-row {
  display: flex;
  flex-wrap: wrap;
  margin-right: -5px;
  margin-left: -5px;
}

.form-row > .col,
.form-row > [class*="col-"] {
  padding-right: 5px;
  padding-left: 5px;
}
	  </style>
   </head>
   <body>
      <!-- <img src="logo.png" class="logo"> -->
      <?php
         // Check for connection errors
         if ($connection->connect_error) {
         	die("Connection failed: " . $connection->connect_error);
         }
         
         // Retrieve the autocomplete data from the formulaire table
         $sql = "SELECT DISTINCT Name_bureau FROM bureau";
         $result = $connection->query($sql);
         $offices = [];
         while($row = $result->fetch_assoc()) {
         	$offices[] = $row['Name_bureau'];
         }
         
         $sql = "SELECT DISTINCT Name_folder FROM folders";
         $result = $connection->query($sql);
         $folders = [];
         while($row = $result->fetch_assoc()) {
         	$folders[] = $row['Name_folder'];
         }
         
         $sql = "SELECT DISTINCT Infos FROM informations";
         $result = $connection->query($sql);
         $infos = [];
         while($row = $result->fetch_assoc()) {
         	$infos[] = $row['Infos'];
         }
         
         $sql = "SELECT DISTINCT Name_task FROM tasks";
         $result = $connection->query($sql);
         $tasks = [];
         while($row = $result->fetch_assoc()) {
         	$tasks[] = $row['Name_task'];
         }
         
         
         ?>
      <div id="layoutSidenav_content">
         <main>
            <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
               <div class="container-xl px-4">
                  <div class="page-header-content pt-4">
                     <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                           <h1 class="page-header-title">
                              <div class="page-header-icon"><i data-feather="plus-square"></i></div>
                              Ajouter un formulaire
                           </h1>
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
                           <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" id="myForm" onsubmit="return checkValues()">
                              <?php if ($successMessage): ?>
                              <div class="alert alert-success"><?php echo $successMessage; ?></div>
                              <?php endif; ?>
                              <?php if ($errorMessage): ?>
                              <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
                              <?php endif; ?>
                              <input type="hidden" name="current_date" value="<?php echo date('Y-m-d'); ?>">
                              <input type="hidden" name="fusername" value="<?php echo $username; ?>">
                              <div class="form-outline">
                                 <div class="col">
                                    <input type="hidden" class="form-label" id="fname" name="fname" value="<?php echo $sessionName; ?> " 
                                       autocomplete="off" list="namesList">
                                    <datalist id="namesList">
                                       <?php foreach ($names as $name) { ?>
                                       <option value="<?php echo $name; ?>">
                                          <?php } ?>
                                    </datalist>
                                 </div>
                              </div>
                              <div class="form-row">
                              <div class="col">
                              <label for="dat">Date :</label>
                              <input class="form-control" type="date" placeholder="Date" id="datePicker" value="<?php echo date('Y-m-d'); ?>" name="fdate" required>
								</div>
                              <div class="col">
                              <label for="fold">Dossier :</label>
                              <input type="text" class="form-control" id="ffolder" name="ffolder" placeholder="Entrez le nom du dossier"
                                 autocomplete="off" list="foldersList" required>
                              <datalist id="foldersList">
                              <?php foreach ($folders as $folder) { ?>
                              <option value="<?php echo $folder; ?>">
                              <?php } ?>
                              </datalist>
                              </div>
                              </div><br>
                              <div class="form-row">
                              <div class=" col">
                              <label for="inf">Informations supplémentaires :</label>
                              <input type="text" class="form-control" id="finfos" name="finfos" placeholder="Entrez vos informations supplémentaires"
                                 autocomplete="off" list="infosList" required>
                              <datalist id="infosList">
                              <?php foreach ($infos as $info) { ?>
                              <option value="<?php echo $info; ?>">
                              <?php } ?>
                              </datalist>
                              </div>
                              <div class="col">
                              <label for="offi">Bureau :</label>
                              <input type="text" class="form-control" id="foffice" name="foffice" placeholder="Entrez le nom du bureau"
                                 autocomplete="off" list="officesList" required>
                              <datalist id="officesList">
                              <?php foreach ($offices as $office) { ?>
                              <option value="<?php echo $office; ?>">
                              <?php } ?>
                              </datalist>
                              </div>
                              </div><br>
                              <div class="form-row">
                              <div class="col">
                              <label for="tas">Taches :</label>
                              <input type="text" class="form-control" id="ftask" name="ftask" placeholder="Entrez les taches"
                                 autocomplete="off" list="tasksList" required>
                              <datalist id="tasksList">
                              <?php foreach ($tasks as $task) { ?>
                              <option value="<?php echo $task; ?>">
                              <?php } ?>
                              </datalist>
                              </div>
                              <div class="col">
                              <label for="hour">Heures de travail :</label>
                              <input type="text" class="form-control" name="fhours" id="fhours" placeholder="Entrez les heures de votre travail" required min="0.5" step="0.1" pattern="^\d+(\.\d+)?$" title="Entrez un nombre entier ou un nombre décimal avec un point (ex. 3 ou 3.5)">
                              </div>
                              </div><br>
                              <div class="form-row">
                              <div class="col">
                              <label for="commen">Commentaires :</label><br>
                              <textarea class="form-control" name="fcomment" id="fcomment" rows="3" placeholder="Entrez votre commentaire" ></textarea>
                              </div>
                              </div><br>
                              <div class="button-container">
                              <button class="btn btn-rounded btn-success">Ajouter</button>
                              <button class="btn btn-rounded btn-warning" id="clean-btn">Vider</button>
                              <button class="btn btn-rounded btn-danger" onclick="document.location='displayform.php'">Annuler</button>
                              </div>
                        </div>
                        </form>
                     </div>
                  </div>
         </main>
         <footer class="footer-admin mt-auto footer-light">
         <div class="container-xl px-4">
         <div class="row">
         <div class="col-md-6 small">Copyright &copy; Osourcing Sarl 2024</div>
         <div class="col-md-6 text-md-end small">
         <a href="#!">Privacy Policy</a>
         &middot;
         <a href="#!">Terms &amp; Conditions</a>
         </div>
         </div>
         </div>
         </footer>
         </div>
         </div>
      </div>
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
      <script>
         $(document).ready(function () {
           $('#clean-btn').on('click', function (e) {
             e.preventDefault();
             $('#datePicker').val('');
             $('#foffice').val('');
             $('#ffolder').val('');
             $('#finfos').val('');
             $('#ftask').val('');
             $('#fhours').val('');
             $('#fcomment').val('');
           });
         });
      </script>
      <script>
         document.addEventListener("DOMContentLoaded", function() {
             let disabledDates = <?php echo json_encode($date_ent); ?>;
             flatpickr("#datePicker", {
                 disable: disabledDates
             });
         });
      </script>
      <script>
         function validateInput() {
           var input = document.getElementById("foffice").value;
           var dataList = document.getElementById("officesList").options;
           var warning = document.getElementById("warning");
           var valid = false;
           
           for(var i=0; i<dataList.length; i++) {
             if(input == dataList[i].value) {
               valid = true;
               break;
             }
           }
           
           if(valid) {
             warning.innerHTML = '';
           } else {
             warning.innerHTML = 'Please choose an existing value from the list.';
           }
         }
      </script>
      <script>
         function validateInput() {
           var input = document.getElementById("ffolder").value;
           var dataList = document.getElementById("foldersList").options;
           var warning = document.getElementById("warning");
           var valid = false;
           
           for(var i=0; i<dataList.length; i++) {
             if(input == dataList[i].value) {
               valid = true;
               break;
             }
           }
           
           if(valid) {
             warning.innerHTML = '';
           } else {
             warning.innerHTML = 'Please choose an existing value from the list.';
           }
         }
      </script>
      <script>
         function validateInput() {
           var input = document.getElementById("finfos").value;
           var dataList = document.getElementById("infosList").options;
           var warning = document.getElementById("warning");
           var valid = false;
           
           for(var i=0; i<dataList.length; i++) {
             if(input == dataList[i].value) {
               valid = true;
               break;
             }
           }
           
           if(valid) {
             warning.innerHTML = '';
           } else {
             warning.innerHTML = 'Please choose an existing value from the list.';
           }
         }
      </script><script>
         function validateInput() {
           var input = document.getElementById("ftask").value;
           var dataList = document.getElementById("tasksList").options;
           var warning = document.getElementById("warning");
           var valid = false;
           
           for(var i=0; i<dataList.length; i++) {
             if(input == dataList[i].value) {
               valid = true;
               break;
             }
           }
           
           if(valid) {
             warning.innerHTML = '';
           } else {
             warning.innerHTML = 'Please choose an existing value from the list.';
           }
         }
      </script>
      <script>
         function validateInput() {
           var input = document.getElementById("ffolder").value;
           var dataList = document.getElementById("foldersList").options;
           var warning = document.getElementById("warning");
           var valid = false;
           
           for(var i=0; i<dataList.length; i++) {
             if(input == dataList[i].value) {
               valid = true;
               break;
             }
           }
           
           if(valid) {
             warning.innerHTML = '';
           } else {
             warning.innerHTML = 'Please choose an existing value from the list.';
           }
         }
      </script>
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
      <script>
         function checkValues() {
             const folderInput = document.getElementById('ffolder').value;
             const infosInput = document.getElementById('finfos').value;
             const officeInput = document.getElementById('foffice').value;
             const taskInput = document.getElementById('ftask').value;
         
             const folders = <?php echo json_encode($folders); ?>;
             const infos = <?php echo json_encode($infos); ?>;
             const offices = <?php echo json_encode($offices); ?>;
             const tasks = <?php echo json_encode($tasks); ?>;
         
             if (!folders.includes(folderInput)) {
                 alert('Le Dossier n\'existe pas dans la liste');
                 return false;
             }
         
             if (!infos.includes(infosInput)) {
                 alert('Informations supplémentaires n\'existe pas dans la liste');
                 return false;
             }
         
             if (!offices.includes(officeInput)) {
                 alert('Le Bureau n\'existe pas dans la liste');
                 return false;
             }
         
             if (!tasks.includes(taskInput)) {
                 alert('La taches n\'existe pas dans la liste');
                 return false;
             }
         
             const fhours = document.getElementById("fhours").value;
             if (fhours < 0.25) {
                 alert("Les heures de circulation doivent être d'au moins 0.25");
                 return false;
             }
         
             const fdate = document.getElementById("datePicker").value;
             if (!fdate) {
                 alert("remplir la date");
                 return false;
             }
         
             const commentInput = document.getElementById("fcomment").value.trim();
             if (taskInput === "Formation" && !commentInput) {
                 alert("Un commentaire est requis lorsque Taches est Formation!");
                 return false;
             }
         
             return true;
         }
         
         document.getElementById("myForm").onsubmit = validateForm;
      </script>
   </body>
</html>