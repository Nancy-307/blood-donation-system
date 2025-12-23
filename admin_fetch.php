<?php
require_once "../includes/admin_auth.php";

$editData = null;
$editTable = null;
$autoSection = '';

if (isset($_GET['edit'], $_GET['table'])) {

    $id = $_GET['edit'];
    $table = preg_replace('/[^a-z_]/', '', $_GET['table']);

    $stmt = $conn->prepare("SELECT * FROM `$table` WHERE id=?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res && $res->num_rows > 0) {
        $editData = $res->fetch_assoc();
        $editTable = $table;

        $map = [
            'requests' => 'admin_request',
            'hospitals' => 'admin_hospital',
            'blood_inventory' => 'admin_inventory',
            'donations' => 'admin_donation'
        ];

        if (isset($map[$table])) {
            header("Location: ../complete_system.php?section={$map[$table]}&edit=1");
            exit;
        }
    }
}
