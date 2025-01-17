<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "backend-2";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}
?>
