<?php include("../includes/header.php"); ?>

<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

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
    width: 15%;
    padding: 10px;
    background: #007bff;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 18px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.btn-secondary {
    width: 10%;
    padding: 10px;
    background: #6c757d;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 18px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.btn-success {
    width: 10%;
    padding: 10px;
    background: #28a745;
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

.iti {
    width: 100%;
}

.signature {
            margin-top: 30px;
        }
        canvas {
            border: 1px solid #000;
            width: 20%;
            height: 150px;
        }
</style>

<?php
include("../.././config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $register_as = trim($_POST['register_as']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone']);
    $country = trim($_POST['country']);
    $state = trim($_POST['state']);
    $city = trim($_POST['city']);
    $post_code = trim($_POST['post_code']);
    $address = trim($_POST['address']);
    $track_name = trim($_POST['track_name']);
    $album_art = trim($_POST['album_art']);
    $genre = trim($_POST['genre']);
    $release_date = trim($_POST['release_date']);
    $claim_type = trim($_POST['claim_type'] ?? null);
    $channel_name = trim($_POST['channel_name']);
    $channel_link = trim($_POST['channel_link']);
    $mcn_status = trim($_POST['mcn_status'] ?? 'not_linked');
    $subscribers_count = trim($_POST['subscribers_count']);
    $videos_count = trim($_POST['videos_count']);
    $signature = $_POST['signature'] ?? null;
    
    if (!isset($_SESSION['id'])) {
        die("<div class='alert alert-danger'>Error: You must be logged in to add a track.</div>");
    }
    $user_id = $_SESSION['id'];

    try {
        if (!isset($pdo)) {
            throw new Exception("Database connection failed!");
        }

        // Verify user exists
        $checkUser = $pdo->prepare("SELECT id FROM users WHERE id = ?");
        $checkUser->execute([$user_id]);
        if ($checkUser->rowCount() == 0) {
            throw new Exception("Error: User ID does not exist.");
        }
        
  
        
        // Directories for file uploads
        $upload_dirs = [
            "profile_picture" => "uploads/profile_pictures/",
            "id_front" => "uploads/id_Front/",
            "id_back" => "uploads/id_back/",
            "track_file" => "uploads/tracks/",
            "album_cover" => "uploads/covers/",
            "channel_dashboard" => "uploads/channel_dashboard/",
            "channel_content" => "uploads/channel_content/",
            "signature" => "uploads/signatures/",
        ];

        // Ensure directories exist
        foreach ($upload_dirs as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
        }

        // Handle file uploads
        function uploadFile($field_name, $upload_dir, $allowed_types = null) {
            if (!empty($_FILES[$field_name]['name'])) {
                $file_name = time() . "_" . basename($_FILES[$field_name]['name']);
                $target_file = $upload_dir . $file_name;

                // Validate file type if restrictions are set
                if ($allowed_types && !in_array($_FILES[$field_name]['type'], $allowed_types)) {
                    throw new Exception("Error: Invalid file format for $field_name.");
                }

                if (move_uploaded_file($_FILES[$field_name]['tmp_name'], $target_file)) {
                    return $target_file;
                } else {
                    throw new Exception("Error: $field_name upload failed.");
                }
            }
            return null;
        }

        $profile_picture = uploadFile('profile_picture', $upload_dirs['profile_picture']);
        $id_front = uploadFile('id_front', $upload_dirs['id_front']);
        $id_back = uploadFile('id_back', $upload_dirs['id_back']);

        // Track file upload (Required)
        $tracklink = uploadFile('track_file', $upload_dirs['track_file']);
        if (!$tracklink) {
            throw new Exception("Error: No track file uploaded.");
        }

        // Album cover upload (Required, with format validation)
        $allowed_image_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $album_cover = uploadFile('album_cover', $upload_dirs['album_cover'], $allowed_image_types);
        if (!$album_cover) {
            throw new Exception("Error: No album cover uploaded or invalid format.");
        }

        $channel_dashboard = uploadFile('channel_dashboard', $upload_dirs['channel_dashboard']);
        $channel_content = uploadFile('channel_content', $upload_dirs['channel_content']);

        $signature_path = null;
        if ($signature) {
            list(, $encodedData) = explode(",", $signature);
            $decodedData = base64_decode($encodedData);
            $signature_path = "uploads/signatures/" . uniqid() . ".png";
            file_put_contents($signature_path, $decodedData);
        }
        // Insert data into the database
        $sql = "INSERT INTO tracks 
            (register_as, name, email, phone_number, profile_picture, id_front, id_back, country, state, city, post_code, address, 
            track_name, album_art, album_cover, tracklink, genre, release_date, user_id, created_at, updated_at, status, deleted_at, 
            channel_link, claim_type, channel_name, mcn_status, subscribers_count, videos_count, channel_dashboard, channel_content, signature) 
            VALUES 
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), 1, NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $register_as, $name, $email, $phone_number, $profile_picture, $id_front, $id_back, $country, $state, $city, $post_code, $address,
            $track_name, $album_art, $album_cover, $tracklink, $genre, $release_date, $user_id,
            $channel_link, $claim_type, $channel_name, $mcn_status, $subscribers_count, $videos_count, $channel_dashboard, $channel_content, $signature_path
        ]);


        echo "<div class='alert alert-success alert-dismissible fade show end-0 w-25 mt-5 position-absolute' role='alert'>
        New track added successfully
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
      </div>";

    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>" . $e->getMessage() . "</div>";
    }
}
?>

<div id="layoutSidenav">
<?php 
  $role = $_SESSION["role"] ?? null; 
?>
<style>
  <?php if ($role == 2): ?>
    .sb-nav-fixed #layoutSidenav #layoutSidenav_nav {
      width: 0px !important;
      /* overflow: hidden; */
    }
    #layoutSidenav_content {
      padding-left: 0px !important;
    }
  <?php endif; ?>
</style>

 
        <main>
            <div class="container " style="padding-top: 120px;">
                <div class="d-flex justify-content-between mb-3">
                    <button class="btn btn-outline-primary step-btn" data-step="1">Profile Verification</button>
                    <button class="btn btn-outline-primary step-btn" data-step="2">Label Verification</button>
                    <button class="btn btn-outline-primary step-btn" data-step="3">Agreement Signing</button>
                </div>
                <form id="multiStepForm" action="" method="POST" enctype="multipart/form-data"
                    class="card p-4 shadow-sm">
                    <div class="step step-1">
                        <h2 class="mb-3 text-center font-weight-bold">Profile Verification</h2>
                        <h5 class="mb-3">Account Information</h5>
                        <div class="mb-3">
                            <label class="form-label">Register As</label>
                            <div class="btn-group d-flex" role="group">
                                <input type="radio" class="btn-check" name="register_as" id="label"
                                    value="Individual Label" required>
                                <label class="btn btn-outline-primary flex-fill" for="label">Individual Label</label>

                                <input type="radio" class="btn-check" name="register_as" id="distributor"
                                    value="Distributor">
                                <label class="btn btn-outline-primary flex-fill" for="distributor">Distributor</label>

                                <input type="radio" class="btn-check" name="register_as" id="artist"
                                    value="Individual Artist">
                                <label class="btn btn-outline-primary flex-fill" for="artist">Individual Artist</label>
                            </div>
                        </div>
                        <div class="mt-4 row">
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                        </div>
                        <div class="mt-4 row">
                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" id="phone" name="phone" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Profile Picture</label>
                                <input type="file" class="form-control" name="profile_picture" accept="image/*" required>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="form-label">Govt. ID <i class="bi bi-info-circle"></i></label>
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="file" class="form-control" name="id_front" accept="image/*" required>
                                    <small class="text-muted">Front</small>
                                </div>
                                <div class="col-md-6">
                                    <input type="file" class="form-control" name="id_back" accept="image/*" required>
                                    <small class="text-muted">Back</small>
                                </div>
                            </div>
                        </div>
                        <h5 class="my-3">Address Information</h5>
                        <div class="mt-4 row">
                            <div class="col-md-6">
                                <label class="form-label">Country</label>
                                <input type="text" class="form-control" id="country" name="country"
                                    placeholder="Enter Country" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">State</label>
                                <input type="text" class="form-control" id="state" name="state"
                                    placeholder="Enter State" required>
                            </div>
                        </div>
                        <div class="mt-4 row">
                            <div class="col-md-6">
                                <label class="form-label">City</label>
                                <input type="text" class="form-control" id="city" name="city" placeholder="Enter City"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Post Code</label>
                                <input type="text" class="form-control" id="post_code" name="post_code"
                                    placeholder="Enter Post Code" required>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3"
                                placeholder="Enter Address" required></textarea>
                        </div>
                        <h5 class="my-3">Track Information</h5>
                        <div class="mt-4 row">
                            <div class="col-md-6">
                                <label for="track_name" class="form-label">Track Name</label>
                                <input type="text" class="form-control" id="track_name" name="track_name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="album_art" class="form-label">Album Art URL</label>
                                <input type="text" class="form-control" id="album_art" name="album_art" required>
                            </div>
                            <div class="col-md-6">
                                <label for="track_file" class="form-label">Upload MP3 Track</label>
                                <input type="file" class="form-control" id="track_file" name="track_file"
                                    accept="audio/mp3" required>
                            </div>
                            <div class="col-md-6">
                                <label for="track_file" class="form-label">Upload Album Cover</label>
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
                            <div class="col-md-6">
                                <label for="genre" class="form-label">Genre</label>
                                <input type="text" class="form-control" id="genre" name="genre" required>
                            </div>
                            <div class="col-md-6">
                                <label for="release_date" class="form-label">Release Date</label>
                                <input type="date" class="form-control" id="release_date" name="release_date" required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-primary next">Next</button>
                        </div>
                    </div>
                    <div class="step step-2 d-none">
                        <h2 class="mb-3 text-center font-weight-bold">Label Verification</h2>
                        <div class="mt-4 row">
                            <div class="col-md-6">
                                <label class="form-label">Channel Name</label>
                                <input type="text" name="channel_name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Channel Link</label>
                                <input type="url" name="channel_link" class="form-control" required>
                            </div>
                        </div>
                        <div class="mt-4 row">
                            <div class="col-md-6">
                                <label for="claim_type" class="form-label">Claim Type (If Needed)</label>
                                <select class="form-control" id="claim_type" name="claim_type" required>
                                    <option value="manual">Manual Claim</option>
                                    <option value="release">Claim Release</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="mcn_status" class="form-label">Link to MCN</label>
                                <select class="form-control" id="mcn_status" name="mcn_status" required>
                                    <option value="not_linked">Not Linked</option>
                                    <option value="linked">Linked</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4 row">
                            <div class="col-md-6">
                                <label class="form-label">Subscribers Count</label>
                                <input type="text" name="subscribers_count" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Videos Count</label>
                                <input type="text" name="videos_count" class="form-control" required>
                            </div>
                        </div>
                        <div class="mt-4">
                            <h5 class="mb-3">Youtube Channel Details</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Channel Dashboard Screenshots</label>
                                    <input type="file" class="form-control" name="channel_dashboard" accept="image/*" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Channel Content Screenshots</label>
                                    <input type="file" class="form-control" name="channel_content" accept="image/*" required>

                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-secondary prev me-3">Previous</button>
                            <button type="button" class="btn btn-primary next">Next</button>
                        </div>
                    </div>
                    <div class="step step-3 d-none">
                        <h2 class="mb-3 text-center font-weight-bold">Agreement Signing</h2>
                        <div class="container">
                            <h4>Content licensing and distribution agreement</h4>
                            <p>This Content Licensing and Distribution Agreement ("Agreement") is made and entered into
                                by and between <strong>[Licensor Name]</strong> and <strong>[Licensee Name]</strong>.
                            </p>

                            <h5>Grant of Rights</h5>
                            <p>The Licensor hereby grants the Licensee a non-exclusive, non-transferable license to
                                distribute the content as detailed below:</p>
                            <ul>
                                <li>Content Title: [Content Title]</li>
                                <li>Description: [Content Description]</li>
                                <li>Usage Rights: [Specific Usage Rights]</li>
                            </ul>

                            <h5>Responsibilities of Parties</h5>
                            <ul>
                                <li>Provide high-quality and legally owned content.</li>
                                <li>Ensure content does not infringe on any third-party rights.</li>
                            </ul>

                            <h5>Licensee Responsibilities</h5>
                            <ul>
                                <li>Distribute content only through authorized platforms.</li>
                                <li>Ensure proper attribution and copyright compliance.</li>
                            </ul>

                            <h5>Compensation and Payment Terms</h5>
                            <p>The Licensee agrees to compensate the Licensor as per the agreed terms:</p>
                            <ul>
                                <li>Payment Amount: [Amount]</li>
                                <li>Payment Schedule: [Schedule]</li>
                                <li>Method of Payment: [Payment Method]</li>
                            </ul>

                            <h5>Termination</h5>
                            <p>Either party may terminate this agreement with prior written notice of [Notice Period]
                                days. Upon termination:</p>
                            <ul>
                                <li>All licensed content must be removed from distribution channels.</li>
                                <li>Any outstanding payments must be settled.</li>
                            </ul>

                            <h5>Intellectual Property Rights</h5>
                            <p>All rights, titles, and interests in and to the content remain with the Licensor.</p>

                            <h5>Governing Law</h5>
                            <p>This agreement shall be governed by the laws of <strong>[Jurisdiction]</strong>.</p>

                            <h5>Signatures</h5>
                            <div class="signature">
            <p><strong>Licensor Signature:</strong></p>
            <canvas id="signature-pad"></canvas>
            <button class="btn" onclick="clearSignature()">Clear</button>
            <button class="btn" onclick="saveSignature()">Save</button>
            <input type="hidden" name="signature" id="signature_input">

        </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary prev me-3">Previous</button>
                            <button type="submit" class="btn btn-success">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
<script>
        let canvas = document.getElementById("signature-pad");
        let ctx = canvas.getContext("2d");
        let isDrawing = false;

        canvas.addEventListener("mousedown", (e) => {
            isDrawing = true;
            ctx.moveTo(e.offsetX, e.offsetY);
        });

        canvas.addEventListener("mousemove", (e) => {
            if (isDrawing) {
                ctx.lineTo(e.offsetX, e.offsetY);
                ctx.stroke();
            }
        });

        canvas.addEventListener("mouseup", () => {
            isDrawing = false;
            ctx.beginPath();
        });

        function clearSignature() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }

        function saveSignature() {
            let dataURL = canvas.toDataURL("image/png");
            console.log("Signature Saved: ", dataURL);
        }

        function saveSignature() {
        let canvas = document.getElementById("signature-pad");
        let dataURL = canvas.toDataURL("image/png");
        document.getElementById("signature_input").value = dataURL;
    }
    </script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const nextButtons = document.querySelectorAll(".next");

    nextButtons.forEach(button => {
        button.addEventListener("click", function () {
            const currentStep = this.closest(".step");
            const inputs = currentStep.querySelectorAll("input[required], textarea[required], select[required]");
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
        input.addEventListener("input", function () {
            if (this.value.trim()) {
                this.classList.remove("is-invalid");
            }
        });
    });
});

</script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
<script>
var input = document.querySelector("#phone");
window.intlTelInput(input, {
    initialCountry: "auto",
    geoIpLookup: function(callback) {
        fetch("https://ipinfo.io?token=YOUR_TOKEN")
            .then(response => response.json())
            .then(data => callback(data.country))
            .catch(() => callback("us"));
    },
    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
});
</script>