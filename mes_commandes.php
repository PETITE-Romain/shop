<?php
include 'includes/header.php';
include 'includes/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: connexion.php");
    exit();
}

$user_id = $_SESSION['user']['id'];

$sql = "SELECT id, date_commande, total 
        FROM commande 
        WHERE id_utilisateur = ? 
        ORDER BY date_commande DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container my-5">
    <h2>Mes commandes</h2>

    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered mt-4">
            <thead class="table-success">
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Total (€)</th>
                    <th>Détail</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($row['date_commande'])) ?></td>
                        <td><?= number_format($row['total'], 2, ',', ' ') ?></td>
                        <td><a href="commande_detail.php?id=<?= $row['id'] ?>" class="btn btn-outline-primary btn-sm">Voir</a></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info mt-4">Aucune commande trouvée.</div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
