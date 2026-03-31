<?php
require_once '../config.php';
check_login();

if ($_SESSION['role'] != 'admin') {
    redirect('../login.php');
}

$id = intval($_GET['id']);
$query = "SELECT * FROM elderly_members WHERE id=$id";
$result = mysqli_query($conn, $query);
$elderly = mysqli_fetch_assoc($result);

if (!$elderly) {
    redirect('manage_elderly.php');
}

// Handle Update
if (isset($_POST['update_elderly'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $admission = $_POST['admission_date'];
    $room = mysqli_real_escape_string($conn, $_POST['room_no']);
    $history = mysqli_real_escape_string($conn, $_POST['history']);
    $family_id = !empty($_POST['family_id']) ? $_POST['family_id'] : "NULL";

    $sql = "UPDATE elderly_members SET 
            name='$name', 
            dob='$dob', 
            gender='$gender', 
            admission_date='$admission', 
            room_no='$room', 
            medical_history='$history', 
            family_member_id=$family_id 
            WHERE id=$id";
            
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Updated successfully'); window.location='manage_elderly.php';</script>";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Elderly - Admin</title>
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
            <h2>Edit Elderly Member</h2>
            <a href="manage_elderly.php" class="btn btn-secondary">Back</a>
        </div>

        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Full Name</label>
                            <input type="text" name="name" class="form-control" value="<?php echo $elderly['name']; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Gender</label>
                            <select name="gender" class="form-select">
                                <option <?php if($elderly['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                                <option <?php if($elderly['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                                <option <?php if($elderly['gender'] == 'Other') echo 'selected'; ?>>Other</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Date of Birth</label>
                            <input type="date" name="dob" class="form-control" value="<?php echo $elderly['dob']; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Admission Date</label>
                            <input type="date" name="admission_date" class="form-control" value="<?php echo $elderly['admission_date']; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Room Number</label>
                            <input type="text" name="room_no" class="form-control" value="<?php echo $elderly['room_no']; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Assign Family Member</label>
                            <select name="family_id" class="form-select">
                                <option value="">-- None --</option>
                                <?php
                                $families = mysqli_query($conn, "SELECT id, name FROM users WHERE role='family'");
                                while($f = mysqli_fetch_assoc($families)) {
                                    $selected = ($elderly['family_member_id'] == $f['id']) ? 'selected' : '';
                                    echo "<option value='{$f['id']}' $selected>{$f['name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label>Medical History</label>
                            <textarea name="history" class="form-control" rows="3"><?php echo $elderly['medical_history']; ?></textarea>
                        </div>
                    </div>
                    <button type="submit" name="update_elderly" class="btn btn-primary">Update Member</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
