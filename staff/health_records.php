<?php
require_once '../config.php';
check_login();

if ($_SESSION['role'] != 'staff') {
    redirect('../login.php');
}

$selected_elderly = null;
if (isset($_GET['view'])) {
    $eid = intval($_GET['view']);
    $selected_elderly = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM elderly_members WHERE id=$eid"));
}

// Add Record
if (isset($_POST['add_record'])) {
    $eid = intval($_POST['elderly_id']);
    $doctor = mysqli_real_escape_string($conn, $_POST['doctor']);
    $diagnosis = mysqli_real_escape_string($conn, $_POST['diagnosis']);
    $meds = mysqli_real_escape_string($conn, $_POST['meds']);
    $date = $_POST['date'];
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);

    $sql = "INSERT INTO medical_records (elderly_id, doctor_name, diagnosis, medications, checkup_date, notes) 
            VALUES ($eid, '$doctor', '$diagnosis', '$meds', '$date', '$notes')";
    
    if (mysqli_query($conn, $sql)) {
        redirect("health_records.php?view=$eid&msg=added");
    } else {
        $error = mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Health Records - Staff</title>
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
            <li><a href="health_records.php" class="nav-link active"><i class="fas fa-heartbeat me-2"></i> Health Records</a></li>
        </ul>
    </div>

    <div class="flex-grow-1 p-4 bg-light">
        <?php if (!$selected_elderly): ?>
            <h2>Select Elderly Member</h2>
            <div class="card">
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Age / Gender</th>
                                <th>Room</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $res = mysqli_query($conn, "SELECT * FROM elderly_members");
                            while($row = mysqli_fetch_assoc($res)) {
                                $age = date_diff(date_create($row['dob']), date_create('today'))->y;
                                echo "<tr>";
                                echo "<td>{$row['name']}</td>";
                                echo "<td>{$age} / {$row['gender']}</td>";
                                echo "<td>{$row['room_no']}</td>";
                                echo "<td><a href='health_records.php?view={$row['id']}' class='btn btn-sm btn-primary'>View Records</a></td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Health Records for <?php echo $selected_elderly['name']; ?></h2>
                <a href="health_records.php" class="btn btn-secondary">Back to List</a>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-primary text-white">Add New Medical Record</div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="elderly_id" value="<?php echo $selected_elderly['id']; ?>">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Doctor Name</label>
                                <input type="text" name="doctor" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Checkup Date</label>
                                <input type="date" name="date" class="form-control" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label>Diagnosis</label>
                                <input type="text" name="diagnosis" class="form-control" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label>Medications</label>
                                <textarea name="meds" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label>Notes</label>
                                <textarea name="notes" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                        <button type="submit" name="add_record" class="btn btn-primary">Add Record</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">History</div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Doctor</th>
                                <th>Diagnosis</th>
                                <th>Medications</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $hist = mysqli_query($conn, "SELECT * FROM medical_records WHERE elderly_id={$selected_elderly['id']} ORDER BY checkup_date DESC");
                            while($h = mysqli_fetch_assoc($hist)) {
                                echo "<tr>";
                                echo "<td>{$h['checkup_date']}</td>";
                                echo "<td>{$h['doctor_name']}</td>";
                                echo "<td>{$h['diagnosis']}</td>";
                                echo "<td>{$h['medications']}</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
