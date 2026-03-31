<?php
require_once '../config.php';
check_login();

if ($_SESSION['role'] != 'family') {
    redirect('../login.php');
}

// Handler
if (isset($_POST['register_elderly'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $history = mysqli_real_escape_string($conn, $_POST['history']);
    $uid = $_SESSION['user_id'];
    
    // Admission date = today, room = Pending
    $admission = date('Y-m-d');
    
    $sql = "INSERT INTO elderly_members (name, dob, gender, admission_date, medical_history, family_member_id, room_no) 
            VALUES ('$name', '$dob', '$gender', '$admission', '$history', $uid, 'Pending')";
    
    if (mysqli_query($conn, $sql)) {
        redirect('dashboard.php');
    } else {
        $error = mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Elderly Relative - Old Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">Family Portal</a>
    </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">Register Elderly Relative</div>
                <div class="card-body">
                    <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label>Full Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Date of Birth</label>
                                <input type="date" name="dob" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Gender</label>
                                <select name="gender" class="form-select" required>
                                    <option>Male</option>
                                    <option>Female</option>
                                    <option>Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Medical History / Special Needs</label>
                            <textarea name="history" class="form-control" rows="4"></textarea>
                        </div>
                        
                        <div class="alert alert-info">
                            <small>By registering, you are submitting a request for admission. Room allocation will be handled by the administration.</small>
                        </div>
                        
                        <button type="submit" name="register_elderly" class="btn btn-primary w-100">Register</button>
                    </form>
                    <div class="text-center mt-3">
                        <a href="dashboard.php">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
