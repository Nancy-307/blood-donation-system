<?php
require_once "../includes/admin_auth.php";

if (isset($_POST['admin_inventory'])) {

    $edit_id = intval($_POST['edit_id'] ?? 0);
    $hospital_id = $_POST['hospital_id'];
    $blood_group = $_POST['blood_group'];
    $units_available = intval($_POST['units_available']);

    if ($edit_id) {
        $stmt = $conn->prepare(
            "UPDATE blood_inventory SET hospital_id=?, blood_group=?, units_available=? WHERE id=?"
        );
        $stmt->bind_param("ssii", $hospital_id, $blood_group, $units_available, $edit_id);
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO blood_inventory (hospital_id, blood_group, units_available) VALUES (?, ?, ?)"
        );
        $stmt->bind_param("ssi", $hospital_id, $blood_group, $units_available);
    }

    $stmt->execute();
    header("Location: ../complete_system.php?section=admin_inventory&msg=success");
    exit;
}
