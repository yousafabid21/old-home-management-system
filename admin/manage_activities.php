<?php
require_once '../config.php';
check_login();

if ($_SESSION['role'] != 'admin') {
    redirect('../login.php');
}

// Add Activity
if (isset($_POST['add_activity'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $type = $_POST['type'];
    $date = $_POST['scheduled_at'];
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $created_by = $_SESSION['user_id'];

    $sql = "INSERT INTO activities (title, description, event_type, scheduled_at, created_by) 
            VALUES ('$title', '$desc', '$type', '$date', $created_by)";
    
    if (mysqli_query($conn, $sql)) {
        $msg = "Activity scheduled!";
    } else {
        $error = mysqli_error($conn);
    }
}

// Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM activities WHERE id=$id");
    redirect('manage_activities.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Activities - Admin</title>
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
            <li><a href="manage_activities.php" class="nav-link active"><i class="fas fa-calendar-alt me-2"></i> Activities</a></li>
            <li><a href="manage_appointments.php" class="nav-link text-white"><i class="fas fa-calendar-check me-2"></i> Appointments</a></li>
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

    <div class="flex-grow-1 p-4 bg-light">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Activity Schedule (Admin)</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addActivityModal">
                <i class="fas fa-plus"></i> Schedule Activity
            </button>
        </div>

        <?php if (isset($msg)) echo "<div class='alert alert-success'>$msg</div>"; ?>

        <div class="card">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $acts = mysqli_query($conn, "SELECT * FROM activities ORDER BY scheduled_at DESC");
                        while($a = mysqli_fetch_assoc($acts)) {
                            echo "<tr>";
                            echo "<td>" . date('M d, H:i', strtotime($a['scheduled_at'])) . "</td>";
                            echo "<td>{$a['title']}</td>";
                            echo "<td><span class='badge bg-info'>{$a['event_type']}</span></td>";
                            echo "<td>{$a['description']}</td>";
                            echo "<td><a href='manage_activities.php?delete={$a['id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Delete?\")'><i class='fas fa-trash'></i></a></td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addActivityModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Schedule Activity</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Type</label>
                        <select name="type" class="form-select">
                            <option>Meal</option>
                            <option>Medication</option>
                            <option>Exercise</option>
                            <option>Social</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Date & Time</label>
                        <input type="datetime-local" name="scheduled_at" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Description</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_activity" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
