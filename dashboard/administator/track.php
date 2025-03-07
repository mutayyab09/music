<?php include("../includes/header.php"); ?>
<?php
include("../.././config.php");

try {
    // ✅ Fetch tracks only for users with role = 2
    $stmt = $pdo->prepare("SELECT tracks.*, users.username FROM tracks JOIN users ON tracks.user_id = users.id WHERE users.role = 2 ORDER BY tracks.created_at DESC"); 
    $stmt->execute();
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
                    <h2>All Artist Tracks</h2>
                    <div class="container-fluid px-4"></div>
                    <div class="card mb-4">
                        <div class="card-body">
                            <table id="datatablesSimple">
                                <thead>
                                    <tr>
                                        <th>Artist Name</th>
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
                                </thead>
                                <tbody>
                                <?php foreach ($tracks as $track) : ?>
                                    <tr>
                                        <td><?= htmlspecialchars($track['username']) ?></td>
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
                                            <form method="POST" action="update_status.php">
                                                <input type="hidden" name="track_id" value="<?= $track['id'] ?>">
                                                <select name="status" class="form-select" onchange="this.form.submit()">
                                                    <option value="0" <?= $track['status'] == 0 ? 'selected' : '' ?>>Declined</option>
                                                    <option value="1" <?= $track['status'] == 1 ? 'selected' : '' ?>>In Progress</option>
                                                    <option value="2" <?= $track['status'] == 2 ? 'selected' : '' ?>>Accepted</option>
                                                </select>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>