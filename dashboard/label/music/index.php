<?php include("../../includes/header.php"); ?>
<link href="//cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css" rel="stylesheet" />
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="//cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
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

#datatablesSimple th,
#datatablesSimple td {
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

    #datatablesSimple th,
    #datatablesSimple td {
        padding: 10px;
        font-size: 14px;
    }
}

.card-body {
    padding: 20px;
}
</style>
<?php
include("../../.././config.php");

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT * FROM music_releases ORDER BY created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $music_releases = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<div id="layoutSidenav">
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4 py-4">

                <div class="card mb-4">
                    <div class="card-body">
                        <table id="datatablesSimple">
                            <thead>
                                <tr>
                                    <th>Cover</th>
                                    <th>Title</th>
                                    <th>Artists</th>
                                    <th>Label</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($music_releases as $release): ?>
                                <tr>
                                    <td><img src="<?= htmlspecialchars($release['album_cover']) ?>" alt="Album Cover"
                                            width="100"></td>
                                    <td><?= htmlspecialchars($release['title']) ?></td>
                                    <td><?= htmlspecialchars($release['primary_artist']) ?></td>
                                    <td><?= htmlspecialchars($release['label']) ?></td>
                                    <td><?= htmlspecialchars($release['status']) ?></td>

                                    <td>
                                        <button class="btn btn-primary btn-sm view-more" data-bs-toggle="offcanvas"
                                            data-bs-target="#detailsModal"
                                            data-id="<?= htmlspecialchars($release['id']) ?>"
                                            data-title="<?= htmlspecialchars($release['title']) ?>"
                                            data-genre="<?= htmlspecialchars($release['genre']) ?>"
                                            data-label="<?= htmlspecialchars($release['label']) ?>"
                                            data-release="<?= htmlspecialchars($release['store_release_date']) ?>"
                                            data-track="<?= htmlspecialchars($release['track_title']) ?>"
                                            data-artist="<?= htmlspecialchars($release['primary_artist']) ?>"
                                            data-writers="<?= htmlspecialchars($release['writers']) ?>"
                                            data-composers="<?= htmlspecialchars($release['composers']) ?>"
                                            data-directors="<?= htmlspecialchars($release['music_directors']) ?>"
                                            data-producer="<?= htmlspecialchars($release['producer']) ?>"
                                            data-publisher="<?= htmlspecialchars($release['publisher']) ?>"
                                            data-language="<?= htmlspecialchars($release['language']) ?>"
                                            data-isrc="<?= htmlspecialchars($release['isrc']) ?>"
                                            data-lyrics="<?= htmlspecialchars($release['lyrics']) ?>"
                                            data-youtube="<?= htmlspecialchars($release['youtube_url']) ?>"
                                            data-cover="<?= htmlspecialchars($release['album_cover']) ?>"
                                            data-trackfile="<?= htmlspecialchars($release['track_file']) ?>">
                                            View More
                                        </button>

                                        <a href="edit.php?id=<?= $release['id'] ?>"
                                            class="btn btn-warning btn-sm">Edit</a>
                                        <a href="delete.php?id=<?= $release['id'] ?>"
                                            class="btn btn-danger btn-sm">Delete</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </main>
        <!-- Right-Side Modal (Offcanvas) -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="detailsModal" aria-labelledby="detailsModalLabel">
            <div class="offcanvas-header">
                <h5 id="detailsModalLabel">Track Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <p><strong>Title:</strong> <span id="modal-title"></span></p>
                <p><strong>Genre:</strong> <span id="modal-genre"></span></p>
                <p><strong>Label:</strong> <span id="modal-label"></span></p>
                <p><strong>Release Date:</strong> <span id="modal-release"></span></p>
                <p><strong>Track Title:</strong> <span id="modal-track"></span></p>
                <p><strong>Primary Artist:</strong> <span id="modal-artist"></span></p>
                <p><strong>Writers:</strong> <span id="modal-writers"></span></p>
                <p><strong>Composers:</strong> <span id="modal-composers"></span></p>
                <p><strong>Music Directors:</strong> <span id="modal-directors"></span></p>
                <p><strong>Producer:</strong> <span id="modal-producer"></span></p>
                <p><strong>Publisher:</strong> <span id="modal-publisher"></span></p>
                <p><strong>Language:</strong> <span id="modal-language"></span></p>
                <p><strong>ISRC:</strong> <span id="modal-isrc"></span></p>
                <p><strong>Lyrics:</strong> <span id="modal-lyrics"></span></p>
                <p><strong>Youtube Link:</strong> <a id="modal-youtube" href="#" target="_blank">View</a></p>

                <p><strong>Album Cover:</strong></p>
                <img id="modal-cover" src="" class="img-fluid" alt="Album Cover">

                <p><strong>Track File:</strong></p>
                <audio id="modal-trackfile" controls>
                    <source src="" type="audio/wav">
                    Your browser does not support the audio tag.
                </audio>
            </div>
        </div>

        <script>
        document.addEventListener("DOMContentLoaded", function() {
            let viewMoreButtons = document.querySelectorAll(".view-more");

            viewMoreButtons.forEach(button => {
                button.addEventListener("click", function() {
                    document.getElementById("modal-title").textContent = this.dataset.title;
                    document.getElementById("modal-genre").textContent = this.dataset.genre;
                    document.getElementById("modal-label").textContent = this.dataset.label;
                    document.getElementById("modal-release").textContent = this.dataset.release;
                    document.getElementById("modal-track").textContent = this.dataset.track;
                    document.getElementById("modal-artist").textContent = this.dataset.artist;
                    document.getElementById("modal-writers").textContent = this.dataset.writers;
                    document.getElementById("modal-composers").textContent = this.dataset
                        .composers;
                    document.getElementById("modal-directors").textContent = this.dataset
                        .directors;
                    document.getElementById("modal-producer").textContent = this.dataset
                        .producer;
                    document.getElementById("modal-publisher").textContent = this.dataset
                        .publisher;
                    document.getElementById("modal-language").textContent = this.dataset
                        .language;
                    document.getElementById("modal-isrc").textContent = this.dataset.isrc;
                    document.getElementById("modal-lyrics").textContent = this.dataset.lyrics;
                    document.getElementById("modal-youtube").href = this.dataset.youtube;
                    document.getElementById("modal-cover").src = this.dataset.cover;
                    document.getElementById("modal-trackfile").src = this.dataset.trackfile;
                });
            });
        });
        </script>
    </div>
</div>

<script>
let table = new DataTable('#datatablesSimple');
</script>