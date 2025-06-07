<?php
session_start();
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    unset($_SESSION['panier'][$id]);
}
header('Location: panier.php');
exit();
