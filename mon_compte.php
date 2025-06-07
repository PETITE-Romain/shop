<?php
include 'includes/header.php';
include 'includes/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: connexion.php");
    exit();
}

$user = $_SESSION['user'];

$stmt = $conn->prepare("SELECT adresse FROM utilisateur WHERE id = ?");
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$stmt->bind_result($adresse);
$stmt->fetch();
$stmt->close();
?>

<!-- Titre -->
<header class="bg-light text-center py-5">
  <div class="container">
    <h1 class="display-5 fw-bold">Mon Compte</h1>
    <p class="lead">Bienvenue dans votre espace personnel</p>
  </div>
</header>

<!-- Contenu -->
<div class="container my-5">
  <div class="row">
    <div class="col-md-8 offset-md-2">

      <div class="card mb-4">
        <div class="card-header bg-success text-white">
          Informations personnelles
        </div>
        <div class="card-body">
          <p><strong>Nom :</strong> <?= htmlspecialchars($user['nom']) ?></p>
          <p><strong>Email :</strong> <?= htmlspecialchars($user['email']) ?></p>
          <p><strong>Adresse :</strong> <?= htmlspecialchars($adresse ?? "Non renseignée") ?></p>
        </div>
      </div>

      <div class="d-grid gap-2 d-md-block">
        <a href="mes_commandes.php" class="btn btn-success me-2">Mes commandes</a>
        <a href="deconnexion.php" class="btn btn-outline-secondary">Déconnexion</a>
      </div>

    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
