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
        $stmt = $pdo->prepare("SELECT * FROM music_releases WHERE id = ?");
        $stmt->execute([$id]);
        $release = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$release) {
            throw new Exception("❌ Record not found!");
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // var_dump($_POST);
        // Sanitize inputs
        $title = htmlspecialchars($_POST['title']);
        $subtitle = htmlspecialchars($_POST['subtitle']);
        $genre = htmlspecialchars($_POST['genre']);
        $label = htmlspecialchars($_POST['label']);
        $physical_release_date = $_POST['physical_release_date'];
        $store_release_date = $_POST['store_release_date'];
        $register_line = htmlspecialchars($_POST['register_line']);
        $production_year = intval($_POST['production_year']); // Since it's a YEAR type in DB
        $catalogue_number = htmlspecialchars($_POST['catalogue_number']);
        $track_title = htmlspecialchars($_POST['track_title']);
        $primary_artist = htmlspecialchars($_POST['primary_artist']);
        $writers = htmlspecialchars($_POST['writers']);
        $composers = htmlspecialchars($_POST['composers']);
        $music_directors = htmlspecialchars($_POST['music_directors']);
        $producer = htmlspecialchars($_POST['producer']);
        $publisher = htmlspecialchars($_POST['publisher']);
        $language = htmlspecialchars($_POST['language']);
        $isrc = htmlspecialchars($_POST['isrc']);
        $lyrics = htmlspecialchars($_POST['lyrics']);
        $youtube_url = filter_var($_POST['youtube_url'], FILTER_SANITIZE_URL); // Ensure valid URL

        // Default values
        $album_cover = $release['album_cover'] ?? '';
        $track_file = $release['track_file'] ?? '';

        // Handle cover image upload
        if (!empty($_FILES['album_cover']['name'])) {
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            $max_file_size = 2 * 1024 * 1024; // 2MB
            $file_name = $_FILES["album_cover"]["name"];
            $file_tmp = $_FILES["album_cover"]["tmp_name"];
            $file_size = $_FILES["album_cover"]["size"];
            $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed_extensions)) {
                throw new Exception("❌ Only JPG, JPEG, PNG, and GIF files are allowed.");
            }

            if ($file_size > $max_file_size) {
                throw new Exception("❌ File is too large. Max size: 2MB.");
            }

            $safe_file_name = preg_replace("/[^a-zA-Z0-9-_]/", "", pathinfo($file_name, PATHINFO_FILENAME));
            $new_file_name = "uploads/" . uniqid() . "_" . $safe_file_name . "." . $ext;

            if (move_uploaded_file($file_tmp, $new_file_name)) {
                $album_cover = $new_file_name;
            } else {
                throw new Exception("❌ Failed to upload image.");
            }
        }

        // Handle track file upload (WAV only)
        if (!empty($_FILES['track_file']['name'])) {
            $allowed_extensions = ['wav'];
            $max_file_size = 10 * 1024 * 1024; // 10MB

            $file_name = $_FILES["track_file"]["name"];
            $file_tmp = $_FILES["track_file"]["tmp_name"];
            $file_size = $_FILES["track_file"]["size"];
            $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed_extensions)) {
                throw new Exception("❌ Only WAV files are allowed.");
            }

            if ($file_size > $max_file_size) {
                throw new Exception("❌ File is too large. Max size: 10MB.");
            }

            $safe_file_name = preg_replace("/[^a-zA-Z0-9-_]/", "", pathinfo($file_name, PATHINFO_FILENAME));
            $new_track_file = "uploads/" . uniqid() . "_" . $safe_file_name . "." . $ext;

            if (move_uploaded_file($file_tmp, $new_track_file)) {
                $track_file = $new_track_file;
            } else {
                throw new Exception("❌ Failed to upload track.");
            }
        }

        // Update database
        $stmt = $pdo->prepare("UPDATE music_releases SET 
            title = ?, subtitle = ?, genre = ?, label = ?, 
            physical_release_date = ?, store_release_date = ?, register_line = ?, production_year = ?, 
            catalogue_number = ?, track_title = ?, primary_artist = ?, writers = ?, 
            composers = ?, music_directors = ?, producer = ?, publisher = ?, 
            language = ?, isrc = ?, lyrics = ?, youtube_url = ?, album_cover = ?, track_file = ?
            WHERE id = ?");
        
        $stmt->execute([
            $title, $subtitle, $genre, $label,
            $physical_release_date, $store_release_date, $register_line, $production_year,
            $catalogue_number, $track_title, $primary_artist, $writers,
            $composers, $music_directors, $producer, $publisher,
            $language, $isrc, $lyrics, $youtube_url, $album_cover, $track_file,
            $id
        ]);

        echo "✅ Record updated successfully!";
        echo "<script>window.location.href = 'index.php';</script>";
        exit;
       
    }
} catch (Exception $e) {
    echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
}
?>



<div id="layoutSidenav">
    <div id="layoutSidenav_content">
        <main>
            <button type="submit" name="redirect">Test Redirect</button>

            <div class="container " style="padding-top: 120px;">
                <div class="d-flex justify-content-between mb-3">
                    <button class="btn btn-outline-primary step-btn" data-step="1">Release</button>
                    <button class="btn btn-outline-primary step-btn" data-step="2">Track</button>
                    <button class="btn btn-outline-primary step-btn" data-step="3">Store</button>
                </div>
                <form id="multiStepForm" action="" method="POST" enctype="multipart/form-data"
                    class="card p-4 shadow-sm">
                    <div class="step step-1">
                        <div class="mt-4 row">
                            <div class="col-md-6">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" class="form-control"
                                    value="<?= htmlspecialchars($release['title']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Subtitle</label>
                                <input type="text" name="subtitle" class="form-control"
                                    value="<?= htmlspecialchars($release['subtitle']) ?>" required>
                            </div>
                        </div>
                        <div class="mt-4 row">
                            <div class="col-md-6">
                                <label class="form-label">Genre & Subgenre</label>
                                <input type="tel" id="phone" name="genre" class="form-control"
                                    value="<?= htmlspecialchars($release['genre']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Label</label>
                                <input type="text" class="form-control" name="label"
                                    value="<?= htmlspecialchars($release['label']) ?>" required>
                            </div>
                        </div>
                        <div class="mt-4 row">
                            <div class="col-md-6">
                                <label for="release_date" class="form-label">Physical Release Date</label>
                                <input type="date" class="form-control" name="physical_release_date"
                                    value="<?= htmlspecialchars($release['physical_release_date']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="release_date" class="form-label">Store Release Date</label>
                                <input type="date" class="form-control" name="store_release_date"
                                    value="<?= htmlspecialchars($release['store_release_date']) ?>" required>
                            </div>
                        </div>
                        <div class="mt-4 row">
                            <div class="col-md-6">
                                <label for="release_date" class="form-label">©&℗ Line</label>
                                <input type="text" class="form-control" name="register_line"
                                    value="<?= htmlspecialchars($release['register_line']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="release_date" class="form-label">Production Year</label>
                                <input type="year" class="form-control" name="production_year"
                                    value="<?= htmlspecialchars($release['production_year']) ?>" required>
                            </div>
                        </div>
                        <div class="mt-4 row">
                            <div class="col-md-6">
                                <label for="release_date" class="form-label">Producer Catalogue Number</label>
                                <input type="text" class="form-control" name="catalogue_number"
                                    value="<?= htmlspecialchars($release['catalogue_number']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="album_cover" class="form-label">Cover</label>
                                <input type="file" class="form-control" name="album_cover" accept="image/*"
                                    onchange="previewImage(event)">

                                <!-- Display existing album cover if available -->
                                <?php if (!empty($release['album_cover'])): ?>
                                <img id="imagePreview" src="<?= htmlspecialchars($release['album_cover']) ?>"
                                    alt="Album Cover Preview" style="width: 100px; height: 100px; margin-top: 10px;">
                                <?php else: ?>
                                <img id="imagePreview" src="#" alt="Album Cover Preview"
                                    style="display:none; width: 100px; height: 100px; margin-top: 10px;">
                                <?php endif; ?>

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

                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-primary next">Next</button>
                        </div>
                    </div>
                    <div class="step step-2 d-none">
                        <div class="container mt-4">
                            <div class="upload-area" id="uploadArea">
                                <input type="file" id="fileInput" name="track_file" accept=".wav" hidden>
                                <p class="mb-2">Click or drag file to this area to upload</p>
                                <small class="text-muted">Only WAV files are allowed.</small>
                            </div>

                            <!-- Show existing or new file -->
                            <div id="filePreview" class="mt-3">
                                <?php if (!empty($release['track_file'])): ?>
                                <p>📁 Current File: <strong><?= htmlspecialchars($release['track_file']) ?></strong></p>
                                <audio id="audio" controls>
                                    <source id="audioSource" src="<?= $release['track_file'] ?>" type="audio/wav">
                                    Your browser does not support the audio element.
                                </audio>
                                <?php endif; ?>
                            </div>

                            <div id="musicForm" class="mt-4">
                                <h4>Track Details</h4>
                                <div id="customAudioPlayer">
                                    <span id="audioTime">00:00</span>
                                    <input type="range" id="seekBar" value="0">
                                    <span id="audioDuration">00:00</span>
                                    <button id="playPauseBtn">▶</button>
                                    <input type="range" id="volumeControl" min="0" max="1" step="0.1" value="1">
                                </div>

                                <audio id="audio" controls>
                                    <source id="audioSource" src="" type="audio/wav">
                                    Your browser does not support the audio element.
                                </audio>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label" for="title">Track Title</label>
                                    <input type="text" class="form-control" id="track_title" name="track_title"
                                        value="<?= htmlspecialchars($release['track_title']) ?>"
                                        placeholder="Enter song title" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="primary_artist">Primary Artist</label>
                                    <input type="text" class="form-control" id="primary_artist" name="primary_artist"
                                        placeholder="Enter Artist name"
                                        value="<?= htmlspecialchars($release['primary_artist']) ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label" for="writers">Writers</label>
                                    <input type="text" class="form-control" id="writers" name="writers"
                                        value="<?= htmlspecialchars($release['writers']) ?>" placeholder="Enter writers"
                                        required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="composers">Composers</label>
                                    <input type="text" class="form-control" id="composers" name="composers"
                                        value="<?= htmlspecialchars($release['composers']) ?>"
                                        placeholder="Enter composers" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label" for="musicDirectors">Music Directors</label>
                                    <input type="text" class="form-control" id="musicDirectors" name="music_directors"
                                        value="<?= htmlspecialchars($release['music_directors']) ?>"
                                        placeholder="Enter music directors">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="Producer">producer</label>
                                    <input type="text" class="form-control" id="producer" name="producer"
                                        value="<?= htmlspecialchars($release['producer']) ?>"
                                        placeholder="Enter producer" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label" for="publisher">Publisher</label>
                                    <input type="text" class="form-control" id="publisher" name="publisher"
                                        value="<?= htmlspecialchars($release['publisher']) ?>"
                                        placeholder="Provide a publisher for this track">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="language">Language</label>
                                    <select class="form-control" id="language" name="language" required>
                                        <option value="English"
                                            <?= ($release['language'] == 'English') ? 'selected' : '' ?>>English
                                        </option>
                                        <option value="Urdu" <?= ($release['language'] == 'Urdu') ? 'selected' : '' ?>>
                                            Urdu</option>
                                        <option value="Hindi"
                                            <?= ($release['language'] == 'Hindi') ? 'selected' : '' ?>>Hindi</option>
                                        <option value="Other"
                                            <?= ($release['language'] == 'Other') ? 'selected' : '' ?>>Other</option>
                                    </select>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label" for="isrc">ISRC</label>
                                    <input type="text" class="form-control" id="isrc" name="isrc"
                                        value="<?= htmlspecialchars($release['isrc']) ?>"
                                        placeholder="Provide a publisher for this track">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="lyrics">Lyrics</label>
                                    <input type="text" class="form-control" id="lyrics" name="lyrics"
                                        value="<?= htmlspecialchars($release['lyrics']) ?>"
                                        placeholder="Provide a publisher for this track">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="youtube_url">Youtube URL</label>
                                <input type="text" class="form-control" id="youtube_url" name="youtube_url"
                                    value="<?= htmlspecialchars($release['youtube_url']) ?>"
                                    placeholder="Provide a publisher for this track">
                            </div>
                            <div class="d-flex justify-content-end mt-4">
                                <!-- <button type="button" class="btn btn-secondary prev me-3">Previous</button> -->
                                <button type="button" class="btn btn-primary next">Next</button>
                            </div>
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
                    input.classList.add("is-invalid"); // Bootstrap red border
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

document.getElementById("uploadArea").addEventListener("click", function() {
    document.getElementById("fileInput").click();
});

document.addEventListener("DOMContentLoaded", function() {
    const fileInput = document.getElementById("fileInput");
    const uploadArea = document.getElementById("uploadArea");
    const filePreview = document.getElementById("filePreview");
    const audio = document.getElementById("audio");
    const audioSource = document.getElementById("audioSource");
    const playPauseBtn = document.getElementById("playPauseBtn");
    const seekBar = document.getElementById("seekBar");
    const audioTime = document.getElementById("audioTime");
    const audioDuration = document.getElementById("audioDuration");
    const volumeControl = document.getElementById("volumeControl");

    // Handle file selection
    fileInput.addEventListener("change", function(event) {
        const file = event.target.files[0];

        if (file && file.type === "audio/wav") {
            const objectURL = URL.createObjectURL(file);

            // Show file name
            filePreview.innerHTML = `<p>📁 Selected File: <strong>${file.name}</strong></p>`;

            // Update audio source
            audioSource.src = objectURL;
            audio.load();
        } else {
            alert("❌ Please select a valid WAV file.");
            fileInput.value = "";
        }
    });

    // Drag & Drop support
    uploadArea.addEventListener("dragover", function(event) {
        event.preventDefault();
        uploadArea.style.border = "2px dashed #007bff";
    });

    uploadArea.addEventListener("dragleave", function() {
        uploadArea.style.border = "2px dashed #ccc";
    });

    uploadArea.addEventListener("drop", function(event) {
        event.preventDefault();
        uploadArea.style.border = "2px dashed #ccc";

        const file = event.dataTransfer.files[0];
        if (file && file.type === "audio/wav") {
            fileInput.files = event.dataTransfer.files; // Set the file input
            fileInput.dispatchEvent(new Event("change")); // Trigger change event
        } else {
            alert("❌ Only WAV files are allowed.");
        }
    });

    // Play/Pause functionality
    playPauseBtn.addEventListener("click", function() {
        if (audio.paused) {
            audio.play();
            playPauseBtn.textContent = "⏸";
        } else {
            audio.pause();
            playPauseBtn.textContent = "▶";
        }
    });

    // Seek bar update
    audio.addEventListener("timeupdate", function() {
        seekBar.value = (audio.currentTime / audio.duration) * 100 || 0;
        audioTime.textContent = formatTime(audio.currentTime);
    });

    seekBar.addEventListener("input", function() {
        audio.currentTime = (seekBar.value / 100) * audio.duration;
    });

    // Update duration
    audio.addEventListener("loadedmetadata", function() {
        audioDuration.textContent = formatTime(audio.duration);
    });

    // Volume control
    volumeControl.addEventListener("input", function() {
        audio.volume = volumeControl.value;
    });

    function formatTime(time) {
        const minutes = Math.floor(time / 60);
        const seconds = Math.floor(time % 60);
        return `${minutes}:${seconds < 10 ? "0" : ""}${seconds}`;
    }
});
</script>