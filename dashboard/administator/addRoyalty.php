<?php include("../includes/header.php"); ?>
<?php
error_reporting(E_ERROR | E_PARSE);


include("../.././config.php");

// Ensure user is logged in
session_start();
if (!isset($_SESSION['id'])) {
    die("<div class='alert alert-danger'>Error: You must be logged in.</div>");
}
$admin_id = $_SESSION['id'];

// Ensure track_id is provided in the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("<div class='alert alert-danger'>Error: Invalid track ID.</div>");
}
$track_id = $_GET['id'];

try {
    if (!isset($pdo)) {
        throw new Exception("Database connection failed!");
    }

    // Fetch track details
    $trackQuery = $pdo->prepare("SELECT track_name, genre, release_date,user_id FROM tracks WHERE id = ?");
    $trackQuery->execute([$track_id]);
    $track = $trackQuery->fetch(PDO::FETCH_ASSOC);

    if (!$track) {
        throw new Exception("Error: Track not found.");
    }

    // Fetch existing royalty
    $royaltyQuery = $pdo->prepare("SELECT p.id, p.amount, p.created_at, u.username AS artist_name, p.status
                                    FROM royalty p 
                                    JOIN users u ON p.artist_id = u.id
                                    WHERE p.track_id = ? ORDER BY id desc");
    $royaltyQuery->execute([$track_id]);
    $royalty = $royaltyQuery->fetchAll(PDO::FETCH_ASSOC);

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $amount = trim($_POST['amount']);
        $artist_id = trim($_POST['artist_id']);

        if (empty($amount) || !is_numeric($amount)) {
            throw new Exception("Error: Invalid amount.");
        }
        if (empty($artist_id) || !is_numeric($artist_id)) {
            throw new Exception("Error: Invalid artist ID.");
        }

        $insertPayment = $pdo->prepare("INSERT INTO royalty (amount, track_id, artist_id, created_at, updated_at, status, admin_id) 
                                        VALUES (?, ?, ?, NOW(), NOW(), 1, ?)");
        $insertPayment->execute([$amount, $track_id, $artist_id, $admin_id]);

        echo "<div class='alert alert-success alert-dismissible fade show' style='text-align: right; position: fixed; top: 70px; right: 10px; z-index: 1000; width: 300px;'>
        Royalty Added Succesfully
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
      </div>";
      echo "<script>window.location.href = 'addRoyalty.php?id=2'</script>";
        // header("Refresh:0"); // Refresh page to show updated data
    }
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>" . $e->getMessage() . "</div>";
}
?>

<div id="layoutSidenav">
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4 pt-5">
                <h2>Royalties for Track: <?= htmlspecialchars($track['track_name']) ?></h2>
                <p><strong>Genre:</strong> <?= htmlspecialchars($track['genre']) ?> | <strong>Release Date:</strong> <?= htmlspecialchars($track['release_date']) ?></p>

                <!-- Existing royalty -->
                <h4>Added Royalties</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($royalty)): ?>
                            <tr><td colspan="4">No royalty found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($royalty as $payment): ?>
                                <tr>
                                    <td><?= htmlspecialchars($payment['id']) ?></td>
                                    <td>₹<?= htmlspecialchars($payment['amount']) ?></td>
                                    <td><?= htmlspecialchars($payment['created_at']) ?></td>
                                    <td>Transfered</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <hr>
                <!-- Add New Payment Form -->
                <h4>Add New Royalty</h4>
                <form action="" method="POST">
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount (₹)</label>
                        <input type="number" class="form-control" id="amount" name="amount" required>
                    </div>
                    <div class="mb-3 d-none">
                        <label for="artist_id" class="form-label">Artist ID</label>
                        <input type="number" class="form-control" id="artist_id" name="artist_id" required value="<?= $track["user_id"] ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Payment</button>
                </form>
            </div>
        </main>
    </div>
</div>
