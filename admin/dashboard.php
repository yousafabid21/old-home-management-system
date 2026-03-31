<?php
require_once '../config.php';
check_login();

if ($_SESSION['role'] != 'admin') {
    redirect('../login.php');
}

// Get counts
$user_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM users"))['c'];
$elderly_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM elderly_members"))['c'];
$donation_sum = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as s FROM donations"))['s'] ?? 0;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Old Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>

<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column flex-shrink-0 p-3 text-white" style="width: 250px;">
        <a href="dashboard.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            <span class="fs-4">Admin Panel</span>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="dashboard.php" class="nav-link active" aria-current="page">
                    <i class="fas fa-home me-2"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="manage_users.php" class="nav-link text-white">
                    <i class="fas fa-users me-2"></i> Users
                </a>
            </li>
            <li>
                <a href="manage_elderly.php" class="nav-link text-white">
                    <i class="fas fa-user-injured me-2"></i> Elderly Members
                </a>
            </li>
            <li>
                <a href="manage_staff.php" class="nav-link text-white">
                    <i class="fas fa-user-nurse me-2"></i> Staff
                </a>
            </li>
            <li>
                <a href="manage_activities.php" class="nav-link text-white">
                    <i class="fas fa-calendar-alt me-2"></i> Activities
                </a>
            </li>
            <li>
                <a href="manage_appointments.php" class="nav-link text-white">
                    <i class="fas fa-calendar-check me-2"></i> Appointments
                </a>
            </li>
            <li>
                <a href="manage_donations.php" class="nav-link text-white">
                    <i class="fas fa-hand-holding-usd me-2"></i> Donations
                </a>
            </li>
            <li>
                <a href="reports.php" class="nav-link text-white">
                    <i class="fas fa-chart-bar me-2"></i> Reports
                </a>
            </li>
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
        <h2>Dashboard Overview</h2>
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Total Users</h6>
                                <h2 class="mb-0"><?php echo $user_count; ?></h2>
                            </div>
                            <i class="fas fa-users fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Elderly Residents</h6>
                                <h2 class="mb-0"><?php echo $elderly_count; ?></h2>
                            </div>
                            <i class="fas fa-user-injured fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Total Donations</h6>
                                <h2 class="mb-0">$<?php echo number_format($donation_sum, 2); ?></h2>
                            </div>
                            <i class="fas fa-dollar-sign fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Activity Table Placeholder -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Recent Registrations</h5>
            </div>
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Email</th>
                            <th>Joined Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $recent_users = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at DESC LIMIT 5");
                        while($row = mysqli_fetch_assoc($recent_users)) {
                            echo "<tr>";
                            echo "<td>{$row['name']}</td>";
                            echo "<td><span class='badge bg-info text-dark'>{$row['role']}</span></td>";
                            echo "<td>{$row['email']}</td>";
                            echo "<td>" . date('M d, Y', strtotime($row['created_at'])) . "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
