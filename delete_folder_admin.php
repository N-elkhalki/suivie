<?php
include 'db_connection.php';

include 'session.php';


    
    if (isset($_POST['folder_delete_btn'])) {
        $id = mysqli_real_escape_string($connection, $_POST['folder_delete_btn']) ;

        $query = "DELETE FROM folders WHERE Id_folder=$id";
        $query_run = mysqli_query($connection,$query);
        if($query_run){
            $_SESSION['success']="Dossier supprimé avec succès";
            header('Location: edit_folder.php');
            exit();

            
        } else {
            $_SESSION['status']="Erreur de suppression du dossier";
            header('Location: edit_folder.php');
            exit();
        }
    }
    ?>

