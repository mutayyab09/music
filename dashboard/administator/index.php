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
        <div class="container-fluid px-4">
                        
                        </div>
                        <div class="card mb-4">
                        
                            <div class="card-body">
                                <table id="datatablesSimple">
                                    <thead>
                                        <tr>
                                            <th>Track Name</th>
                                            <th>Album Art</th>
                                            <th>Track Link</th>
                                            <th>Genre</th>
                                            <th>Release Date</th>
                                            <th>User_id</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                  
                                    <tbody>
                                    <?php foreach ($tracks as $track) : ?>
                <tr>
                
                    <td><?= htmlspecialchars($track['track_name']) ?></td>
                    <td><?= htmlspecialchars($track['album_art']) ?></td>
                    <td><?= htmlspecialchars($track['tracklink']) ?></td>
                    <td><?= htmlspecialchars($track['genre']) ?></td>
                    <td><?= htmlspecialchars($track['release_date']) ?></td>
                    <td><?= htmlspecialchars($track['user_id']) ?></td>
                    <td><?= htmlspecialchars($track['status']) ?></td>
                  
                </tr>
            <?php endforeach; ?>
                                        </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
    </div>
               
            </div>
        </main>

    </div>
</div>
