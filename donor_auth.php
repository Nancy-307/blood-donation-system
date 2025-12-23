<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['logged_in']) || $_SESSION['user_type'] !== 'donor') {
    header("Location: ../auth/auth.php");
    exit;
}

$logged_donor_id = $_SESSION['donor_id'];
?>
