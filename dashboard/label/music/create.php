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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $title = $_POST['title'];
        $subtitle = $_POST['subtitle'];
        $genre = $_POST['genre'];
        $label = $_POST['label'];
        $physical_release_date = $_POST['physical_release_date'];
        $store_release_date = $_POST['store_release_date'];
        $register_line = $_POST['register_line'];
        $production_year = $_POST['production_year'];
        $catalogue_number = $_POST['catalogue_number'];
        $track_title = $_POST['track_title'];
        $primary_artist = $_POST['primary_artist'];
        $writers = $_POST['writers'];
        $composers = $_POST['composers'];
        $music_directors = $_POST['music_directors'];
        $producer = $_POST['producer'];
        $publisher = $_POST['publisher'];
        $language = $_POST['language'];
        $isrc = $_POST['isrc'];
        $lyrics = $_POST['lyrics'];
        $youtube_url = $_POST['youtube_url'];

        // Ensure uploads directory exists
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }

        // File Upload for Album Cover
        $album_cover = null;
        if (isset($_FILES['album_cover']) && $_FILES['album_cover']['error'] == 0) {
            $ext = pathinfo($_FILES["album_cover"]["name"], PATHINFO_EXTENSION);
            if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png'])) {
                $album_cover = "uploads/" . uniqid() . "." . $ext;
                move_uploaded_file($_FILES["album_cover"]["tmp_name"], $album_cover);
            }
        }

        // File Upload for Track File
        $track_file = null;
        if (isset($_FILES['track_file']) && $_FILES['track_file']['error'] == 0) {
            $ext = pathinfo($_FILES["track_file"]["name"], PATHINFO_EXTENSION);
            if (strtolower($ext) === 'wav') {
                $track_file = "uploads/" . uniqid() . "." . $ext;
                move_uploaded_file($_FILES["track_file"]["tmp_name"], $track_file);
            }
        }

        // Corrected SQL query
        $sql = "INSERT INTO music_releases 
        (title, subtitle, genre, label, physical_release_date, store_release_date, register_line, production_year, catalogue_number, album_cover, track_file, track_title, primary_artist, writers, composers, music_directors, producer, publisher, language, isrc, lyrics, youtube_url, created_at) 
        VALUES (:title, :subtitle, :genre, :label, :physical_release_date, :store_release_date, :register_line, :production_year, :catalogue_number, :album_cover, :track_file, :track_title, :primary_artist, :writers, :composers, :music_directors, :producer, :publisher, :language, :isrc, :lyrics, :youtube_url, NOW())";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':subtitle', $subtitle);
        $stmt->bindParam(':genre', $genre);
        $stmt->bindParam(':label', $label);
        $stmt->bindParam(':physical_release_date', $physical_release_date);
        $stmt->bindParam(':store_release_date', $store_release_date);
        $stmt->bindParam(':register_line', $register_line);
        $stmt->bindParam(':production_year', $production_year);
        $stmt->bindParam(':catalogue_number', $catalogue_number);
        $stmt->bindParam(':album_cover', $album_cover);
        $stmt->bindParam(':track_file', $track_file);
        $stmt->bindParam(':track_title', $track_title);
        $stmt->bindParam(':primary_artist', $primary_artist);
        $stmt->bindParam(':writers', $writers);
        $stmt->bindParam(':composers', $composers);
        $stmt->bindParam(':music_directors', $music_directors);
        $stmt->bindParam(':producer', $producer);
        $stmt->bindParam(':publisher', $publisher);
        $stmt->bindParam(':language', $language);
        $stmt->bindParam(':isrc', $isrc);
        $stmt->bindParam(':lyrics', $lyrics);
        $stmt->bindParam(':youtube_url', $youtube_url);

        $stmt->execute();

        echo "Record added successfully!";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>


<div id="layoutSidenav">
    <div id="layoutSidenav_content">
        <main>
            <div class="container " style="padding-top: 40px;">
                <div class="d-flex justify-content-between mb-3">
                    <button class="btn btn-outline-primary step-btn" data-step="1">Release</button>
                    <button class="btn btn-outline-primary step-btn" data-step="2">Track</button>
                    <button class="btn btn-outline-primary step-btn" data-step="3">Store</button>
                    <button class="btn btn-outline-primary step-btn" data-step="4">Submission</button>
                </div>
                <form id="multiStepForm" action="" method="POST" enctype="multipart/form-data"
                    class="card p-4 shadow-sm">
                    <div class="step step-1">
                        <div class="mt-4 row">
                            <div class="col-md-6">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Subtitle</label>
                                <input type="email" name="subtitle" class="form-control" required>
                            </div>
                        </div>
                        <div class="mt-4 row">
                            <div class="col-md-6">
                                <label class="form-label">Genre & Subgenre</label>
                                <input type="tel" id="phone" name="genre" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Label</label>
                                <input type="text" class="form-control" name="label" required>
                            </div>
                        </div>
                        <div class="mt-4 row">
                            <div class="col-md-6">
                                <label for="release_date" class="form-label">Physical Release Date</label>
                                <input type="date" class="form-control" name="physical_release_date" required>
                            </div>
                            <div class="col-md-6">
                                <label for="release_date" class="form-label">Store Release Date</label>
                                <input type="date" class="form-control" name="store_release_date" required>
                            </div>
                        </div>
                        <div class="mt-4 row">
                            <div class="col-md-6">
                                <label for="release_date" class="form-label">©&℗ Line</label>
                                <input type="text" class="form-control" name="register_line" required>
                            </div>
                            <div class="col-md-6">
                                <label for="release_date" class="form-label">Production Year</label>
                                <input type="date" class="form-control" name="production_year" required>
                            </div>
                        </div>
                        <div class="mt-4 row">
                            <div class="col-md-6">
                                <label for="release_date" class="form-label">Producer Catalogue Number</label>
                                <input type="text" class="form-control" name="catalogue number" required>
                            </div>
                            <div class="col-md-6">
                                <label for="release_date" class="form-label">Cover</label>
                                <input type="file" class="form-control" name="album_cover" accept="image/*"
                                    onchange="previewImage(event)" required>
                                <img id="imagePreview" src="#" alt="Album Cover Preview"
                                    style="display:none; width: 100px; height: 100px; margin-top: 10px;"><br>
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
                                <input type="file" id="fileInput" accept=".wav" hidden name="track_file">
                                <p class="mb-2">Click or drag file to this area to upload</p>
                                <small class="text-muted">Only WAV files are allowed.</small>
                            </div>
                            <div id="filePreview" class="mt-3"></div>
                            <div id="musicForm" class="mt-4">
                                <h4>Track Details</h4>
                                <div id="customAudioPlayer">
                                    <span id="audioTime">00:00</span>
                                    <input type="range" id="seekBar" value="0">
                                    <span id="audioDuration">00:00</span>
                                    <button id="playPauseBtn">▶</button>
                                    <input type="range" id="volumeControl" min="0" max="1" step="0.1" value="1">
                                </div>

                                <audio id="audio">
                                    <source src="your-audio-file.mp3" type="audio/mp3">
                                </audio>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label" for="title">Track Title</label>
                                        <input type="text" class="form-control" id="track_title" name="track_title" 
                                            placeholder="Enter song title" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="primary_artist">Primary Artist</label>
                                        <input type="text" class="form-control" id="primary_artist" name="primary_artist"
                                            placeholder="Enter Artist name" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label" for="writers">Writers</label>
                                        <input type="text" class="form-control" id="writers" name="writers" placeholder="Enter writers"
                                            required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="composers">Composers</label>
                                        <input type="text" class="form-control" id="composers" name="composers"
                                            placeholder="Enter composers" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label" for="musicDirectors">Music Directors</label>
                                        <input type="text" class="form-control" id="musicDirectors" name="music_directors"
                                            placeholder="Enter music directors">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="Producer">producer</label>
                                        <input type="text" class="form-control" id="producer" name="producer"
                                            placeholder="Enter producer" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label" for="publisher">Publisher</label>
                                        <input type="text" class="form-control" id="publisher" name="publisher"
                                            placeholder="Provide a publisher for this track">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="language">Language</label>
                                        <select class="form-control" id="language" name="language">
                                            <option>English</option>
                                            <option>Urdu</option>
                                            <option>Hindi</option>
                                            <option>Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label" for="isrc">ISRC</label>
                                        <input type="text" class="form-control" id="isrc" name="isrc"
                                            placeholder="Provide a publisher for this track">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="lyrics">Lyrics</label>
                                        <input type="text" class="form-control" id="lyrics" name="lyrics"
                                            placeholder="Provide a publisher for this track">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="youtube_url">Youtube URL</label>
                                    <input type="text" class="form-control" id="youtube_url" name="youtube_url"
                                        placeholder="Provide a publisher for this track">
                                </div>
                                <div class="d-flex justify-content-end mt-4">
                                    <button type="button" class="btn btn-secondary prev me-3">Previous</button>
                                    <button type="button" class="btn btn-primary next">Next</button>
                                </div>
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
                            <button type="button" class="btn btn-secondary prev me-3">Previous</button>
                            <button type="button" class="btn btn-primary next">Next</button>
                        </div>
                    </div>
                    <div class="step step-4 d-none">
                        <h2>Review Your Submission</h2>

                        <!-- Step 1 Data Display -->
                        <div class="review-section">
                            <h3>Step 1: Release Info</h3>
                            <p><strong>Title:</strong> <span id="releaseTitle"></span></p>
                            <p><strong>Artist:</strong> <span id="releaseArtist"></span></p>
                        </div>

                        <!-- Step 2 Data Display -->
                        <div class="review-section">
                            <h3>Step 2: Tracks Info</h3>
                            <p><strong>Track Name:</strong> <span id="trackName"></span></p>
                            <p><strong>Duration:</strong> <span id="trackDuration"></span></p>
                        </div>

                        <!-- Step 3 Data Display -->
                        <div class="review-section">
                            <h3>Step 3: Stores</h3>
                            <div id="selectedStores"></div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary">Submit</button>
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



const audio = document.getElementById("audio");
const playPauseBtn = document.getElementById("playPauseBtn");
const seekBar = document.getElementById("seekBar");
const volumeControl = document.getElementById("volumeControl");
const audioTime = document.getElementById("audioTime");
const audioDuration = document.getElementById("audioDuration");

// Play/Pause button
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