<?php include("../includes/header.php"); ?>

<link href="//cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css" rel="stylesheet"/>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="//cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>

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
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user) : ?>
                                        <tr>
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
<script>
    let table = new DataTable('#datatablesSimple');
</script>
