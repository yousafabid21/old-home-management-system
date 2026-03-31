<?php
require_once '../config.php';
check_login();

if ($_SESSION['role'] != 'family') {
    redirect('../login.php');
}

$uid = $_SESSION['user_id'];
$elderly = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM elderly_members WHERE family_member_id = $uid"));

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Family Dashboard - Old Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">Family Portal</a>
        <div class="d-flex">
            <a href="dashboard.php" class="nav-link text-white me-3 fw-bold">Dashboard</a>
            <a href="appointments.php" class="nav-link text-white me-3">Appointments</a>
            <span class="navbar-text text-white me-3">|</span>
            <span class="navbar-text text-white me-3">Welcome, <?php echo $_SESSION['name']; ?></span>
            <a href="../logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container">
    <?php if (!$elderly): ?>
        <div class="alert alert-warning text-center">
            <h4>No Elderly Member Linked</h4>
            <p>You haven't registered any elderly relative yet.</p>
            <a href="register_elderly.php" class="btn btn-primary mt-2">Register Elderly Relative</a>
            <p class="mt-2 text-muted"><small>If you have already registered, please wait for admin approval/linking.</small></p>
        </div>
    <?php else: ?>
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-user-circle fa-5x text-muted"></i>
                        </div>
                        <h3><?php echo $elderly['name']; ?></h3>
                        <p class="text-muted">Room: <?php echo $elderly['room_no']; ?></p>
                        <hr>
                        <div class="text-start">
                            <p><strong>DOB:</strong> <?php echo $elderly['dob']; ?></p>
                            <p><strong>Gender:</strong> <?php echo $elderly['gender']; ?></p>
                            <p><strong>Admission:</strong> <?php echo $elderly['admission_date']; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-3">
                    <div class="card-header bg-danger text-white">Emergency Contact</div>
                    <div class="card-body">
                        <p class="mb-0"><strong>Admin Office:</strong> +1 234 567 890</p>
                        <p class="mb-0"><strong>Medical Staff:</strong> +1 987 654 321</p>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <a href="appointments.php" class="btn btn-success btn-lg"><i class="fas fa-calendar-check me-2"></i> Book Appointment</a>
                </div>
            </div>
            
            <div class="col-md-8">
                <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="health-tab" data-bs-toggle="tab" data-bs-target="#health" type="button">Health Records</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity" type="button">Daily Routine</button>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <!-- Health Tab -->
                    <div class="tab-pane fade show active" id="health">
                        <h4 class="mb-3">Recent Medical Updates</h4>
                        <?php
                        $health = mysqli_query($conn, "SELECT * FROM medical_records WHERE elderly_id = {$elderly['id']} ORDER BY checkup_date DESC");
                        if (mysqli_num_rows($health) > 0) {
                            while($h = mysqli_fetch_assoc($health)) {
                                echo "<div class='card mb-3 border-start border-4 border-info'>";
                                echo "<div class='card-body'>";
                                echo "<h5 class='card-title'>{$h['diagnosis']} <small class='text-muted float-end'>{$h['checkup_date']}</small></h5>";
                                echo "<h6 class='card-subtitle mb-2 text-muted'>Dr. {$h['doctor_name']}</h6>";
                                echo "<p class='card-text'><strong>Medications:</strong> {$h['medications']}</p>";
                                if ($h['notes']) echo "<p class='card-text text-muted'><small>Note: {$h['notes']}</small></p>";
                                echo "</div></div>";
                            }
                        } else {
                            echo "<p class='text-muted'>No medical records found.</p>";
                        }
                        ?>
                    </div>
                    
                    <!-- Activity Tab -->
                    <div class="tab-pane fade" id="activity">
                        <h4 class="mb-3">Today's Schedule & Activities</h4>
                        <?php
                        // In a real app we'd filter by assignments, but here showing all facility activities for simplicity or filtered
                        // Assuming activities are general for now unless specific assignment logic exists
                        $acts = mysqli_query($conn, "SELECT * FROM activities WHERE scheduled_at >= CURDATE() ORDER BY scheduled_at ASC LIMIT 10");
                         if (mysqli_num_rows($acts) > 0) {
                             echo "<ul class='list-group'>";
                            while($a = mysqli_fetch_assoc($acts)) {
                                echo "<li class='list-group-item d-flex justify-content-between align-items-center'>";
                                echo "<div><span class='badge bg-secondary me-2'>{$a['event_type']}</span> {$a['title']} <br> <small class='text-muted'>{$a['description']}</small></div>";
                                echo "<span>" . date('M d, H:i', strtotime($a['scheduled_at'])) . "</span>";
                                echo "</li>";
                            }
                            echo "</ul>";
                        } else {
                            echo "<p class='text-muted'>No upcoming activities.</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
