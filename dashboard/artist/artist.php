<?php include("../includes/header.php"); ?>
<?php
include("../.././config.php");

try {
    // ✅ Ensure session is started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // ✅ Get logged-in user ID from session
    $user_id = $_SESSION["id"];

    // ✅ Fetch tracks only for the logged-in user
    $stmt = $pdo->prepare("SELECT * FROM tracks WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $tracks = $stmt->fetchAll();

} catch (PDOException $e) {
    die("<div class='alert alert-danger'>Error fetching tracks: " . $e->getMessage() . "</div>");
}
?>

<div id="layoutSidenav">
    <?php include("../includes/sidebar.php"); ?> 
       <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <div class="container mt-5">
                   <h2>Welcome Artist</h2>
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
                                            <th>YouTube Video</th>
                                            <th>Claim Type</th>
                                            <th>YouTube Channel</th>
                                            <th>MCN Status</th>
                                        </tr>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                        <th>Track Name</th>
                                            <th>Album Art</th>
                                            <th>Track Link</th>
                                            <th>Genre</th>
                                            <th>Release Date</th>
                                            <th>YouTube Video</th>
                                            <th>Claim Type</th>
                                            <th>YouTube Channel</th>
                                            <th>MCN Status</th>
                                            <th>Status</th>
                                        </tr>
                                    
                                    </tfoot>
                                    <tbody>
                                    <?php foreach ($tracks as $track) : ?>
                <tr>
                
                    <td><?= htmlspecialchars($track['track_name']) ?></td>
                    <td><?= htmlspecialchars($track['album_art']) ?></td>
                    <td><?= htmlspecialchars($track['tracklink']) ?></td>
                    <td><?= htmlspecialchars($track['genre']) ?></td>
                    <td><?= htmlspecialchars($track['release_date']) ?></td>
                    <td><?= htmlspecialchars($track['video_url']) ?></td>
                    <td><?= htmlspecialchars($track['claim_type']) ?></td>
                    <td><?= htmlspecialchars($track['youtube_channel']) ?></td>
                    <td><?= htmlspecialchars($track['mcn_status']) ?></td> 
                    <td>
                                                <?php 
                                                    switch ($track['status']) {
                                                        case 0: echo "Declined"; break;
                                                        case 1: echo "In Progress"; break;
                                                        case 2: echo "Accepted"; break;
                                                        default: echo "Unknown"; 
                                                    }
                                                ?>
                                            </td>
                </tr>
            <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
            </div>
        </main>
        <?php //include("../includes/footer.php"); ?>
    </div>
</div>

