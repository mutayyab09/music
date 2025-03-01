<?php include("includes/header.php"); ?>

<div id="layoutSidenav">
    <?php include("includes/sidebar.php"); ?> <!-- Sidebar Include -->

    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Dashboard</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item active">Admin Overview</li>
                </ol>

                <!-- Stats Cards -->
                <div class="row">
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-primary text-white mb-4">
                            <div class="card-body">Total Artists: <span id="totalArtists">0</span></div>
                            <div class="card-footer">
                                <a class="text-white" href="all-artists.php">View Artists</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-success text-white mb-4">
                            <div class="card-body">Total Tracks: <span id="totalTracks">0</span></div>
                            <div class="card-footer">
                                <a class="text-white" href="all-tracks.php">View Tracks</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-warning text-white mb-4">
                            <div class="card-body">Pending Approvals: <span id="pendingTracks">0</span></div>
                            <div class="card-footer">
                                <a class="text-white" href="pending-tracks.php">Approve Now</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-danger text-white mb-4">
                            <div class="card-body">Total Earnings: $<span id="totalEarnings">0</span></div>
                            <div class="card-footer">
                                <a class="text-white" href="revenue-reports.php">View Reports</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="row">
                    <div class="col-xl-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-chart-area me-1"></i>
                                Monthly Uploads
                            </div>
                            <div class="card-body">
                                <canvas id="uploadsChart" width="100%" height="40"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-chart-bar me-1"></i>
                                Earnings Report
                            </div>
                            <div class="card-body">
                                <canvas id="earningsChart" width="100%" height="40"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-list"></i> Recent Activity
                    </div>
                    <div class="card-body">
                        <ul id="recentActivity" class="list-group">
                            <li class="list-group-item">No recent activity</li>
                        </ul>
                    </div>
                </div>
            </div>
        </main>

    </div>
</div>
