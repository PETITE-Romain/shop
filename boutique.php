<?php
include 'includes/db.php';
include 'includes/header.php';

// Récupération des filtres
$categorie_filtre = $_GET['categorie'] ?? '';
$tri_prix = $_GET['tri_prix'] ?? '';

// Récupération des catégories
$categories = $conn->query("SELECT * FROM categorie");

// Construction de la requête produits
$sql = "
    SELECT p.id, p.nom, p.prix, p.image, c.nom AS categorie
    FROM produit p
    JOIN categorie c ON p.id_categorie = c.id
";

$conditions = [];

if (!empty($categorie_filtre)) {
    $conditions[] = "p.id_categorie = " . intval($categorie_filtre);
}

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

if ($tri_prix === 'asc') {
    $sql .= " ORDER BY p.prix ASC";
} elseif ($tri_prix === 'desc') {
    $sql .= " ORDER BY p.prix DESC";
}

$produits = $conn->query($sql);
?>

<div class="container my-5">
  <h2>Nos produits</h2>
  <div class="row">
    <div class="col-md-3">
      <form method="get" class="border p-3 rounded bg-light">
        <h5>Filtrer</h5>

        <div class="mb-3">
          <label for="categorie" class="form-label">Catégorie</label>
          <select name="categorie" id="categorie" class="form-select">
            <option value="">Toutes</option>
            <?php while($cat = $categories->fetch_assoc()): ?>
              <option value="<?= $cat['id'] ?>" <?= ($categorie_filtre == $cat['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['nom']) ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="mb-3">
          <label for="tri_prix" class="form-label">Trier par prix</label>
          <select name="tri_prix" id="tri_prix" class="form-select">
            <option value="">Aucun</option>
            <option value="asc" <?= ($tri_prix === 'asc') ? 'selected' : '' ?>>Prix croissant</option>
            <option value="desc" <?= ($tri_prix === 'desc') ? 'selected' : '' ?>>Prix décroissant</option>
          </select>
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-2">Appliquer</button>
        <a href="<?= strtok($_SERVER["REQUEST_URI"], '?') ?>" class="btn btn-secondary w-100">Réinitialiser les filtres</a>
      </form>
    </div>

    <div class="container my-5">
    <div class="row">
      <?php while($p = $produits->fetch_assoc()): ?>
        <div class="col-md-4">
          <div class="card mb-4">
            <img src="<?= htmlspecialchars($p['image']) ?>" class="card-img-top object-fit-cover" style="height: 300px;" alt="<?= htmlspecialchars($p['nom']) ?>">
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($p['nom']) ?></h5>
              <p class="card-text">Catégorie : <?= htmlspecialchars($p['categorie']) ?></p>
              <p class="fw-bold"><?= number_format($p['prix'], 2, ',', ' ') ?> €</p>
              <form action="ajouter_panier.php" method="post">
                <input type="hidden" name="produit_id" value="<?= $p['id'] ?>">
                <input type="number" name="quantite" value="1" min="1" class="form-control mb-2">
                <button type="submit" class="btn btn-success w-100">Ajouter au panier</button>
              </form>
              <a href="produit.php?id=<?= $p['id'] ?>" class="btn btn-outline-secondary mt-2 w-100">Détail</a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
    </div>
</div>
</div>

<?php include 'includes/footer.php'; ?>
