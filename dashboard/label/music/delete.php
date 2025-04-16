<?php
include("../../.././config.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $pdo->prepare("DELETE FROM music_releases WHERE id = ?");
    $stmt->execute([$id]);

    echo "<script>window.location.href = 'index.php';</script>";
    exit();
}
?>
