<?php
include 'includes/db.php';
include 'includes/header.php';

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT p.*, c.nom AS categorie FROM produit p JOIN categorie c ON p.id_categorie = c.id WHERE p.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$produit = $result->fetch_assoc();

if (!$produit) {
    echo "<div class='container my-5'><h3>Produit introuvable.</h3></div>";
    include 'includes/footer.php';
    exit();
}
?>

<script>
  function fetchOpenFoodFacts() {
    const nomProduit = "<?= addslashes($produit['nom']) ?>";
    const infoDiv = document.getElementById("openfoodfacts-info");

    fetch(`https://world.openfoodfacts.org/cgi/search.pl?search_terms=${encodeURIComponent(nomProduit)}&search_simple=1&action=process&json=1&lc=fr`)
      .then(response => response.json())
      .then(data => {
        if (data.products && data.products.length > 0) {
          const produit = data.products[0];

          const html = `
            <div class="card mt-3">
              <div class="card-header bg-info text-white">Produit ou produit dérivée récupérer grâce à l'API OpenFoodFacts en lien avec le porduit recherché</div>
              <div class="card-body">
                <p><strong>Nom :</strong> ${produit.product_name || 'N/A'}</p>
                <p><strong>Nutri-Score :</strong> ${produit.nutriscore_grade ? produit.nutriscore_grade.toUpperCase() : 'Non disponible'}</p>
                <p><strong>Allergènes :</strong> ${produit.allergens_tags?.join(', ') || 'Non précisé'}</p>
              </div>
            </div>
          `;
          infoDiv.innerHTML = html;
        } else {
          infoDiv.innerHTML = `<div class="alert alert-warning">Aucune information trouvée pour ce produit.</div>`;
        }
      })
      .catch(error => {
        console.error("Erreur API:", error);
        infoDiv.innerHTML = `<div class="alert alert-danger">Erreur lors de la récupération des données.</div>`;
      });
  }
</script>


<div class="container my-5">
  <div class="row">
    <div class="col-md-6">
      <img src="<?= htmlspecialchars($produit['image']) ?>" style="" class="img-fixed-size img-fluid rounded" alt="<?= $produit['nom'] ?>">
    </div>
    <div class="col-md-6">
      <h2><?= htmlspecialchars($produit['nom']) ?></h2>
      <p class="text-muted">Catégorie : <?= htmlspecialchars($produit['categorie']) ?></p>
      <p><?= nl2br(htmlspecialchars($produit['description'])) ?></p>
      <h4 class="text-success"><?= number_format($produit['prix'], 2, ',', ' ') ?> €</h4>
      <form action="ajouter_panier.php" method="post">
        <input type="hidden" name="produit_id" value="<?= $produit['id'] ?>">
        <div class="mb-3">
          <label>Quantité (kg)</label>
          <input type="number" class="form-control" name="quantite" value="1" min="1">
        </div>
        <button type="submit" class="btn btn-success">Ajouter au panier</button><br>
        <button type="button" class="btn btn-outline-info mt-3" onclick="fetchOpenFoodFacts();">En savoir plus</button>
        <div id="openfoodfacts-info" class="mt-3"></div>
      </form>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
