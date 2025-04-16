<?php include("../../includes/header.php"); ?>
<link rel="stylesheet" href="/music/dashboard/assets/css/styles.css">

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

    $sql = "SELECT * FROM video_releases ORDER BY created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $video_releases = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                        <a class="btn bg-primary border-1 w-25"
                        href="<?= BASE_URL ?>dashboard/label/video/create.php">Create</a>
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
                                <?php foreach ($video_releases as $release): ?>
                                <tr>
                                    <td><img src="<?= htmlspecialchars($release['thumbnail_image']) ?>" alt="Album Cover"
                                            width="100"></td>
                                    <td><?= htmlspecialchars($release['title']) ?></td>
                                    <td><?= htmlspecialchars($release['primary_artist']) ?></td>
                                    <td><?= htmlspecialchars($release['label']) ?></td>
                                    <td><?= htmlspecialchars($release['store_release_date']) ?></td>

                                    <td>
                                        <button class="btn btn-primary btn-sm view-more" data-bs-toggle="offcanvas"
                                            data-bs-target="#detailsModal"
                                            data-title="<?= htmlspecialchars($release['title']) ?>"
                                            data-type="<?= htmlspecialchars($release['type']) ?>"
                                            data-primary_artist="<?= htmlspecialchars($release['primary_artist']) ?>"
                                            data-genre_sub_genre="<?= htmlspecialchars($release['genre_sub_genre']) ?>"
                                            data-language="<?= htmlspecialchars($release['language']) ?>"
                                            data-physical_release_date="<?= htmlspecialchars($release['physical_release_date']) ?>"
                                            data-writers="<?= htmlspecialchars($release['writers']) ?>"
                                            data-music_directors="<?= htmlspecialchars($release['music_directors']) ?>"
                                            data-producers="<?= htmlspecialchars($release['producers']) ?>"
                                            data-isrc ="<?= htmlspecialchars($release['isrc']) ?>"
                                            data-publisher="<?= htmlspecialchars($release['publisher']) ?>"
                                            data-label="<?= htmlspecialchars($release['label']) ?>"
                                            data-vevo_channel="<?= htmlspecialchars($release['vevo_channel']) ?>"
                                            data-description="<?= htmlspecialchars($release['description']) ?>"
                                            data-keywords	="<?= htmlspecialchars($release['keywords']) ?>"
                                            data-youtube_video_url="<?= htmlspecialchars($release['youtube_video_url']) ?>"
                                            data-cover="<?= htmlspecialchars($release['thumbnail_image']) ?>"
                                        >
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
        <p><strong>Type:</strong> <span id="modal-type"></span></p>
        <p><strong>Primary Artist:</strong> <span id="modal-primary_artist"></span></p>
        <p><strong>Genre:</strong> <span id="modal-genre"></span></p>
        <p><strong>Language:</strong> <span id="modal-language"></span></p>
        <p><strong>Release Date:</strong> <span id="modal-release"></span></p>
        <p><strong>Writers:</strong> <span id="modal-writers"></span></p>
        <p><strong>Music Directors:</strong> <span id="modal-music_directors"></span></p>
        <p><strong>Producers:</strong> <span id="modal-producers"></span></p>
        <p><strong>ISRC:</strong> <span id="modal-isrc"></span></p>
        <p><strong>Publisher:</strong> <span id="modal-publisher"></span></p>
        <p><strong>Label:</strong> <span id="modal-label"></span></p>
        <p><strong>VEVO Channel:</strong> <span id="modal-vevo"></span></p>
        <p><strong>Description:</strong> <span id="modal-description"></span></p>
        <p><strong>Keywords:</strong> <span id="modal-keywords"></span></p>
        <p><strong>YouTube Link:</strong> <a id="modal-youtube" href="#" target="_blank">View</a></p>

        <p><strong>Album Cover:</strong></p>
        <img id="modal-cover" src="" class="img-fluid" alt="Album Cover">
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const viewMoreButtons = document.querySelectorAll(".view-more");

    viewMoreButtons.forEach(button => {
        button.addEventListener("click", function () {
            document.getElementById("modal-title").textContent = this.dataset.title;
            document.getElementById("modal-type").textContent = this.dataset.type;
            document.getElementById("modal-primary_artist").textContent = this.dataset.primary_artist;
            document.getElementById("modal-genre").textContent = this.dataset.genre_sub_genre;
            document.getElementById("modal-language").textContent = this.dataset.language;
            document.getElementById("modal-release").textContent = this.dataset.physical_release_date;
            document.getElementById("modal-writers").textContent = this.dataset.writers;
            document.getElementById("modal-music_directors").textContent = this.dataset.music_directors;
            document.getElementById("modal-producers").textContent = this.dataset.producers;
            document.getElementById("modal-isrc").textContent = this.dataset.isrc;
            document.getElementById("modal-publisher").textContent = this.dataset.publisher;
            document.getElementById("modal-label").textContent = this.dataset.label;
            document.getElementById("modal-vevo").textContent = this.dataset.vevo_channel;
            document.getElementById("modal-description").textContent = this.dataset.description;
            document.getElementById("modal-keywords").textContent = this.dataset.keywords;
            document.getElementById("modal-youtube").href = this.dataset.youtube_video_url;
            document.getElementById("modal-cover").src = this.dataset.cover;
        });
    });
});
</script>


<script>
let table = new DataTable('#datatablesSimple');
</script>