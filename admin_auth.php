<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['logged_in']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../auth/auth.php");
    exit;
}

$is_admin = true;
?>
