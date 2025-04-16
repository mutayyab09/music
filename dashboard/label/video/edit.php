<?php include("../../includes/header.php"); ?>
<link href="//cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css" rel="stylesheet" />
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="//cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
<style>
.upload-area {
    border: 2px dashed #ccc;
    padding: 20px;
    text-align: center;
    cursor: pointer;
}

.upload-area:hover {
    background-color: #f9f9f9;
}

#musicForm {
    display: none;
}

#customAudioPlayer {
    display: flex;
    align-items: center;
    background: #f8f8f8;
    padding: 8px;
    border-radius: 8px;
    width: 100%;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

#seekBar {
    flex-grow: 1;
    margin: 0 10px;
    cursor: pointer;
}

#playPauseBtn {
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
}

#volumeControl {
    width: 50px;
    margin-left: 10px;
}

.stores-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 10px;
}

.store-item {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
    border-radius: 10px;
    cursor: pointer;
    border: 2px solid transparent;
    transition: all 0.3s;
}

.store-item img {
    max-width: 100%;
    height: auto;
    border-radius: 5px;
}

.store-item.selected {
    border-color: blue;
}
</style>
<?php
include("../../.././config.php");

try {
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']); // Ensure ID is an integer
        $stmt = $pdo->prepare("SELECT * FROM video_releases WHERE id = ?");
        $stmt->execute([$id]);
        $video = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$video) {
            throw new Exception("❌ Record not found!");
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $title = $_POST['title'];
        $type = $_POST['type'];
        $primary_artist = $_POST['primary_artist'];
        $genre_sub_genre = $_POST['genre_sub_genre'];
        $language = $_POST['language'];
        $physical_release_date = $_POST['physical_release_date'];
        $writers = $_POST['writers'];
        $music_directors = $_POST['music_directors'];
        $producers = $_POST['producers'];
        $isrc = $_POST['isrc'];
        $publisher = $_POST['publisher'];
        $label = $_POST['label'];
        $vevo_channel = $_POST['vevo_channel'];
        $description = $_POST['description'];
        $keywords = $_POST['keywords'];
        $store_release_date = $_POST['store_release_date'];
        $youtube_video_url = $_POST['youtube_video_url'];
    
        $thumbnail_image = $video['thumbnail_image']; // Keep the old image by default
    
        // Handle new file upload
        if (isset($_FILES['thumbnail_image']) && $_FILES['thumbnail_image']['error'] == 0) {
            $ext = pathinfo($_FILES["thumbnail_image"]["name"], PATHINFO_EXTENSION);
            if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png'])) {
                $thumbnail_image = "uploads/" . uniqid() . "." . $ext;
                move_uploaded_file($_FILES["thumbnail_image"]["tmp_name"], $thumbnail_image);
            }
        }
    
        try {
            $query = "UPDATE video_releases SET 
                title = :title, 
                type = :type, 
                primary_artist = :primary_artist, 
                genre_sub_genre = :genre_sub_genre, 
                language = :language, 
                physical_release_date = :physical_release_date, 
                writers = :writers, 
                music_directors = :music_directors, 
                producers = :producers, 
                isrc = :isrc, 
                publisher = :publisher, 
                label = :label, 
                vevo_channel = :vevo_channel, 
                description = :description, 
                thumbnail_image = :thumbnail_image, 
                keywords = :keywords, 
                store_release_date = :store_release_date, 
                youtube_video_url = :youtube_video_url
                WHERE id = :id";
    
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':title' => $title,
                ':type' => $type,
                ':primary_artist' => $primary_artist,
                ':genre_sub_genre' => $genre_sub_genre,
                ':language' => $language,
                ':physical_release_date' => $physical_release_date,
                ':writers' => $writers,
                ':music_directors' => $music_directors,
                ':producers' => $producers,
                ':isrc' => $isrc,
                ':publisher' => $publisher,
                ':label' => $label,
                ':vevo_channel' => $vevo_channel,
                ':description' => $description,
                ':thumbnail_image' => $thumbnail_image,
                ':keywords' => $keywords,
                ':store_release_date' => $store_release_date,
                ':youtube_video_url' => $youtube_video_url,
                ':id' => $id
            ]);
    
            echo "Record updated successfully!";
            echo "<script>window.location.href = 'index.php';</script>";
            exit;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>




<div id="layoutSidenav">
    <div id="layoutSidenav_content">
        <main>
            <div class="container " style="padding-top: 40px;">
                <div class="d-flex justify-content-between mb-3">
                    <button class="btn btn-outline-primary step-btn" data-step="1">Release</button>
                    <button class="btn btn-outline-primary step-btn" data-step="2">Media</button>
                    <button class="btn btn-outline-primary step-btn" data-step="3">Store</button>
                    <button class="btn btn-outline-primary step-btn" data-step="4">Submission</button>
                </div>
                <form id="multiStepForm" action="" method="POST" enctype="multipart/form-data"
                    class="card p-4 shadow-sm">
                    <div class="step step-1">
                        <div class="mt-4 row">
                            <h4>Basic Information</h4>
                            <div class="col-md-6">
                                <label>Title</label>
                                <input type="text" name="title" class="form-control"
                                    value="<?= htmlspecialchars($video['title']) ?>" required>

                            </div>
                            <div class="col-md-6">

                                <label>Type</label>
                                <input type="text" name="type" class="form-control"
                                    value="<?= htmlspecialchars($video['type']) ?>" required>

                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Primary Artist</label>
                                <input type="text" name="primary_artist" class="form-control"
                                    value="<?= htmlspecialchars($video['primary_artist']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Genre & Subgenre</label>
                                <input type="text" name="genre_sub_genre" class="form-control"
                                    value="<?= htmlspecialchars($video['genre_sub_genre']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="language">Language</label>
                                <select class="form-control" name="language">
                                    <option <?= $video['language'] == 'English' ? 'selected' : '' ?>>English</option>
                                    <option <?= $video['language'] == 'Urdu' ? 'selected' : '' ?>>Urdu</option>
                                    <option <?= $video['language'] == 'Hindi' ? 'selected' : '' ?>>Hindi</option>
                                    <option <?= $video['language'] == 'Other' ? 'selected' : '' ?>>Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="release_date" class="form-label">Physical Release Date</label>
                                <input type="text" name="physical_release_date" class="form-control"
                                    value="<?= htmlspecialchars($video['physical_release_date']) ?>">
                            </div>
                        </div>
                        <div class="mt-4 row">
                            <h4>Production Details</h4>
                            <div class="col-md-6">
                                <label class="form-label" for="writers">Writers</label>
                                <input type="text" name="writers" class="form-control"
                                    value="<?= htmlspecialchars($video['writers']) ?>">

                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="musicDirectors">Music Directors</label>
                                <input type="text" name="music_directors" class="form-control"
                                    value="<?= htmlspecialchars($video['music_directors']) ?>">

                            </div>
                        </div>
                        <div class="mt-4 row">
                            <h4>Identification & Tracking</h4>
                            <div class="col-md-6">
                                <label class="form-label" for="Producer">Producer</label>
                                <input type="text" name="producers" class="form-control"
                                    value="<?= htmlspecialchars($video['producers']) ?>">

                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="isrc">ISRC</label>
                                <input type="text" name="isrc" class="form-control"
                                    value="<?= htmlspecialchars($video['isrc']) ?>">

                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="publisher">Publisher</label>
                                <input type="text" name="publisher" class="form-control"
                                    value="<?= htmlspecialchars($video['publisher']) ?>">

                            </div>

                        </div>
                        <div class="mt-4 row">
                            <h4>Distribution & Promotion</h4>
                            <div class="col-md-6">
                                <label for="label" class="form-label">Label</label>
                                <input type="text" name="label" class="form-control"
                                    value="<?= htmlspecialchars($video['label']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="vevo_channel" class="form-label">Vevo Channel</label>
                                <input type="text" name="vevo_channel" class="form-control"
                                    value="<?= htmlspecialchars($video['vevo_channel']) ?>">
                            </div>
                        </div>
                        <div class="mt-4 row">
                            <h4>Additional Information</h4>
                            <div class="col-md-6">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description"
                                    class="form-control"><?= htmlspecialchars($video['description']) ?></textarea>
                            </div>


                            <div class="col-md-6" hidden>

                                <label class="form-label">Cover</label>
                                <input type="file" class="form-control" id="coverImage">
                                <small class="text-muted">Please note that only JPG files of 1920x1080 px are permitted
                                    for upload.</small>
                            </div>

                            <div class="col-md-6">
                                <label for="release_date" class="form-label">Cover</label>
                                <input type="file" class="form-control" id="coverImage" name="thumbnail_image" accept="image/*"
                                    value="<?= htmlspecialchars($video['thumbnail_image']) ?>" readonly
                                    onchange="previewImage(event)">
                                <img id="imagePreview" src="" alt="Image Preview"
                                    style="display:none; width: 120px; height: auto;">
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
                                <br>
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

                        </div>
                        <div class="col-md-6">
                            <label for="keywords" class="form-label">Keywords</label>
                            <input type="text" name="keywords" class="form-control"
                                value="<?= htmlspecialchars($video['keywords']) ?>">
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-primary next">Next</button>
                        </div>
                    </div>
                    <div class="step step-2 d-none">
                        <div class="container mt-4">
                            <div class="container mt-5 d-flex justify-content-center">
                                <div class="card shadow-lg p-3" style="width: 500px; border-radius: 10px;">
                                    <label class="form-label fw-bold">YouTube Video URL <span
                                            class="text-danger">*</span></label>
                                    <input type="text" id="youtubeURL" name="youtube_video_url" class="form-control"
                                        value="<?= htmlspecialchars($video['youtube_video_url']) ?>"
                                        placeholder="Enter YouTube Video URL">

                                    <div class="d-flex justify-content-end mt-3">
                                        <button type="button" class="btn btn-primary" onclick="loadYouTubeVideo()">Load
                                            Video</button>

                                    </div>
                                </div>
                            </div>

                            <!-- Video Preview -->
                            <div id="videoPreview" class="mt-4 text-center"></div>
                            <script>
                            function getYouTubeVideoId(url) {
                                let videoId = "";

                                // Check for standard YouTube URL
                                let match = url.match(/(?:https?:\/\/)?(?:www\.)?youtube\.com\/watch\?v=([^&]+)/);
                                if (match) {
                                    videoId = match[1];
                                } else {
                                    // Check for shortened YouTube URL
                                    match = url.match(/(?:https?:\/\/)?youtu\.be\/([^?]+)/);
                                    if (match) {
                                        videoId = match[1];
                                    }
                                }

                                return videoId;
                            }

                            function loadYouTubeVideo(event) {
                                event?.preventDefault(); // Prevent form submission if event exists
                                let url = document.getElementById('youtubeURL').value;
                                let videoId = getYouTubeVideoId(url);

                                if (videoId) {
                                    document.getElementById('videoPreview').innerHTML = `
        <iframe width="1180" height="800" 
            src="https://www.youtube.com/embed/${videoId}" 
            frameborder="0" allowfullscreen>
        </iframe>`;
                                } else {
                                    alert("Invalid YouTube URL!");
                                }
                            }
                            </script>
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-primary next">Next</button>
                        </div>


                    </div>
                    <div class="step step-3 d-none">
                        <h3>Stores</h3>
                        <p>Manage stores.</p>

                        <div class="store-selection">
                            <button id="selectAll">Select All</button>
                            <button id="deselectAll">Deselect All</button>

                            <div id="storeList" class="stores-grid">
                                <!-- Icons dynamically added here -->
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-4">

                            <button type="submit" class="btn btn-success">Update</button>

                        </div>
                    </div>
            </div>
            </form>
    </div>
    </main>
</div>
</div>


<script>
document.addEventListener("DOMContentLoaded", function() {
    const nextButtons = document.querySelectorAll(".next");

    nextButtons.forEach(button => {
        button.addEventListener("click", function() {
            const currentStep = this.closest(".step");
            const inputs = currentStep.querySelectorAll(
                "input[required], textarea[required], select[required]");
            let allFilled = true;

            inputs.forEach(input => {
                if (!input.value.trim()) {
                    input.classList.add("is-invalid");
                    allFilled = false;
                } else {
                    input.classList.remove("is-invalid");
                }
            });

            if (allFilled) {
                const nextStep = currentStep.nextElementSibling;
                if (nextStep && nextStep.classList.contains("step")) {
                    currentStep.classList.add("d-none");
                    nextStep.classList.remove("d-none");
                }
            }
        });
    });

    // Remove validation error on input change
    document.querySelectorAll("input, textarea, select").forEach(input => {
        input.addEventListener("input", function() {
            if (this.value.trim()) {
                this.classList.remove("is-invalid");
            }
        });
    });
});



const audio = document.getElementById("audio");
const playPauseBtn = document.getElementById("playPauseBtn");
const seekBar = document.getElementById("seekBar");
const volumeControl = document.getElementById("volumeControl");
const audioTime = document.getElementById("audioTime");
const audioDuration = document.getElementById("audioDuration");

playPauseBtn.addEventListener("click", () => {
    if (audio.paused) {
        audio.play();
        playPauseBtn.textContent = "⏸"; // Pause icon
    } else {
        audio.pause();
        playPauseBtn.textContent = "▶"; // Play icon
    }
});

// Update progress bar
audio.addEventListener("timeupdate", () => {
    seekBar.value = (audio.currentTime / audio.duration) * 100;
    audioTime.textContent = formatTime(audio.currentTime);
});

// Seek functionality
seekBar.addEventListener("input", () => {
    audio.currentTime = (seekBar.value / 100) * audio.duration;
});

// Volume control
volumeControl.addEventListener("input", () => {
    audio.volume = volumeControl.value;
});

// Format time function
function formatTime(time) {
    let minutes = Math.floor(time / 60);
    let seconds = Math.floor(time % 60);
    return `${minutes}:${seconds < 10 ? "0" : ""}${seconds}`;
}

// Set duration when metadata loads
audio.addEventListener("loadedmetadata", () => {
    audioDuration.textContent = formatTime(audio.duration);
});


document.addEventListener("DOMContentLoaded", function() {
    const uploadArea = document.getElementById("uploadArea");
    const fileInput = document.getElementById("fileInput");
    const filePreview = document.getElementById("filePreview");
    const musicForm = document.getElementById("musicForm");

    // Default form hidden rahega
    musicForm.style.display = "none";

    uploadArea.addEventListener("click", () => fileInput.click());

    uploadArea.addEventListener("dragover", (e) => {
        e.preventDefault();
        uploadArea.style.backgroundColor = "#f1f1f1";
    });

    uploadArea.addEventListener("dragleave", () => {
        uploadArea.style.backgroundColor = "";
    });

    uploadArea.addEventListener("drop", (e) => {
        e.preventDefault();
        uploadArea.style.backgroundColor = "";
        handleFiles(e.dataTransfer.files);
    });

    fileInput.addEventListener("change", () => handleFiles(fileInput.files));

    function handleFiles(files) {
        if (files.length > 0) {
            const file = files[0];
            if (file.name.toLowerCase().endsWith(".wav")) {
                filePreview.innerHTML = `<p class='text-success'>Uploaded: ${file.name}</p>`;
                musicForm.style.display = "block"; // ✅ Form show karna hai
            } else {
                filePreview.innerHTML =
                    `<p class='text-danger'>Invalid file type! Please upload a WAV file.</p>`;
                fileInput.value = ""; // Reset file input
                musicForm.style.display = "none"; // ❌ Invalid file to form hide
            }
        }
    }

});
</script>