<?php
require_once 'config.php';

$error = "";

if (isset($_SESSION['user_id'])) {
    redirect($_SESSION['role'] . '/dashboard.php');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT id, name, password, role FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['role'] = $row['role'];
            
            // Redirect based on role
            redirect($row['role'] . '/dashboard.php');
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "User not found!";
    }
}

include 'includes/header.php';
?>

<div class="auth-container">
    <h2 class="text-center mb-4">Login</h2>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
        <div class="text-center mt-3">
            <a href="register.php" class="text-decoration-none">Create an account</a>
        </div>
    </form>

</div>

<?php include 'includes/footer.php'; ?>
