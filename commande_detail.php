<?php
include 'includes/header.php';
include 'includes/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: connexion.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='alert alert-danger'>Commande invalide.</div>";
    exit();
}

$id_commande = intval($_GET['id']);
$user_id = $_SESSION['user']['id'];

$stmt = $conn->prepare("SELECT * FROM commande WHERE id = ? AND id_utilisateur = ?");
$stmt->bind_param("ii", $id_commande, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='alert alert-danger'>Commande introuvable ou accès non autorisé.</div>";
    exit();
}

$commande = $result->fetch_assoc();

$sql = "
    SELECT p.nom, p.prix, cp.quantite
    FROM commande_produit cp
    JOIN produit p ON cp.id_produit = p.id
    WHERE cp.id_commande = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_commande);
$stmt->execute();
$produits = $stmt->get_result();
?>

<div class="container my-5">
    <h2>Détail de la commande #<?= $commande['id'] ?></h2>
    <p>Date : <?= date('d/m/Y H:i', strtotime($commande['date_commande'])) ?></p>
    <p>Total : <strong><?= number_format($commande['total'], 2, ',', ' ') ?> €</strong></p>

    <table class="table table-bordered mt-4">
        <thead class="table-light">
            <tr>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Prix unitaire (€)</th>
                <th>Sous-total (€)</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($p = $produits->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($p['nom']) ?></td>
                    <td><?= $p['quantite'] ?></td>
                    <td><?= number_format($p['prix'], 2, ',', ' ') ?></td>
                    <td><?= number_format($p['prix'] * $p['quantite'], 2, ',', ' ') ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="mes_commandes.php" class="btn btn-secondary mt-3">← Retour à mes commandes</a>
</div>

<?php include 'includes/footer.php'; ?>
