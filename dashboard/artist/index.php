<?php include("../includes/header.php"); ?>
<?php
include("../.././config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $track_name = trim($_POST['track_name']);
    $album_art = trim($_POST['album_art']);
    $genre = trim($_POST['genre']);
    $release_date = trim($_POST['release_date']);

    // ✅ New Fields
    $video_url = trim($_POST['video_url'] ?? null);
    $claim_type = trim($_POST['claim_type'] ?? null);
    $youtube_channel = trim($_POST['youtube_channel'] ?? null);
    $mcn_status = trim($_POST['mcn_status'] ?? 'not_linked');
    
    // ✅ Ensure user is logged in
    session_start();
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

        // ✅ Ensure the upload directories exist
        $track_dir = "uploads/tracks/";
        $cover_dir = "uploads/covers/";
        if (!is_dir($track_dir)) {
            mkdir($track_dir, 0777, true);
        }
        if (!is_dir($cover_dir)) {
            mkdir($cover_dir, 0777, true);
        }

        // ✅ Handle MP3 File Upload & Save Link in DB
        $tracklink = null;
        if (!empty($_FILES['track_file']['name'])) {
            $file_name = time() . "_" . basename($_FILES['track_file']['name']);
            $target_file = $track_dir . $file_name;
            
            if (move_uploaded_file($_FILES['track_file']['tmp_name'], $target_file)) {
                $tracklink = $target_file; // Save only the file path in DB
            } else {
                throw new Exception("Error: File upload failed.");
            }
        } else {
            throw new Exception("Error: No file uploaded.");
        }

        // ✅ Handle Album Cover Upload
        $album_cover = null;
        if (!empty($_FILES['album_cover']['name'])) {
            $cover_name = time() . "_" . basename($_FILES['album_cover']['name']);
            $cover_target = $cover_dir . $cover_name;
            
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (in_array($_FILES['album_cover']['type'], $allowed_types)) {
                if (move_uploaded_file($_FILES['album_cover']['tmp_name'], $cover_target)) {
                    $album_cover = $cover_target; // Save only the file path in DB
                } else {
                    throw new Exception("Error: Cover image upload failed.");
                }
            } else {
                throw new Exception("Error: Invalid cover image format. Only JPG, PNG, GIF, and WEBP allowed.");
            }
        }

        // ✅ Insert track with new fields
        $sql = "INSERT INTO tracks (track_name, album_cover, tracklink, genre, release_date, video_url, claim_type, youtube_channel, mcn_status, user_id, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)"; // Removed NOW() and NULL for default values

$stmt = $pdo->prepare($sql);
$stmt->execute([$track_name, $album_cover, $tracklink, $genre, $release_date, $video_url, $claim_type, $youtube_channel, $mcn_status, $user_id]);

        echo "<div class='alert alert-success alert-dismissible fade show' style='text-align: right; position: fixed; top: 70px; right: 10px; z-index: 1000; width: 300px;'>
        New track added successfully
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
      </div>";

    } catch (Exception $e) {
        echo "<div class='alert alert-danger ' style='top: 70px; right: 10px;'>" . $e->getMessage() . "</div>";
    }
}
?>


<style>
    div.container-fluid.px-4.pt-5 {
    max-width: 600px;
    width: 100%;
    padding: 2rem;
    background: #ffffff;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.btn-primary {
    width: 100%;
    padding: 10px;
    background: #007bff;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 18px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.btn-primary:hover {
    background: #0056b3;
}
</style>

<div id="layoutSidenav">
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4 pt-5">
                <h2>Add a New Track</h2>
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="track_name" class="form-label">Track Name</label>
                        <input type="text" class="form-control" id="track_name" name="track_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="track_file" class="form-label">Upload MP3 Track</label>
                        <input type="file" class="form-control" id="track_file" name="track_file" accept="audio/mp3" required>
                    </div>
                    <div>
                        <label for="track_file" class="form-label">Upload Track Cover</label>
                        <input type="file" class="form-control" name="album_cover" accept="image/*" onchange="previewImage(event)">
                        <img id="imagePreview" src="#" alt="Album Cover Preview" style="display:none; width: 100px; height: 100px; margin-top: 10px;"><br>
    <script>
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('imagePreview');
                output.src = reader.result;
                output.style.display = 'block';
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
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

                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </main>
    </div>
</div>
