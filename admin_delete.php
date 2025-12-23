<?php
require_once "../includes/admin_auth.php";

if (isset($_GET['delete'], $_GET['table'])) {

    $id = $_GET['delete'];
    $table = preg_replace('/[^a-z_]/', '', $_GET['table']);

    $stmt = $conn->prepare("DELETE FROM `$table` WHERE id=?");
    $stmt->bind_param("s", $id);
    $stmt->execute();

    $section = $_GET['section'] ?? '';
    header("Location: ../complete_system.php?section=$section");
    exit;
}
