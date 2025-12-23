<?php
require_once "../includes/donor_auth.php";

if (isset($_POST['update_profile'])) {

    $name = $_POST['name'];
    $age = intval($_POST['age']);
    $gender = $_POST['gender'];
    $blood_group = $_POST['blood_group'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];

    if ($age < 18) {
        header("Location: profile.php?msg=age_error");
        exit;
    }

    if (strlen($contact) < 10) {
        header("Location: profile.php?msg=contact_error");
        exit;
    }

    $stmt = $conn->prepare(
        "UPDATE donors SET name=?, age=?, gender=?, blood_group=?, contact=?, address=? WHERE id=?"
    );
    $stmt->bind_param(
        "sissssi",
        $name, $age, $gender, $blood_group, $contact, $address, $logged_donor_id
    );
    $stmt->execute();

    header("Location: profile.php?msg=success");
    exit;
}
