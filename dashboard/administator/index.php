<?php include("../includes/header.php"); ?>
<?php
include("../.././config.php");
try {
    // ✅ Fetch only tracks where status = 2
    $stmt = $pdo->query("SELECT * FROM tracks WHERE status = 1 ORDER BY created_at DESC");
    $tracks = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("<div class='alert alert-danger'>Error fetching tracks: " . $e->getMessage() . "</div>");
}
?>


<div id="layoutSidenav">
    <?php include("../includes/sidebar.php"); ?> <!-- Sidebar Include -->

    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
               

                <div class="container mt-5">
        <h2>Welcome Administator</h2>

               
            </div>
        </main>

    </div>
</div>
