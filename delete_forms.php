<?php
include 'db_connection.php';

include 'session.php';


    
    if (isset($_POST['forms_delete_btn'])) {
        $id = mysqli_real_escape_string($connection, $_POST['forms_delete_btn']) ;

        $query = "DELETE FROM formulaire WHERE Id_formulaire=$id";
        $query_run = mysqli_query($connection,$query);
        if($query_run){
            $_SESSION['success']="Formulaire supprimé avec succès";
            header('Location: displayform.php');
            exit();

            
        } else {
            $_SESSION['status']="Erreur de suppression du formulaire";
            header('Location: displayform.php');
            exit();
        }
    }
    ?>

