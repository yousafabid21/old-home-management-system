<?php
require_once 'config.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $phone = mysqli_real_escape_string($conn, $_POST['contact_no']);

    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Check if email exists
        $check_email = "SELECT id FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $check_email);
        if (mysqli_num_rows($result) > 0) {
            $error = "Email already registered!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (name, email, password, role, contact_no) VALUES ('$name', '$email', '$hashed_password', '$role', '$phone')";
            
            if (mysqli_query($conn, $sql)) {
                $success = "Registration successful! You can now <a href='login.php'>Login</a>";
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="auth-container">
    <h2 class="text-center mb-4">Create Account</h2>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Phone Number</label>
            <input type="text" name="contact_no" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Register As</label>
            <select name="role" class="form-select" required>
                <option value="family">Family Member</option>
                <option value="donor">Donor</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Register</button>
        <div class="text-center mt-3">
            <a href="login.php" class="text-decoration-none">Already have an account? Login</a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
