<?php
require_once '../config.php';
check_login();

if ($_SESSION['role'] != 'staff') {
    redirect('../login.php');
}

$elderly_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM elderly_members"))['c'];
$today_activities = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM activities WHERE DATE(scheduled_at) = CURDATE()"))['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Dashboard - Old Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>

<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column flex-shrink-0 p-3 text-white" style="width: 250px;">
        <a href="dashboard.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            <span class="fs-4">Staff Panel</span>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li><a href="dashboard.php" class="nav-link active"><i class="fas fa-home me-2"></i> Dashboard</a></li>
            <li><a href="manage_activities.php" class="nav-link text-white"><i class="fas fa-calendar-alt me-2"></i> Activities</a></li>
            <li><a href="health_records.php" class="nav-link text-white"><i class="fas fa-heartbeat me-2"></i> Health Records</a></li>
        </ul>
        <hr>
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                <strong><?php echo $_SESSION['name']; ?></strong>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                <li><a class="dropdown-item" href="../logout.php">Sign out</a></li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-grow-1 p-4 bg-light">
        <h2>Staff Dashboard</h2>
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card bg-info text-white h-100">
                    <div class="card-body">
                        <h3>Elderly Members</h3>
                        <p class="fs-4"><?php echo $elderly_count; ?> Residents</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white h-100">
                    <div class="card-body">
                        <h3>Today's Activities</h3>
                        <p class="fs-4"><?php echo $today_activities; ?> Scheduled</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-dark h-100">
                    <div class="card-body">
                        <h3>Upcoming Visits</h3>
                        <?php 
                        $visits_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM appointments WHERE status='Approved' AND appointment_date >= CURDATE()"))['c']; 
                        ?>
                        <p class="fs-4"><?php echo $visits_count; ?> Approved</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Upcoming Activities</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <?php
                    $acts = mysqli_query($conn, "SELECT * FROM activities WHERE scheduled_at >= NOW() ORDER BY scheduled_at ASC LIMIT 5");
                    if (mysqli_num_rows($acts) > 0) {
                        while($a = mysqli_fetch_assoc($acts)) {
                            echo "<li class='list-group-item d-flex justify-content-between align-items-center'>";
                            echo "<div><strong>{$a['title']}</strong> <br> <small class='text-muted'>{$a['description']}</small></div>";
                            echo "<span class='badge bg-primary rounded-pill'>" . date('H:i', strtotime($a['scheduled_at'])) . "</span>";
                            echo "</li>";
                        }
                    } else {
                        echo "<li class='list-group-item'>No upcoming activities.</li>";
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
