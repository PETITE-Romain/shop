<?php
include 'includes/header.php';
include 'includes/db.php';

$panier = $_SESSION['panier'] ?? [];

if (empty($panier)) {
    echo "<div class='container my-5'><h3>Votre panier est vide.</h3></div>";
    include 'includes/footer.php';
    exit();
}

$ids = implode(',', array_keys($panier));
$result = $conn->query("SELECT id, nom, prix, image FROM produit WHERE id IN ($ids)");

$total = 0;
?>

<div class="container my-5">
  <h2>Votre panier</h2>
  <table class="table">
    <thead>
      <tr>
        <th>Produit</th>
        <th>Image</th>
        <th>Prix</th>
        <th>Quantité</th>
        <th>Sous-total</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): 
        $id = $row['id'];
        $quantite = $panier[$id];
        $sous_total = $row['prix'] * $quantite;
        $total += $sous_total;
      ?>
        <tr>
          <td><?= htmlspecialchars($row['nom']) ?></td>
          <td><img src="<?= htmlspecialchars($row['image']) ?>" alt="" width="50"></td>
          <td><?= number_format($row['prix'], 2, ',', ' ') ?> €</td>
          <td><?= $quantite ?></td>
          <td><?= number_format($sous_total, 2, ',', ' ') ?> €</td>
          <td>
            <a href="supprimer_panier.php?id=<?= $id ?>" class="btn btn-sm btn-danger">Supprimer</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <h4 class="text-end">Total : <?= number_format($total, 2, ',', ' ') ?> €</h4>

  <div class="text-end mt-4">
    <a href="boutique.php" class="btn btn-outline-secondary">Continuer mes achats</a>
    <a href="valider_commande.php" class="btn btn-success">Valider la commande</a>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
