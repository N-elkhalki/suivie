<?php
$servername = "localhost";
$username = "osour2185";
$password = "kU8!fV145TPq8W";
$dbname = "osour2185";

$connection = new mysqli($servername, $username, $password, $dbname);
if ($connection->connect_error) {
    die("La connexion a échoué: " . $connection->connect_error);
}
?>
