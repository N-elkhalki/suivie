<?php
$servername = "91.216.107.162";
$username = "osour2189855";
$password = "kU8!fVfVK1TPq8W";
$dbname = "osour2189855";

$connection = new mysqli($servername, $username, $password, $dbname);
if ($connection->connect_error) {
    die("La connexion a échoué: " . $connection->connect_error);
}
?>