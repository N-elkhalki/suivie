<?php
include('db_connection.php'); // Pour la connexion à la base de données

// Check if a search term was provided
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($connection, $_GET['search']);

    // SQL query to get users matching the search term (you can add your own filters as necessary)
    $sql = "SELECT Id_user, Name FROM user WHERE Name LIKE '%$search%' ORDER BY Name ASC LIMIT 10";
    $result = mysqli_query($connection, $sql);

    // Initialize an array to hold the users
    $users = array();

    // Fetch the results and store them in the array
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }

    // Return the results as JSON
    echo json_encode($users);
}
?>
