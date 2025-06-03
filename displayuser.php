<?php $pageTitle = "Liste des utilisateurs"; ?>
<?php
include 'db_connection.php';
include 'session.php';
include 'sidebar.php';

$privilege = $_SESSION['privilege'];
if ($privilege == 2 ) {
    // Redirect to an unauthorized access page or display an error message
    header("Location: index.php");
    exit;
}
?>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="js/datatables/datatables-simple-demo.js"></script><script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.core.min.js" integrity="sha512-UhlYw//T419BPq/emC5xSZzkjjreRfN3426517rfsg/XIEC02ggQBb680V0VvP+zaDZ78zqse3rqnnI5EJ6rxA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
      <div id="layoutSidenav_content">
         <main>
            <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
               <div class="container-xl px-4">
                  <div class="page-header-content pt-4">
                     <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                           <h1 class="page-header-title">
                              <div class="page-header-icon"><i data-feather="plus-square"></i></div>
                              Liste des utilisateurs
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
				<div class="col-md-4 text-right">  </div>
<?php
// Retrieve the user data from the database
$query = "SELECT * FROM user";
$result = mysqli_query($connection, $query);
// Check if any users are found
if ($result && mysqli_num_rows($result) > 0) {

    echo '<table id="datatablesSimple"  >';
    echo '<thead >';
    echo '<tr>';

    echo '<th >Email</th>';
    echo '<th >Mot de passe</th>';
    echo '<th >Nom</th>';
    echo '<th >Date de naissance</th>';
    echo '<th >Privilège</th>';
    echo '<th >Action</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    // Loop through each user and display the data
    while ($row = mysqli_fetch_assoc($result)) {
        $id = $row['Id_user'];
        $Login = $row['Login'];
        $Password = $row['Password'];
        $Name = $row['Name'];
        $Privilege = $row['Privilege'];
        $Date = $row['Date'];
        // Determine the user privilege text based on the privilege value
        $privilegeText = '';
        if ($Privilege == 1) {
            $privilegeText = 'Admin';
        } elseif ($Privilege == 2) {
            $privilegeText = 'Superviseur';
        } else {
            $privilegeText = 'User';
        }

        // Display the user data in the table
        echo '<tr>';
        echo '<td>' . $Login . '</td>';
        echo '<td>*********</td>';
        echo '<td>' . $Name . '</td>';
        echo '<td>' . $Date . '</td>';
        echo '<td>' . $privilegeText . '</td>';
        echo '<td><a class="btn btn-datatable btn-icon btn-transparent-dark me-2" type="submit" href="Edit.php?id=' . $id . '"><i data-feather="edit"></i></a>';
        echo '<form action = "deleteuser.php" method="POST" class="d-inline">';
        echo '<button class="btn btn-datatable btn-icon btn-transparent-dark" type="submit" name="user_delete_btn" value=' . $id . '><i data-feather="trash-2"></i></button>';
        echo '</form></td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table><br>';
    // echo '<button type="submit"><a href="deleteuser.php?id=' . $id . '">Delete Selected Folders</a></button>';

} else {
    echo 'Aucun utilisateur trouvé.';
}

// Close the database connection
mysqli_close($connection);
?>
</div>
</div>
</div>
</div>
</main>
</div>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
		<script src="js/datatables/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="js/datatables/datatables-simple-demo.js"></script>
</body>
</html>