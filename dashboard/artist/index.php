<?php include("../includes/header.php"); ?>
<?php
include("../.././config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $track_name = trim($_POST['track_name']);
    $album_art = trim($_POST['album_art']);
    $tracklink = trim($_POST['tracklink']);
    $genre = trim($_POST['genre']);
    $release_date = trim($_POST['release_date']);

    // ✅ New Fields
    $video_url = trim($_POST['video_url'] ?? null);
    $claim_type = trim($_POST['claim_type'] ?? null);
    $youtube_channel = trim($_POST['youtube_channel'] ?? null);
    $mcn_status = trim($_POST['mcn_status'] ?? 'not_linked');
    
    // ✅ Get logged-in user's ID from session
    if (!isset($_SESSION['id'])) {
        die("<div class='alert alert-danger'>Error: You must be logged in to add a track.</div>");
    }
    $user_id = $_SESSION['id']; 

    try {
        if (!isset($pdo)) {
            throw new Exception("Database connection failed!");
        }

        // ✅ Check if user_id exists in users table
        $checkUser = $pdo->prepare("SELECT id FROM users WHERE id = ?");
        $checkUser->execute([$user_id]);

        if ($checkUser->rowCount() == 0) {
            throw new Exception("Error: User ID does not exist.");
        }

        // ✅ Insert track with new fields
        $sql = "INSERT INTO tracks (track_name, album_art, tracklink, genre, release_date, video_url, claim_type, youtube_channel, mcn_status, user_id, created_at, updated_at, status, deleted_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), 1, NULL)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$track_name, $album_art, $tracklink, $genre, $release_date, $video_url, $claim_type, $youtube_channel, $mcn_status, $user_id]);

        echo "<div class='alert alert-success alert-dismissible fade show' style='text-align: right; position: fixed; top: 70px; right: 10px; z-index: 1000; width: 300px;'>
        New track added successfully
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
      </div>";

    } catch (Exception $e) {
        echo "<div class='alert alert-danger ' style='top: 70px; right: 10px;'>" . $e->getMessage() . "</div>";
    }
}
?>

<div id="layoutSidenav">
    <?php include("../includes/sidebar.php"); ?> 

    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4 pt-5">
                <h2>Add a New Track</h2>
                <form action="" method="POST">
                <div class="mb-3">
    <label for="track_name" class="form-label">Track Name</label>
    <input type="text" class="form-control" id="track_name" name="track_name" required>
</div>

<div class="mb-3">
    <label for="album_art" class="form-label">Album Art URL</label>
    <input type="text" class="form-control" id="album_art" name="album_art">
</div>

<div class="mb-3">
    <label for="tracklink" class="form-label">Track Link</label>
    <input type="text" class="form-control" id="tracklink" name="tracklink" required>
</div>

<div class="mb-3">
    <label for="genre" class="form-label">Genre</label>
    <input type="text" class="form-control" id="genre" name="genre">
</div>

<div class="mb-3">
    <label for="release_date" class="form-label">Release Date</label>
    <input type="date" class="form-control" id="release_date" name="release_date">
</div>

<!-- ✅ New YouTube & MCN Fields -->
<div class="mb-3">
    <label for="video_url" class="form-label">YouTube Video URL (If Claim Needed)</label>
    <input type="text" class="form-control" id="video_url" name="video_url">
</div>

<div class="mb-3">
    <label for="claim_type" class="form-label">Claim Type (If Needed)</label>
    <select class="form-control" id="claim_type" name="claim_type">
        <option value="">-- Select --</option>
        <option value="manual">Manual Claim</option>
        <option value="release">Claim Release</option>
    </select>
</div>

<div class="mb-3">
    <label for="youtube_channel" class="form-label">YouTube Official Channel</label>
    <input type="text" class="form-control" id="youtube_channel" name="youtube_channel">
</div>

<div class="mb-3">
    <label for="mcn_status" class="form-label">Link to MCN</label>
    <select class="form-control" id="mcn_status" name="mcn_status">
        <option value="not_linked">Not Linked</option>
        <option value="linked">Linked</option>
    </select>
</div>

                    <div class="mb-3 invisible">
                        <label for="user_id" class="form-label">User ID</label>
                        <input type="number" class="form-control" id="user_id" name="user_id" 
                            value="<?php echo isset($_SESSION['id']) ? $_SESSION['id'] : ''; ?>" readonly required>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </main>
    </div>
</div>
