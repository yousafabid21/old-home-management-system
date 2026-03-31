<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "old_home_db";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    // Try connecting without db selected to see if it's just the DB missing
    $conn_check = mysqli_connect($servername, $username, $password);
    if ($conn_check) {
        die("Connection successful but database '$dbname' not found. Please run setup.php first.");
    }
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset to utf8mb4
mysqli_set_charset($conn, "utf8mb4");

// Start Session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper function for redirection
function redirect($url) {
    echo "<script>window.location.href='$url';</script>";
    exit();
}

// Function to check login
function check_login() {
    if (!isset($_SESSION['user_id'])) {
        redirect('../login.php');
    }
}
?>
