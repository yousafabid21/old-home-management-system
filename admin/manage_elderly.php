<?php
require_once '../config.php';
check_login();

if ($_SESSION['role'] != 'admin') {
    redirect('../login.php');
}

// Handle Add Elderly
if (isset($_POST['add_elderly'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $admission = $_POST['admission_date'];
    $room = mysqli_real_escape_string($conn, $_POST['room_no']);
    $history = mysqli_real_escape_string($conn, $_POST['history']);
    $family_id = !empty($_POST['family_id']) ? $_POST['family_id'] : "NULL";

    $sql = "INSERT INTO elderly_members (name, dob, gender, admission_date, room_no, medical_history, family_member_id) 
            VALUES ('$name', '$dob', '$gender', '$admission', '$room', '$history', $family_id)";
            
    if (mysqli_query($conn, $sql)) {
        $msg = "Elderly member registered successfully";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM elderly_members WHERE id=$id");
    redirect('manage_elderly.php');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Elderly - Admin</title>
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
            <li><a href="manage_elderly.php" class="nav-link active"><i class="fas fa-user-injured me-2"></i> Elderly Members</a></li>
            <li><a href="manage_staff.php" class="nav-link text-white"><i class="fas fa-user-nurse me-2"></i> Staff</a></li>
            <li><a href="manage_activities.php" class="nav-link text-white"><i class="fas fa-calendar-alt me-2"></i> Activities</a></li>
            <li><a href="manage_appointments.php" class="nav-link text-white"><i class="fas fa-calendar-check me-2"></i> Appointments</a></li>
            <li><a href="manage_donations.php" class="nav-link text-white"><i class="fas fa-hand-holding-usd me-2"></i> Donations</a></li>
             <li><a href="reports.php" class="nav-link text-white"><i class="fas fa-chart-bar me-2"></i> Reports</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="flex-grow-1 p-4 bg-light">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Elderly Member Management</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addElderlyModal">
                <i class="fas fa-plus"></i> Add New Member
            </button>
        </div>

        <?php if (isset($msg)) echo "<div class='alert alert-success'>$msg</div>"; ?>
        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

        <div class="card">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Room</th>
                            <th>Admission Date</th>
                            <th>Family Member</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT e.*, u.name as family_name 
                                  FROM elderly_members e 
                                  LEFT JOIN users u ON e.family_member_id = u.id 
                                  ORDER BY e.id DESC";
                        $result = mysqli_query($conn, $query);
                        
                        while ($row = mysqli_fetch_assoc($result)) {
                            // Calculate Age
                            $dob = new DateTime($row['dob']);
                            $now = new DateTime();
                            $age = $now->diff($dob)->y;

                            echo "<tr>";
                            echo "<td>{$row['name']}</td>";
                            echo "<td>{$age} yrs ({$row['gender']})</td>";
                            echo "<td>{$row['room_no']}</td>";
                            echo "<td>{$row['admission_date']}</td>";
                            echo "<td>" . ($row['family_name'] ?? '<span class="text-muted">None</span>') . "</td>";
                            echo "<td>
                                    <a href='edit_elderly.php?id={$row['id']}' class='btn btn-sm btn-info text-white'><i class='fas fa-edit'></i></a>
                                    <a href='manage_elderly.php?delete={$row['id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure?\")'><i class='fas fa-trash'></i></a>
                                  </td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addElderlyModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Register Elderly Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Full Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Gender</label>
                            <select name="gender" class="form-select">
                                <option>Male</option>
                                <option>Female</option>
                                <option>Other</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Date of Birth</label>
                            <input type="date" name="dob" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Admission Date</label>
                            <input type="date" name="admission_date" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Room Number</label>
                            <input type="text" name="room_no" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Assign Family Member</label>
                            <select name="family_id" class="form-select">
                                <option value="">-- None --</option>
                                <?php
                                $families = mysqli_query($conn, "SELECT id, name FROM users WHERE role='family'");
                                while($f = mysqli_fetch_assoc($families)) {
                                    echo "<option value='{$f['id']}'>{$f['name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label>Medical History</label>
                            <textarea name="history" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_elderly" class="btn btn-primary">Register</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
