<?php
require_once "../includes/admin_auth.php";

if (isset($_POST['admin_hospital'])) {

    $id = trim($_POST['id']);
    $edit_id = trim($_POST['edit_id'] ?? '');
    $name = trim($_POST['name']);
    $city = trim($_POST['city']);
    $state = trim($_POST['state']);
    $contact = trim($_POST['contact']);

    if (strlen($contact) < 10) {
        header("Location: ../complete_system.php?section=admin_hospital&msg=contact_error");
        exit;
    }

    if ($edit_id) {
        $stmt = $conn->prepare(
            "UPDATE hospitals SET id=?, name=?, city=?, state=?, contact=? WHERE id=?"
        );
        $stmt->bind_param("ssssss", $id, $name, $city, $state, $contact, $edit_id);
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO hospitals (id, name, city, state, contact) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("sssss", $id, $name, $city, $state, $contact);
    }

    $stmt->execute();
    header("Location: ../complete_system.php?section=admin_hospital&msg=success");
    exit;
}
