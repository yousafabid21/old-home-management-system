<?php
require_once '../config.php';
check_login();

if ($_SESSION['role'] != 'admin') {
    redirect('../login.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @media print {
            .sidebar, .no-print { display: none !important; }
            .content { margin: 0 !important; width: 100% !important; }
        }
    </style>
</head>
<body>

<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column flex-shrink-0 p-3 text-white no-print" style="width: 250px;">
        <a href="dashboard.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            <span class="fs-4">Admin Panel</span>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li><a href="dashboard.php" class="nav-link text-white"><i class="fas fa-home me-2"></i> Dashboard</a></li>
             <li><a href="manage_users.php" class="nav-link text-white"><i class="fas fa-users me-2"></i> Users</a></li>
            <li><a href="manage_elderly.php" class="nav-link text-white"><i class="fas fa-user-injured me-2"></i> Elderly Members</a></li>
            <li><a href="manage_staff.php" class="nav-link text-white"><i class="fas fa-user-nurse me-2"></i> Staff</a></li>
            <li><a href="manage_activities.php" class="nav-link text-white"><i class="fas fa-calendar-alt me-2"></i> Activities</a></li>
            <li><a href="manage_appointments.php" class="nav-link text-white"><i class="fas fa-calendar-check me-2"></i> Appointments</a></li>
            <li><a href="manage_donations.php" class="nav-link text-white"><i class="fas fa-hand-holding-usd me-2"></i> Donations</a></li>
             <li><a href="reports.php" class="nav-link active"><i class="fas fa-chart-bar me-2"></i> Reports</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="flex-grow-1 p-4 bg-light content">
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <h2>System Reports</h2>
            <button onclick="window.print()" class="btn btn-secondary"><i class="fas fa-print"></i> Print Report</button>
        </div>

        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Financial Report (Donations)</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Total Donations</th>
                                    <th>Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT DATE_FORMAT(donation_date, '%Y-%m') as month, SUM(amount) as total, COUNT(*) as count 
                                        FROM donations 
                                        GROUP BY month 
                                        ORDER BY month DESC";
                                $res = mysqli_query($conn, $sql);
                                while($row = mysqli_fetch_assoc($res)) {
                                    echo "<tr>";
                                    echo "<td>{$row['month']}</td>";
                                    echo "<td>$" . number_format($row['total'], 2) . "</td>";
                                    echo "<td>{$row['count']}</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Occupancy Report</h4>
                    </div>
                    <div class="card-body">
                        <?php
                        $total_beds = 50; // Assuming fixed capacity for demo
                        $occupied = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM elderly_members"))['c'];
                        $free = $total_beds - $occupied;
                        ?>
                        <div class="row text-center">
                            <div class="col-md-4">
                                <h5>Total Capacity</h5>
                                <h2><?php echo $total_beds; ?></h2>
                            </div>
                            <div class="col-md-4 text-success">
                                <h5>Occupied</h5>
                                <h2><?php echo $occupied; ?></h2>
                            </div>
                            <div class="col-md-4 text-warning">
                                <h5>Available</h5>
                                <h2><?php echo $free; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 mt-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Elderly Health Overview (Recent Checkups)</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Last Checkup</th>
                                    <th>Diagnosis</th>
                                    <th>Doctor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Get latest medical record for each elderly
                                $sql = "SELECT e.name, m.checkup_date, m.diagnosis, m.doctor_name 
                                        FROM elderly_members e 
                                        JOIN medical_records m ON e.id = m.elderly_id 
                                        WHERE m.checkup_date = (SELECT MAX(checkup_date) FROM medical_records WHERE elderly_id = e.id)
                                        ORDER BY m.checkup_date DESC LIMIT 10";
                                $health_res = mysqli_query($conn, $sql);
                                
                                if (mysqli_num_rows($health_res) > 0) {
                                    while($hr = mysqli_fetch_assoc($health_res)) {
                                        echo "<tr>";
                                        echo "<td>{$hr['name']}</td>";
                                        echo "<td>{$hr['checkup_date']}</td>";
                                        echo "<td>{$hr['diagnosis']}</td>";
                                        echo "<td>{$hr['doctor_name']}</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4' class='text-center'>No recent records found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
