<?php
require_once '../config.php';
check_login();

if ($_SESSION['role'] != 'donor') {
    redirect('../login.php');
}

// Handle New Donation
if (isset($_POST['donate'])) {
    $amount = $_POST['amount'];
    $method = $_POST['method'];
    $msg = mysqli_real_escape_string($conn, $_POST['message']);
    $uid = $_SESSION['user_id'];
    
    // Status 'Completed' for demo purposes
    $sql = "INSERT INTO donations (donor_id, amount, payment_method, message, status) 
            VALUES ($uid, '$amount', '$method', '$msg', 'Completed')";
    
    if (mysqli_query($conn, $sql)) {
        $success_msg = "Thank you for your generous donation!";
    } else {
        $error_msg = "Error: " . mysqli_error($conn);
    }
}

$uid = $_SESSION['user_id'];
$total_donated = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as s FROM donations WHERE donor_id=$uid"))['s'] ?? 0;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Donor Dashboard - Old Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark bg-success mb-4">
    <div class="container">
        <a class="navbar-brand" href="#">Donor Portal</a>
        <div class="d-flex">
            <span class="navbar-text text-white me-3">Welcome, <?php echo $_SESSION['name']; ?></span>
            <a href="../logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-warning text-dark h-100">
                <div class="card-body text-center">
                    <h3>Total Contributed</h3>
                    <h1 class="display-4 fw-bold">$<?php echo number_format($total_donated, 2); ?></h1>
                    <p>Thank you for making a difference!</p>
                </div>
            </div>
        </div>
        <div class="col-md-8">
             <div class="card h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <h3>Make a New Donation</h3>
                    <p class="text-muted">Your support helps us provide better care.</p>
                    <button class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#donateModal">
                        <i class="fas fa-heart me-2"></i> Donate Now
                    </button>
                    <?php if (isset($success_msg)) echo "<div class='alert alert-success mt-3'>$success_msg</div>"; ?>
                </div>
             </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Donation History</div>
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $history = mysqli_query($conn, "SELECT * FROM donations WHERE donor_id=$uid ORDER BY donation_date DESC");
                    while($row = mysqli_fetch_assoc($history)) {
                        echo "<tr>";
                        echo "<td>" . date('M d, Y', strtotime($row['donation_date'])) . "</td>";
                        echo "<td>$" . number_format($row['amount'], 2) . "</td>";
                        echo "<td>{$row['payment_method']}</td>";
                        echo "<td>{$row['message']}</td>";
                        echo "<td><span class='badge bg-success'>{$row['status']}</span></td>";
                        echo "<td><button class='btn btn-sm btn-outline-secondary'><i class='fas fa-print'></i> Print</button></td>"; // Placeholder
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="donateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Make a Donation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Amount ($)</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Payment Method</label>
                        <select name="method" class="form-select">
                            <option>Credit Card</option>
                            <option>PayPal</option>
                            <option>Bank Transfer</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Message (Optional)</label>
                        <textarea name="message" class="form-control" placeholder="Leave a message of support..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="donate" class="btn btn-success">Process Donation</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
