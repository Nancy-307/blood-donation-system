<?php
require_once "../includes/admin_auth.php";

if (isset($_POST['admin_request'])) {

    $edit_id = intval($_POST['edit_id'] ?? 0);
    $patient_name = trim($_POST['patient_name']);
    $blood_group = $_POST['blood_group'];
    $units_required = intval($_POST['units_required']);
    $hospital = trim($_POST['hospital']);
    $contact = trim($_POST['contact']);
    $request_date = $_POST['request_date'];

    if (strtotime($request_date) < strtotime(date('Y-m-d')) || strlen($contact) < 10) {
        header("Location: ../complete_system.php?section=admin_request&msg=error");
        exit;
    }

    if ($edit_id) {
        $stmt = $conn->prepare(
            "UPDATE requests SET patient_name=?, blood_group=?, units_required=?, hospital=?, contact=?, request_date=? WHERE id=?"
        );
        $stmt->bind_param("ssisssi", $patient_name, $blood_group, $units_required, $hospital, $contact, $request_date, $edit_id);
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO requests (patient_name, blood_group, units_required, hospital, contact, request_date)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("ssisss", $patient_name, $blood_group, $units_required, $hospital, $contact, $request_date);
    }

    $stmt->execute();
    header("Location: ../complete_system.php?section=admin_request&msg=success");
    exit;
}
