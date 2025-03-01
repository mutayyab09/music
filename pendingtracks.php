<?php include("includes/header.php"); ?>
<div id="layoutSidenav">
    <?php include("includes/sidebar.php"); ?>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Pending Track Approvals</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item active">Approve or Reject Uploaded Tracks</li>
                </ol>

                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-clock"></i> Tracks Awaiting Approval
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Track Name</th>
                                    <th>Artist</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                               
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
        <?php include("includes/footer.php"); ?>
    </div>
</div>