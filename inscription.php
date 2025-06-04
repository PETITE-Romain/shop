<?php 
include 'includes/header.php'; 
include 'includes/db.php';


$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $motdepasse = trim($_POST['motdepasse']);

    $check = $conn->prepare("SELECT id FROM utilisateur WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $message = "<div class='alert alert-danger'>Un compte existe déjà avec cet email.</div>";
    } else {
        $hash = password_hash($motdepasse, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("INSERT INTO utilisateur (nom, prenom, email, mot_de_passe) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nom, $prenom, $email, $hash);

        if ($stmt->execute()) {
            header("Location: connexion.php?inscription=success");
            exit();
        } else {
            $message = "<div class='alert alert-danger'>Erreur lors de l'inscription.</div>";
        }

        $stmt->close();
    }

    $check->close();
}

?>
<h1>Créer un compte</h1>
<form method="POST" action="inscription.php">
  <div class="mb-3">
    <label for="nom" class="form-label">Nom</label>
    <input type="text" class="form-control" name="nom" placeholder="Entrez votre nom" required>
  </div>
  <div class="mb-3">
    <label for="prenom" class="form-label">Prénom</label>
    <input type="text" class="form-control" name="prenom" placeholder="Entrez votre prénom" required>
  </div>
  <div class="mb-3">
    <label for="email" class="form-label">Email</label>
    <input type="email" class="form-control" name="email" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" placeholder="Veuillez entrer une adresse email valide.">
  </div>
  <div class="mb-3">
    <label for="motdepasse" class="form-label">Mot de passe</label>
    <input type="password" class="form-control" name="motdepasse" placeholder="Veuillez rentrer votre mot de passe" required>
  </div>
  <button type="submit" class="btn btn-success">S'inscrire</button>
</form>

<?php include 'includes/footer.php'; ?>