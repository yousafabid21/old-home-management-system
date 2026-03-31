<?php
require_once '../config.php';
check_login();

if ($_SESSION['role'] != 'admin') {
    redirect('../login.php');
}

// Handle Status Update
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action']; // 'approve' or 'reject'
    $status = ($action == 'approve') ? 'Approved' : 'Rejected';
    
    $sql = "UPDATE appointments SET status = '$status' WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        redirect('manage_appointments.php?msg=updated');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Appointments - Admin</title>
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
            <li><a href="dashboard.php" class="nav-link text-white"><i class="fas fa-home me-2"></i> Dashboard</a></li>
            <li><a href="manage_users.php" class="nav-link text-white"><i class="fas fa-users me-2"></i> Users</a></li>
            <li><a href="manage_elderly.php" class="nav-link text-white"><i class="fas fa-user-injured me-2"></i> Elderly Members</a></li>
            <li><a href="manage_staff.php" class="nav-link text-white"><i class="fas fa-user-nurse me-2"></i> Staff</a></li>
            <li><a href="manage_activities.php" class="nav-link text-white"><i class="fas fa-calendar-alt me-2"></i> Activities</a></li>
            <li><a href="manage_appointments.php" class="nav-link active"><i class="fas fa-calendar-check me-2"></i> Appointments</a></li>
            <li><a href="manage_donations.php" class="nav-link text-white"><i class="fas fa-hand-holding-usd me-2"></i> Donations</a></li>
             <li><a href="reports.php" class="nav-link text-white"><i class="fas fa-chart-bar me-2"></i> Reports</a></li>
        </ul>
        <hr>
        <div class="dropdown">
             <a href="../logout.php" class="d-flex align-items-center text-white text-decoration-none">
                <strong>Sign out</strong>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-grow-1 p-4 bg-light">
        <h2 class="mb-4">Manage Appointments</h2>
        
        <div class="card">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Requested By</th>
                            <th>Elderly Member</th>
                            <th>Date & Time</th>
                            <th>Notes</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT a.*, u.name as requester, e.name as elderly_name 
                                FROM appointments a 
                                JOIN users u ON a.family_member_id = u.id 
                                JOIN elderly_members e ON a.elderly_id = e.id 
                                ORDER BY a.status = 'Pending' DESC, a.appointment_date DESC";
                        $res = mysqli_query($conn, $sql);
                        
                        if (mysqli_num_rows($res) > 0) {
                            while($row = mysqli_fetch_assoc($res)) {
                                $statusClass = match($row['status']) {
                                    'Approved' => 'success',
                                    'Rejected' => 'danger',
                                    default => 'warning text-dark'
                                };
                                echo "<tr>";
                                echo "<td>{$row['requester']}</td>";
                                echo "<td>{$row['elderly_name']}</td>";
                                echo "<td>" . date('M d, Y h:i A', strtotime($row['appointment_date'])) . "</td>";
                                echo "<td>{$row['notes']}</td>";
                                echo "<td><span class='badge bg-{$statusClass}'>{$row['status']}</span></td>";
                                echo "<td>";
                                if ($row['status'] == 'Pending') {
                                    echo "<a href='manage_appointments.php?action=approve&id={$row['id']}' class='btn btn-sm btn-success me-1' onclick=\"return confirm('Approve this appointment?')\"><i class='fas fa-check'></i></a>";
                                    echo "<a href='manage_appointments.php?action=reject&id={$row['id']}' class='btn btn-sm btn-danger' onclick=\"return confirm('Reject this appointment?')\"><i class='fas fa-times'></i></a>";
                                } else {
                                    echo "<span class='text-muted'>-</span>";
                                }
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center'>No appointments found.</td></tr>";
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
