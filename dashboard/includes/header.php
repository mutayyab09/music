<?php
session_start();
?>
<!DOCTYPE html>

<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Dashboard - SB Admin</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="../../assets/css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        
    </head>

    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <!-- Navbar Brand-->
            <a class="navbar-brand ps-3" href="index.php">
    <?php echo isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Start Bootstrap'; ?>
</a>            <!-- Sidebar Toggle-->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
            <!-- Navbar Search-->
            <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
                <div class="input-group">
                    <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
                    <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
                </div>
            </form>
            <!-- Navbar-->
            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="#!">Settings</a></li>
                        <li><a class="dropdown-item" href="#!">Activity Log</a></li>
                        <li><hr class="dropdown-divider" /></li>
                        <li><a class="dropdown-item" href="../../logout.php">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                    <div class="nav">
<?php if(isset($_SESSION["role"]) && $_SESSION["role"] == 0): ?>
    <a class="nav-link" href="../../dashboard/administator/register.php">
        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
        Create New Admin
    </a>
    <a class="nav-link" href="../../dashboard/administator/admin.php">
        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
        All Admin
    </a>
    <a class="nav-link" href="../../dashboard/administator/artist.php">
        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
        All Artist
    </a>
<?php endif; ?>

<?php if(isset($_SESSION["role"]) && $_SESSION["role"] == 1): ?>
    <a class="nav-link" href="../../dashboard/admin/artist.php">
        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
        All Artist
    </a>
<?php endif; ?>


    <a class="nav-link collapsed" href="" data-bs-toggle="collapse" data-bs-target="#tracksManagement" aria-expanded="false" aria-controls="tracksManagement">
        <div class="sb-nav-link-icon"><i class="fas fa-music"></i></div>
        Tracks Management
        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
    </a>
    <div class="collapse" id="tracksManagement" data-bs-parent="#sidenavAccordion">
        <nav class="sb-sidenav-menu-nested nav">
            <a class="nav-link" href="../../dashboard/artist/artist.php">All Tracks</a>
            <a class="nav-link" href="../../dashboard/artist/index.php">Add New Tracks</a>
            <!-- <a class="nav-link" href="pendingtracks.php">Pending Approvals</a>
            <a class="nav-link" href="approved-tracks.html">Approved Tracks</a>
            <a class="nav-link" href="rejected-tracks.html">Rejected Tracks</a> -->
        </nav>
    </div>
    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#artistManagement" aria-expanded="false" aria-controls="artistManagement">
        <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
        Artist Management
        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
    </a>
    <div class="collapse" id="artistManagement" data-bs-parent="#sidenavAccordion">
        <nav class="sb-sidenav-menu-nested nav">
            <a class="nav-link" href="all-artists.html">All Artists</a>
            <a class="nav-link" href="verified-artists.html">Verified Artists</a>
            <a class="nav-link" href="new-artist-requests.html">New Artist Requests</a>
            <a class="nav-link" href="banned-artists.html">Banned Artists</a>
        </nav>
    </div>

    <!-- Payments & Earnings -->
    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#paymentsEarnings" aria-expanded="false" aria-controls="paymentsEarnings">
        <div class="sb-nav-link-icon"><i class="fas fa-dollar-sign"></i></div>
        Payments & Earnings
        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
    </a>
    <div class="collapse" id="paymentsEarnings" data-bs-parent="#sidenavAccordion">
        <nav class="sb-sidenav-menu-nested nav">
            <a class="nav-link" href="transactions.html">Transactions</a>
            <a class="nav-link" href="artist-payouts.html">Artist Payouts</a>
            <a class="nav-link" href="revenue-reports.html">Revenue Reports</a>
        </nav>
    </div>

    <!-- Analytics & Reports -->
    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#analyticsReports" aria-expanded="false" aria-controls="analyticsReports">
        <div class="sb-nav-link-icon"><i class="fas fa-chart-line"></i></div>
        Analytics & Reports
        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
    </a>
    <div class="collapse" id="analyticsReports" data-bs-parent="#sidenavAccordion">
        <nav class="sb-sidenav-menu-nested nav">
            <a class="nav-link" href="user-engagement.html">User Engagement</a>
            <a class="nav-link" href="top-artists.html">Top Artists</a>
            <a class="nav-link" href="trending-tracks.html">Trending Tracks</a>
        </nav>
    </div>

    <!-- Settings -->
    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#settings" aria-expanded="false" aria-controls="settings">
        <div class="sb-nav-link-icon"><i class="fas fa-cogs"></i></div>
        Settings
        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
    </a>
    <div class="collapse" id="settings" data-bs-parent="#sidenavAccordion">
        <nav class="sb-sidenav-menu-nested nav">
            <a class="nav-link" href="site-configuration.html">Site Configuration</a>
            <a class="nav-link" href="user-roles.html">User Roles & Permissions</a>
        </nav>
    </div>
</div>

                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Logged in as:</div>
                        Start Bootstrap
                    </div>
                </nav>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
