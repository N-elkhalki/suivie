<?php
include 'db_connection.php';
include 'session.php';


    
    if (isset($_POST['user_delete_btn'])) {
        $id = mysqli_real_escape_string($connection, $_POST['user_delete_btn']) ;

        $query = "DELETE FROM user WHERE Id_user=$id";
        $query_run = mysqli_query($connection,$query);
        if($query_run){
            $_SESSION['success']="Utilisateur supprimé avec succès";
            header('Location: displayuser.php');
            exit();

            
        } else {
            $_SESSION['status']="Erreur de suppression de l'utilisateur";
            header('Location: displayuser.php');
            exit();
        }
    }
    ?>

