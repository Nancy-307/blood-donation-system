<?php
session_start();

if (isset($_SESSION['user_type'])) {
    if ($_SESSION['user_type'] === 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: donor/profile.php");
    }
}
?>

<h2>Blood Donation System</h2>
<a href="auth/login.php">Login</a>
<a href="auth/register.php">Register</a>
