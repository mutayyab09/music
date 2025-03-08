<?php include("../includes/header.php"); ?>
<?php
include("../.././config.php");

try {
    // ✅ Fetch tracks only for users with role = 2
    $stmt = $pdo->prepare("SELECT tracks.*, users.username, 
            COALESCE(SUM(royalty.amount), 0) AS total_royalties
        FROM tracks
        JOIN users ON tracks.user_id = users.id
        LEFT JOIN royalty ON tracks.id = royalty.track_id
        WHERE users.role = 2 AND tracks.status = 2
        GROUP BY tracks.id, users.username
ORDER BY tracks.created_at DESC;"); 
    $stmt->execute();
    $tracks = $stmt->fetchAll();

} catch (PDOException $e) {
    die("<div class='alert alert-danger'>Error fetching tracks: " . $e->getMessage() . "</div>");
}
?>
<style>
div.container.mt-5 {
    background-color: #f9f9f9;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}
h2 {
    font-size: 28px;
    color: #333;
    margin-bottom: 20px;
    text-align: center;
}
#datatablesSimple {
    width: 100%;
    border-collapse: collapse;
    background-color: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

#datatablesSimple th, #datatablesSimple td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

#datatablesSimple th {
    background-color: #007bff;
    color: #fff;
    font-weight: bold;
}

#datatablesSimple tbody tr:nth-child(even) {
    background-color: #f2f2f2;
}
#datatablesSimple tbody tr:hover {
    background-color: #e9f5ff;
}
div.dt-length {
    gap: 20px;
    display: flex;

    align-items: center;
}
@media (max-width: 768px) {
    #datatablesSimple th, #datatablesSimple td {
        padding: 10px;
        font-size: 14px;
    }
}
.card-body {
    padding: 20px;
}

</style>



<div id="layoutSidenav">
       <div id="layoutSidenav_content">
        <main>
        <div class="container-fluid px-4">
                <div class="mt-5">
                    <h2>All Approved Tracks</h2>
                    <div class="container-fluid px-4"></div>
                    <div class="card mb-4">
                        <div class="card-body">
                            <table id="datatablesSimple">
                                <thead>
                                    <tr>
                                        <th>Artist Name</th>
                                        <th>Track Name</th>
                                        <th>Track Art</th>
                                        <th>Track Link</th>
                                        <th>Genre</th>
                                        <th>Release Date</th>
                                        <th>YouTube Video</th>
                                        <th>Claim Type</th>
                                        <th>YouTube Channel</th>
                                        <th>MCN Status</th>
                                        <th>Royalty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($tracks as $track) : ?>
                                    <tr>
                                        <td><?= htmlspecialchars($track['username']) ?></td>
                                        <td><?= htmlspecialchars($track['track_name']) ?></td>
<td>
    <img src="../artist/uploads/covers/<?= str_replace("uploads/covers", "", htmlspecialchars($track['album_cover'])) ?>" alt="Album Cover" width="100">
</td>

<td>
    <audio controls>
        <source src="<?= str_replace("uploads/covers", "",htmlspecialchars($track['tracklink'])) ?>" type="audio/mp3">
        Your browser does not support the audio element.
    </audio>
</td>
                                        <td><?= htmlspecialchars($track['genre']) ?></td>
                                        <td><?= htmlspecialchars($track['release_date']) ?></td>
                                        <td><?= htmlspecialchars($track['video_url']) ?></td>
                                        <td><?= htmlspecialchars($track['claim_type']) ?></td>
                                        <td><?= htmlspecialchars($track['youtube_channel']) ?></td>
                                        <td><?= htmlspecialchars($track['mcn_status']) ?></td>
                                        <td>
                                            
                                                <span>Total Royalty: </span>
                                                <span><?= $track["total_royalties"]?> ₹</span>
                                                <hr>
                                                <!-- <input type="hidden" name="track_id" value="<?= $track['id'] ?>">
                                                <input type="hidden" name="user_id" value="<?= $track['user_id'] ?>">
                                                 -->
                                                <a href="addRoyalty.php?id=<?= $track['id'] ?>">
                                                    <button type="button" class="btn btn-sm btn-primary">Add Royalty</button>
                                                </a>
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
        <script>
    let table = new DataTable('#datatablesSimple');
</script>
    </div>
</div>
