<?php 
include 'includes/header.php';
include 'includes/db.php';

$prenom = isset($_SESSION['user']['prenom']) ? $_SESSION['user']['prenom'] : '';
$nom = isset($_SESSION['user']['nom']) ? $_SESSION['user']['nom'] : '';
$email = isset($_SESSION['user']['email']) ? $_SESSION['user']['email'] : '';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenomPost = trim($_POST['prenom'] ?? '');
    $nomPost = trim($_POST['nom'] ?? '');
    $emailPost = trim($_POST['email'] ?? '');
    $sujetPost = trim($_POST['sujet'] ?? '');
    $messagePost = trim($_POST['message'] ?? '');

    if (!$prenomPost) {
        $errors[] = "Le prénom est requis.";
    }
    if (!$nomPost) {
        $errors[] = "Le nom est requis.";
    }
    if (!$emailPost || !filter_var($emailPost, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Un email valide est requis.";
    }
    if (!$sujetPost) {
        $errors[] = "Le sujet est requis.";
    }
    if (!$messagePost) {
        $errors[] = "Le message est requis.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO contact (prenom, nom, email, sujet, message) VALUES (?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("sssss", $prenomPost, $nomPost, $emailPost, $sujetPost, $messagePost);
            if ($stmt->execute()) {
                $success = "Merci pour votre message, nous vous répondrons rapidement.";
                if (!$prenom && !$nom && !$email) {
                    $prenomPost = $nomPost = $emailPost = '';
                }
                $sujetPost = $messagePost = '';
            } else {
                $errors[] = "Erreur lors de l'enregistrement. Veuillez réessayer.";
            }
            $stmt->close();
        } else {
            $errors[] = "Erreur lors de la préparation de la requête.";
        }
    }
}

$prenomForm = $_SERVER['REQUEST_METHOD'] === 'POST' ? htmlspecialchars($prenomPost ?? '') : htmlspecialchars($prenom);
$nomForm = $_SERVER['REQUEST_METHOD'] === 'POST' ? htmlspecialchars($nomPost ?? '') : htmlspecialchars($nom);
$emailForm = $_SERVER['REQUEST_METHOD'] === 'POST' ? htmlspecialchars($emailPost ?? '') : htmlspecialchars($email);
$sujetForm = $_SERVER['REQUEST_METHOD'] === 'POST' ? htmlspecialchars($sujetPost ?? '') : '';
$messageForm = $_SERVER['REQUEST_METHOD'] === 'POST' ? htmlspecialchars($messagePost ?? '') : '';
?>

<h1>Contactez-nous</h1>

<?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul>
        <?php foreach ($errors as $error): ?>
            <li><?= $error ?></li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" action="">
  <div class="mb-3">
    <label for="prenom" class="form-label">Prénom</label>
    <input type="text" class="form-control" id="prenom" name="prenom" value="<?= $prenomForm ?>" <?= $prenom ? 'readonly' : '' ?> required>
  </div>
  <div class="mb-3">
    <label for="nom" class="form-label">Nom</label>
    <input type="text" class="form-control" id="nom" name="nom" value="<?= $nomForm ?>" <?= $nom ? 'readonly' : '' ?> required>
  </div>
  <div class="mb-3">
    <label for="email" class="form-label">Email</label>
    <input type="email" class="form-control" id="email" name="email" value="<?= $emailForm ?>" <?= $email ? 'readonly' : '' ?> required>
  </div>
  <div class="mb-3">
    <label for="sujet" class="form-label">Sujet</label>
    <input type="text" class="form-control" id="sujet" name="sujet" value="<?= $sujetForm ?>" required>
  </div>
  <div class="mb-3">
    <label for="message" class="form-label">Message</label>
    <textarea class="form-control" id="message" name="message" rows="4" required><?= $messageForm ?></textarea>
  </div>
  <button type="submit" class="btn btn-success">Envoyer</button>
</form>

<?php include 'includes/footer.php'; ?>
