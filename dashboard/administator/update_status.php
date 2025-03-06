<?php
include("../../config.php"); // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['track_id'], $_POST['status'])) {
    $track_id = $_POST['track_id'];
    $status = $_POST['status'];

    try {
        $stmt = $pdo->prepare("UPDATE tracks SET status = ? WHERE id = ?");
        $stmt->execute([$status, $track_id]);

        // ✅ Redirect back to the page
        header("Location: {$_SERVER['HTTP_REFERER']}");
        exit;
    } catch (Exception $e) {
        echo "Error updating status: " . $e->getMessage();
    }
}
?>
