<?php
require_once '../config.php';
check_login();

if ($_SESSION['role'] != 'admin') {
    redirect('../login.php');
}

// Update Details
if (isset($_POST['update_staff'])) {
    $uid = $_POST['user_id'];
    $designation = mysqli_real_escape_string($conn, $_POST['designation']);
    $shift = mysqli_real_escape_string($conn, $_POST['shift']);
    $joining = $_POST['joining_date'];

    // Check if record exists
    $check = mysqli_query($conn, "SELECT id FROM staff_p_details WHERE user_id=$uid");
    if (mysqli_num_rows($check) > 0) {
        $sql = "UPDATE staff_p_details SET designation='$designation', shift='$shift', joining_date='$joining' WHERE user_id=$uid";
    } else {
        $sql = "INSERT INTO staff_p_details (user_id, designation, shift, joining_date) VALUES ($uid, '$designation', '$shift', '$joining')";
    }
    
    if (mysqli_query($conn, $sql)) {
        $msg = "Staff details updated.";
    } else {
        $error = mysqli_error($conn);
    }
}

// Add New Staff
if (isset($_POST['add_staff'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    
    // Staff Details
    $designation = mysqli_real_escape_string($conn, $_POST['designation']);
    $shift = mysqli_real_escape_string($conn, $_POST['shift']);
    $joining = $_POST['joining_date'];

    // 1. Create User
    $sql_user = "INSERT INTO users (name, email, password, role, contact_no) VALUES ('$name', '$email', '$password', 'staff', '$contact')";
    
    if (mysqli_query($conn, $sql_user)) {
        $uid = mysqli_insert_id($conn);
        
        // 2. Add Staff Details
        $sql_details = "INSERT INTO staff_p_details (user_id, designation, shift, joining_date) VALUES ($uid, '$designation', '$shift', '$joining')";
        mysqli_query($conn, $sql_details);
        
        $msg = "New staff member added successfully!";
    } else {
        $error = "Error adding user: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Staff - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>

<div class="d-flex">
    <div class="sidebar d-flex flex-column flex-shrink-0 p-3 text-white" style="width: 250px;">
        <a href="dashboard.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            <span class="fs-4">Admin Panel</span>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li><a href="dashboard.php" class="nav-link text-white"><i class="fas fa-home me-2"></i> Dashboard</a></li>
            <li><a href="manage_users.php" class="nav-link text-white"><i class="fas fa-users me-2"></i> Users</a></li>
            <li><a href="manage_elderly.php" class="nav-link text-white"><i class="fas fa-user-injured me-2"></i> Elderly Members</a></li>
            <li><a href="manage_staff.php" class="nav-link active"><i class="fas fa-user-nurse me-2"></i> Staff</a></li>
            <li><a href="manage_activities.php" class="nav-link text-white"><i class="fas fa-calendar-alt me-2"></i> Activities</a></li>
            <li><a href="manage_appointments.php" class="nav-link text-white"><i class="fas fa-calendar-check me-2"></i> Appointments</a></li>
            <li><a href="manage_donations.php" class="nav-link text-white"><i class="fas fa-hand-holding-usd me-2"></i> Donations</a></li>
             <li><a href="reports.php" class="nav-link text-white"><i class="fas fa-chart-bar me-2"></i> Reports</a></li>
        </ul>
    </div>

    <div class="flex-grow-1 p-4 bg-light">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Staff Directory & Shifts</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                <i class="fas fa-plus"></i> Add New Staff
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
                            <th>Email</th>
                            <th>Designation</th>
                            <th>Shift</th>
                            <th>Joining Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT u.id, u.name, u.email, s.designation, s.shift, s.joining_date 
                                FROM users u 
                                LEFT JOIN staff_p_details s ON u.id = s.user_id 
                                WHERE u.role = 'staff'";
                        $res = mysqli_query($conn, $sql);
                        while($row = mysqli_fetch_assoc($res)) {
                            echo "<tr>";
                            echo "<td>{$row['name']}</td>";
                            echo "<td>{$row['email']}</td>";
                            echo "<td>" . ($row['designation'] ?? '-') . "</td>";
                            echo "<td>" . ($row['shift'] ?? '-') . "</td>";
                            echo "<td>" . ($row['joining_date'] ?? '-') . "</td>";
                            echo "<td><button class='btn btn-sm btn-primary' data-bs-toggle='modal' data-bs-target='#editStaff{$row['id']}'>Edit Details</button></td>";
                            echo "</tr>";
                            
                            // Modal inside loop for simplicity
                            echo "
                            <div class='modal fade' id='editStaff{$row['id']}' tabindex='-1'>
                                <div class='modal-dialog'>
                                    <div class='modal-content'>
                                        <form method='POST'>
                                            <div class='modal-header'>
                                                <h5 class='modal-title'>Edit Staff: {$row['name']}</h5>
                                                <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                                            </div>
                                            <div class='modal-body'>
                                                <input type='hidden' name='user_id' value='{$row['id']}'>
                                                <div class='mb-3'>
                                                    <label>Designation</label>
                                                    <input type='text' name='designation' class='form-control' value='{$row['designation']}'>
                                                </div>
                                                <div class='mb-3'>
                                                    <label>Shift</label>
                                                    <select name='shift' class='form-select'>
                                                        <option value='Morning' " . ($row['shift']=='Morning'?'selected':'') . ">Morning</option>
                                                        <option value='Evening' " . ($row['shift']=='Evening'?'selected':'') . ">Evening</option>
                                                        <option value='Night' " . ($row['shift']=='Night'?'selected':'') . ">Night</option>
                                                    </select>
                                                </div>
                                                <div class='mb-3'>
                                                    <label>Joining Date</label>
                                                    <input type='date' name='joining_date' class='form-control' value='{$row['joining_date']}'>
                                                </div>
                                            </div>
                                            <div class='modal-footer'>
                                                <button type='submit' name='update_staff' class='btn btn-primary'>Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            ";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Staff Modal -->
<div class="modal fade" id="addStaffModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Staff Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6 class="text-primary">Personal Details</h6>
                    <div class="mb-3">
                        <label>Full Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Contact Number</label>
                        <input type="text" name="contact" class="form-control">
                    </div>
                    
                    <hr>
                    <h6 class="text-primary">Job Details</h6>
                    <div class="mb-3">
                        <label>Designation</label>
                        <input type="text" name="designation" class="form-control" placeholder="e.g. Nurse, Caretaker" required>
                    </div>
                    <div class="mb-3">
                        <label>Shift</label>
                        <select name="shift" class="form-select">
                            <option value="Morning">Morning</option>
                            <option value="Evening">Evening</option>
                            <option value="Night">Night</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Joining Date</label>
                        <input type="date" name="joining_date" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_staff" class="btn btn-primary">Create Staff Account</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
