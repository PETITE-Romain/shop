<?php
include 'includes/header.php';
include 'includes/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: connexion.php");
    exit();
}


$panier = $_SESSION['panier'] ?? [];

if (empty($panier)) {
    header('Location: panier.php');
    exit();
}

$id_utilisateur = $_SESSION['user']['id'];

$total = 0;
$ids = implode(',', array_keys($panier));
$result = $conn->query("SELECT id, prix FROM produit WHERE id IN ($ids)");

$produits = [];
while ($row = $result->fetch_assoc()) {
    $produits[$row['id']] = $row;
    $total += $row['prix'] * $panier[$row['id']];
}

$stmt = $conn->prepare("INSERT INTO commande (nom, total, date_commande, id_utilisateur) VALUES (?, ?, NOW(), ?)");
$nom_commande = "Commande " . date('YmdHis');
$stmt->bind_param("sdi", $nom_commande, $total, $id_utilisateur);
$stmt->execute();
$id_commande = $stmt->insert_id;

$stmt2 = $conn->prepare("INSERT INTO commande_produit (id_commande, id_produit, quantite) VALUES (?, ?, ?)");
foreach ($panier as $id_produit => $quantite) {
    $stmt2->bind_param("iii", $id_commande, $id_produit, $quantite);
    $stmt2->execute();
}

unset($_SESSION['panier']);

header("Location: mes_commandes.php");
exit();
