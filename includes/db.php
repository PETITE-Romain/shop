<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "shop";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}
?>
