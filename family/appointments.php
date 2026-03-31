<?php
require_once '../config.php';
check_login();

if ($_SESSION['role'] != 'family') {
    redirect('../login.php');
}

$uid = $_SESSION['user_id'];
$elderly = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM elderly_members WHERE family_member_id = $uid"));

if (!$elderly) {
    // If no elderly member linked, user can't book appointment
    $error_msg = "No elderly member linked to your account.";
}

// Handle New Appointment
$msg = "";
if (isset($_POST['book_appointment']) && $elderly) {
    $date = $_POST['date'];
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);
    $eid = $elderly['id'];

    if (strtotime($date) < time()) {
        $msg = "Please choose a future date.";
        $msg_type = "danger";
    } else {
        $sql = "INSERT INTO appointments (family_member_id, elderly_id, appointment_date, status, notes) 
                VALUES ($uid, $eid, '$date', 'Pending', '$notes')";
        if (mysqli_query($conn, $sql)) {
            $msg = "Appointment request sent successfully!";
            $msg_type = "success";
        } else {
            $msg = "Error: " . mysqli_error($conn);
            $msg_type = "danger";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Appointments - Old Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">Family Portal</a>
        <div class="d-flex">
            <a href="dashboard.php" class="nav-link text-white me-3">Dashboard</a>
            <a href="appointments.php" class="nav-link text-white me-3 fw-bold">Appointments</a>
            <span class="navbar-text text-white me-3">|</span>
            <span class="navbar-text text-white me-3">Welcome, <?php echo $_SESSION['name']; ?></span>
            <a href="../logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container">
    <h2 class="mb-4">My Appointments</h2>

    <?php if (!empty($msg)): ?>
        <div class="alert alert-<?php echo $msg_type; ?> alert-dismissible fade show" role="alert">
            <?php echo $msg; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($error_msg)): ?>
        <div class="alert alert-warning"><?php echo $error_msg; ?></div>
    <?php else: ?>

        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">Request New Appointment</div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label>Date & Time</label>
                                <input type="datetime-local" name="date" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Reason / Notes</label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="e.g. Weekly visit"></textarea>
                            </div>
                            <button type="submit" name="book_appointment" class="btn btn-success w-100">Submit Request</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Appointment History</div>
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                    <th>Requested On</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT * FROM appointments WHERE family_member_id = $uid ORDER BY appointment_date DESC";
                                $res = mysqli_query($conn, $sql);
                                if (mysqli_num_rows($res) > 0) {
                                    while($row = mysqli_fetch_assoc($res)) {
                                        $statusClass = match($row['status']) {
                                            'Approved' => 'success',
                                            'Rejected' => 'danger',
                                            default => 'warning text-dark'
                                        };
                                        echo "<tr>";
                                        echo "<td>" . date('M d, Y h:i A', strtotime($row['appointment_date'])) . "</td>";
                                        echo "<td><span class='badge bg-{$statusClass}'>{$row['status']}</span></td>";
                                        echo "<td>{$row['notes']}</td>";
                                        echo "<td>" . date('M d, Y', strtotime($row['created_at'])) . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4' class='text-center text-muted'>No appointments found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
