<?php $pageTitle = "Liste des dossiers"; ?>
<?php
include 'db_connection.php';
include 'session.php';
include 'sidebar.php';

$privilege = $_SESSION['privilege'];
if ($privilege == 3 ) {
    // Redirect to an unauthorized access page or display an error message
    header("Location: index.php");
    exit;
}
?>
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
<link href="https://unpkg.com/bootstrap-table@1.22.1/dist/bootstrap-table.min.css" rel="stylesheet">

<div id="layoutSidenav_content">
  <main>
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
      <div class="container-xl px-4">
        <div class="page-header-content pt-4">
          <div class="row align-items-center justify-content-between">
            <div class="col-auto mt-4">
              <h1 class="page-header-title">
                <div class="page-header-icon"><i data-feather="plus-square"></i></div>
                Liste des dossiers
              </h1>
            </div>
          </div>
        </div>
      </div>
    </header>
    <div class="container-fluid px-4">
      <div class="card mt-n10">
        <!-- ============================================================== -->
        <!-- Start Page Content -->
        <!-- ============================================================== -->
        <div class="row">
          <!-- column -->
          <div class="col-sm-12">
            <div class="card">
              <div class="card-body">
                <?php
                if (isset($_SESSION['username'])) {
                    // Retrieve the folder data from the database
                    $query = "SELECT * FROM folders";
                    $result = mysqli_query($connection, $query);

                    // Check if any folders are found
                    if ($result && mysqli_num_rows($result) > 0) {
                        ?>
                        <div style="margin:5px auto;width:80%">
                          <table id="bootstrap-table" class="table table-bordered">
                            <thead>
                              <tr>
                                <th width="100" data-sortable="true">Nom du dossier</th>
                                <th width="100" data-sortable="true">Action</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php 
                              // Loop through each folder and display the data
                              while ($row = mysqli_fetch_assoc($result)) {
                                  $Id_folder = $row['Id_folder'];
                                  $Folder = $row['Name_folder'];
                              
                                  // Display the folder data in the table
                                  echo '<tr>';
                                  echo '<td>' . $Folder . '</td>';
                                  echo '<td>';
                                  echo '<button class="btn btn-rounded btn-success"><a href="editf.php?id=' . $Id_folder . '" style="color:white;">Modifier</a></button>';
                                  echo '<form action="delete_folder_admin.php" method="POST" class="d-inline">';
                                  echo '<button class="btn btn-rounded btn-danger" type="submit" name="folder_delete_btn" value="' . $Id_folder . '">Supprimer</button>';
                                  echo '</form>';
                                  echo '<button class="btn btn-rounded btn-primary" onclick="openModal(' . $Id_folder . ')">Livré</button>';
                                  echo '</td>';
                                  echo '</tr>';
                              }
                              ?>
                            </tbody>
                          </table>
                        </div>
                        <?php
                    } else {
                        echo 'Aucun dossier trouvé.';
                    }

                    // Close the database connection
                    mysqli_close($connection);
                } else {
                    echo 'Nom d utilisateur de la session non défini.';
                }
                ?>
              </div>
            </div>
          </div>
          <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
          <script src="bootstrap.min.js"></script>
          <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.12.1/bootstrap-table.min.js"></script>

          <script>
            var $table = $('#bootstrap-table');
            $().ready(function(){
              $table.bootstrapTable({
                toolbar: ".toolbar",
                clickToSelect: true,
                search: true,
                showToggle: false,
                showColumns: false,
                pagination: true,
                searchAlign: 'left',
                pageSize: 5,
                clickToSelect: false,

                formatShowingRows: function(pageFrom, pageTo, totalRows){
                  //do nothing here, we don't want to show the text "showing x of y from..."
                },
                formatRecordsPerPage: function(pageNumber){
                  return pageNumber + " rows visible";
                }
              });
              $(window).resize(function () {
                $table.bootstrapTable('resetView');
              });
            });

            function openModal(folderId) {
              var modal = document.getElementById("dateModal");
              var folderIdInput = document.getElementById("folderId");
              folderIdInput.value = folderId;
              modal.style.display = "block";
            }

            document.addEventListener("DOMContentLoaded", function() {
              var modal = document.getElementById("dateModal");
              var span = document.getElementsByClassName("close")[0];

              span.onclick = function() {
                modal.style.display = "none";
              }

              window.onclick = function(event) {
                if (event.target == modal) {
                  modal.style.display = "none";
                }
              }

              document.getElementById("dateForm").onsubmit = function(event) {
                event.preventDefault();
                var formData = new FormData(this);
                fetch('new_update_statuts.php', {
                  method: 'POST',
                  body: formData
                }).then(response => {
                  if (response.ok) {
                    alert('Folder status updated successfully.');
                    location.reload();
                  } else {
                    alert('Error updating folder status.');
                  }
                });
              };
            });
          </script>
          <!-- Date Modal -->
          <div id="dateModal" class="modal">
            <div class="modal-content">
              <span class="close">&times;</span>
              <h2>Select a Date</h2>
              <form id="dateForm">
                <input type="hidden" id="folderId" name="folder_id">
                <label for="deliveryDate">Delivery Date:</label>
                <input type="date" id="deliveryDate" name="delivery_date" required>
                <button type="submit">Submit</button>
              </form>
            </div>
          </div>

          <!-- Add the following CSS for modal styling -->
          <style>
            .modal {
              display: none;
              position: fixed;
              z-index: 1;
              padding-top: 100px;
              left: 0;
              top: 0;
              width: 100%;
              height: 100%;
              overflow: auto;
              background-color: rgb(0,0,0);
              background-color: rgba(0,0,0,0.4);
            }

            .modal-content {
              background-color: #fefefe;
              margin: auto;
              padding: 20px;
              border: 1px solid #888;
              width: 80%;
            }

            .close {
              color: #aaa;
              float: right;
              font-size: 28px;
              font-weight: bold;
            }

            .close:hover,
            .close:focus {
              color: black;
              text-decoration: none;
              cursor: pointer;
            }
          </style>
        </div>
      </div>
    </div>
  </main>
</div>
