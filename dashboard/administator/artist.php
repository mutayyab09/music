<?php include("../includes/header.php"); ?>
<?php
include("../.././config.php");

try {
    // ✅ Ensure session is started
 
    // ✅ Fetch tracks only for users with role = 1
    $stmt = $pdo->prepare("SELECT * FROM users WHERE role = 2 ORDER BY created_at DESC");
    $stmt->execute();
    $users = $stmt->fetchAll();
    

} catch (PDOException $e) {
    die("<div class='alert alert-danger'>Error fetching tracks: " . $e->getMessage() . "</div>");
}
?>


<div id="layoutSidenav">
    <?php include("../includes/sidebar.php"); ?> 
       <div id="layoutSidenav_content">
        <main>
            
        <div class="container-fluid px-4">
                <div class="container mt-5">
                    <h2>Admins</h2>
                    <div class="container-fluid px-4"></div>
                    <div class="card mb-4">
                        <div class="card-body">
                            <table id="datatablesSimple">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user) : ?>
                                        <tr>
                                            <td><?= htmlspecialchars($user['id']) ?></td>
                                            <td><?= htmlspecialchars($user['username']) ?></td>
                                            <td><?= htmlspecialchars($user['email']) ?></td>
                                            <td><?= htmlspecialchars($user['created_at']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <?php //include("../includes/footer.php"); ?>
    </div>
</div>

