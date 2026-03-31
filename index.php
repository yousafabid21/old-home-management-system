<?php
require_once 'config.php';
include 'includes/header.php';
?>

<!-- Hero Section -->
<div class="hero-section text-center">
    <div class="container">
        <h1 class="display-4 fw-bold">Compassionate Elderly Care</h1>
        <p class="lead mb-4">Streamlining care, connecting families, and supporting our elderly loved ones.</p>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="register.php" class="btn btn-primary btn-lg me-2">Get Started</a>
            <a href="login.php" class="btn btn-outline-light btn-lg">Login</a>
        <?php else: ?>
            <a href="<?php echo $_SESSION['role']; ?>/dashboard.php" class="btn btn-primary btn-lg">Go to Dashboard</a>
        <?php endif; ?>
    </div>
</div>

<!-- Features Section -->
<div class="container py-5">
    <div class="row text-center mb-5">
        <div class="col-lg-8 mx-auto">
            <h2 class="fw-bold">Our Features</h2>
            <p class="text-muted">Designed to serve everyone involved in the care process.</p>
        </div>
    </div>
    <div class="row g-4">
        <!-- Family Portal -->
        <div class="col-md-4">
            <div class="card h-100 p-4">
                <div class="card-body">
                    <h3 class="card-title fw-bold text-primary">Family Portal</h3>
                    <p class="card-text">Stay connected with your loved ones. Monitor their health, viewing daily routines, and receive updates remotely.</p>
                </div>
            </div>
        </div>
        <!-- Care Management -->
        <div class="col-md-4">
            <div class="card h-100 p-4">
                <div class="card-body">
                    <h3 class="card-title fw-bold text-success">Care Management</h3>
                    <p class="card-text">Efficient tools for staff to manage schedules, medical records, and daily activities seamlessly.</p>
                </div>
            </div>
        </div>
        <!-- Donations -->
        <div class="col-md-4">
            <div class="card h-100 p-4">
                <div class="card-body">
                    <h3 class="card-title fw-bold text-warning">Donations</h3>
                    <p class="card-text">Transparent donation tracking system allowing supporters to contribute to the well-being of the elderly.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
