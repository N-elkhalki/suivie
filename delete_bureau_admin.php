<?php
include 'db_connection.php';

include 'session.php';


    
    if (isset($_POST['bureau_delete_btn'])) {
        $id = mysqli_real_escape_string($connection, $_POST['bureau_delete_btn']) ;

        $query = "DELETE FROM bureau WHERE Id_Bureau=$id";
        $query_run = mysqli_query($connection,$query);
        if($query_run){
            $_SESSION['success']="Bureau supprimé avec succès";
            header('Location: edit_bureau.php');
            exit();

            
        } else {
            $_SESSION['status']="Erreur de suppression du bureau";
            header('Location: edit_bureau.php');
            exit();
        }
    }
    ?>

