<?php
require_once '../config.php';
check_login();

if ($_SESSION['role'] != 'staff') {
    redirect('../login.php');
}

$msg = "";
if (isset($_POST['send_alert'])) {
    $elderly_id = $_POST['elderly_id'];
    $details = mysqli_real_escape_string($conn, $_POST['details']);
    $severity = $_POST['severity'];
    
    // Get Elderly Name
    $elderly = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name, family_member_id FROM elderly_members WHERE id=$elderly_id"));
    $e_name = $elderly['name'];
    
    $message = "EMERGENCY: [$severity] for $e_name. Details: $details";
    
    // Notify Admins
    $admins = mysqli_query($conn, "SELECT id FROM users WHERE role='admin'");
    while($admin = mysqli_fetch_assoc($admins)) {
        $aid = $admin['id'];
        mysqli_query($conn, "INSERT INTO notifications (user_id, message, type) VALUES ($aid, '$message', 'Emergency')");
    }
    
    // Notify Family if exists
    if ($elderly['family_member_id']) {
        $fid = $elderly['family_member_id'];
        mysqli_query($conn, "INSERT INTO notifications (user_id, message, type) VALUES ($fid, '$message', 'Emergency')");
    }
    
    $msg = "Emergency alert sent to Admins and Family members.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Emergency Reporting - Staff</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>

<div class="d-flex">
    <div class="sidebar d-flex flex-column flex-shrink-0 p-3 text-white" style="width: 250px;">
        <a href="dashboard.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            <span class="fs-4">Staff Panel</span>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li><a href="dashboard.php" class="nav-link text-white"><i class="fas fa-home me-2"></i> Dashboard</a></li>
            <li><a href="manage_activities.php" class="nav-link text-white"><i class="fas fa-calendar-alt me-2"></i> Activities</a></li>
            <li><a href="health_records.php" class="nav-link text-white"><i class="fas fa-heartbeat me-2"></i> Health Records</a></li>
             <li><a href="emergency.php" class="nav-link active"><i class="fas fa-exclamation-triangle me-2"></i> Emergency</a></li>
        </ul>
    </div>

    <div class="flex-grow-1 p-4 bg-light">
        <h2 class="text-danger mb-4"><i class="fas fa-exclamation-circle"></i> Emergency Reporting</h2>
        
        <?php if ($msg): ?>
            <div class="alert alert-success"><?php echo $msg; ?></div>
        <?php endif; ?>

        <div class="card border-danger">
            <div class="card-header bg-danger text-white">Report Critical Incident</div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label>Elderly Member</label>
                        <select name="elderly_id" class="form-select" required>
                            <option value="">Select Member...</option>
                            <?php
                            $res = mysqli_query($conn, "SELECT id, name FROM elderly_members");
                            while($row = mysqli_fetch_assoc($res)) {
                                echo "<option value='{$row['id']}'>{$row['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label>Severity Level</label>
                        <select name="severity" class="form-select" required>
                            <option value="High">High - Immediate Attention Required</option>
                            <option value="Critical">Critical - Life Threatening</option>
                            <option value="Medium">Medium - Urgent but Stable</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Incident Details</label>
                        <textarea name="details" class="form-control" rows="5" required placeholder="Describe the medical emergency or incident..."></textarea>
                    </div>

                    <button type="submit" name="send_alert" class="btn btn-danger btn-lg w-100">
                        <i class="fas fa-bell"></i> SEND EMERGENCY ALERT
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
