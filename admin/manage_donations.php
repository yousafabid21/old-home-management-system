<?php
require_once '../config.php';
check_login();

if ($_SESSION['role'] != 'admin') {
    redirect('../login.php');
}

// Handle Add Donation
if (isset($_POST['add_donation'])) {
    $donor_id = $_POST['donor_id'];
    $amount = $_POST['amount'];
    $method = $_POST['method'];
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    $sql = "INSERT INTO donations (donor_id, amount, payment_method, message, status) VALUES ('$donor_id', '$amount', '$method', '$message', 'Completed')";
    if (mysqli_query($conn, $sql)) {
        $msg = "Donation recorded successfully";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Donations - Admin</title>
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
            <li><a href="manage_appointments.php" class="nav-link text-white"><i class="fas fa-calendar-check me-2"></i> Appointments</a></li>
            <li><a href="manage_donations.php" class="nav-link active"><i class="fas fa-hand-holding-usd me-2"></i> Donations</a></li>
             <li><a href="reports.php" class="nav-link text-white"><i class="fas fa-chart-bar me-2"></i> Reports</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="flex-grow-1 p-4 bg-light">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Donation Records</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDonationModal">
                <i class="fas fa-plus"></i> Record Donation
            </button>
        </div>

        <?php if (isset($msg)) echo "<div class='alert alert-success'>$msg</div>"; ?>
        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

        <div class="card">
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Donor Name</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Date</th>
                            <th>Message</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT d.*, u.name as donor_name 
                                  FROM donations d 
                                  JOIN users u ON d.donor_id = u.id 
                                  ORDER BY d.donation_date DESC";
                        $result = mysqli_query($conn, $query);
                        
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>#{$row['id']}</td>";
                            echo "<td>{$row['donor_name']}</td>";
                            echo "<td>$" . number_format($row['amount'], 2) . "</td>";
                            echo "<td>{$row['payment_method']}</td>";
                            echo "<td>" . date('M d, Y', strtotime($row['donation_date'])) . "</td>";
                            echo "<td>{$row['message']}</td>";
                            echo "<td><span class='badge bg-success'>{$row['status']}</span></td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Donation Modal -->
<div class="modal fade" id="addDonationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Record New Donation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Donor</label>
                        <select name="donor_id" class="form-select" required>
                            <option value="">Select Donor...</option>
                            <?php
                            $donors = mysqli_query($conn, "SELECT id, name FROM users WHERE role='donor'");
                            while($d = mysqli_fetch_assoc($donors)) {
                                echo "<option value='{$d['id']}'>{$d['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Amount ($)</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Payment Method</label>
                        <select name="method" class="form-select">
                            <option>Cash</option>
                            <option>Bank Transfer</option>
                            <option>Cheque</option>
                            <option>Online</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Message (Optional)</label>
                        <textarea name="message" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_donation" class="btn btn-primary">Save Record</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
