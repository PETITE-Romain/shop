<?php 
include 'includes/header.php'; 
include 'includes/db.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $motdepasse = trim($_POST['motdepasse']);

    $stmt = $conn->prepare("SELECT id, nom, prenom, mot_de_passe FROM utilisateur WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $nom, $prenom, $hash);
        $stmt->fetch();

        if (password_verify($motdepasse, $hash)) {
            $_SESSION['user'] = [
                'id' => $id,
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email
            ];
            header("Location: index.php");
            exit();
        } else {
            $message = "<div class='alert alert-danger'>Mot de passe incorrect.</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>Email non trouvé.</div>";
    }

    $stmt->close();
}
?>

<h1>Connexion</h1>
<?= $message ?>
<form method="POST" action="">
  <div class="mb-3">
    <label class="form-label">Email</label>
    <input type="email" class="form-control" name="email" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Mot de passe</label>
    <input type="password" class="form-control" name="motdepasse" required>
  </div>
  <button type="submit" class="btn btn-success">Se connecter</button>
</form>

<?php include 'includes/footer.php'; ?>
