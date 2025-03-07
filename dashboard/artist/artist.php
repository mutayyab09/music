<?php include("../includes/header.php"); ?>

<link href="//cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css" rel="stylesheet"/>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="//cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>

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

<script>
    let table = new DataTable('#datatablesSimple');
</script>