<?php include("../includes/header.php"); ?>

<link href="//cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css" rel="stylesheet"/>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="//cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>

<?php 
include("../.././config.php");  
try {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $user_id = $_SESSION["id"];
    
    $stmt = $pdo->prepare("SELECT p.id, p.amount, t.track_name, p.created_at, p.updated_at, u.username AS admin_name, 
                            CASE p.status 
                                WHEN 1 THEN 'Transferred' 
                                WHEN 2 THEN 'Pending' 
                                WHEN 3 THEN 'Issue' 
                                ELSE 'Unknown' 
                            END AS status_text
                            FROM royalty p
                            JOIN tracks t ON p.track_id = t.id
                            LEFT JOIN users u ON p.admin_id = u.id
                            WHERE p.artist_id = ?
                            ORDER BY p.created_at DESC");
    
    $stmt->execute([$user_id]);
    $payments = $stmt->fetchAll();
} catch (PDOException $e) {
    die("<div class='alert alert-danger'>Error fetching records: " . $e->getMessage() . "</div>");
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
                    <h2>Royalties</h2>
                    <div class="card mb-4">
                        <div class="card-body">
                            <table id="datatablesSimple">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Amount</th>
                                        <th>Track Name</th>
                                        <th>Created By</th>
                                        <th>Created At</th>
                                        
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payments as $payment) : ?>
                                        <tr>
                                            <td><?= htmlspecialchars($payment['id']) ?></td>
                                            <td><?= htmlspecialchars($payment['amount']) ?></td>
                                            <td><?= htmlspecialchars($payment['track_name']) ?></td>
                                            <td><?= htmlspecialchars($payment['admin_name'] ?? 'N/A') ?></td>
                                            <td><?= date("m/d/Y", strtotime($payment['created_at']))  ?></td>
                                            <td><?= htmlspecialchars($payment['status_text']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    let table = new DataTable('#datatablesSimple');
</script>
