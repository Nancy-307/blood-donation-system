<?php
require_once "../includes/donor_auth.php";

if (isset($_POST['submit_donation'])) {

    $hospital_id = $_POST['hospital_id'];
    $blood_group = $_POST['blood_group'];
    $units_donated = intval($_POST['units_donated']);
    $donation_date = $_POST['donation_date'];

    if (strtotime($donation_date) < strtotime(date('Y-m-d'))) {
        header("Location: donate.php?msg=date_error");
        exit;
    }

    $conn->begin_transaction();
    try {
        // Insert donation
        $stmt = $conn->prepare(
            "INSERT INTO donations (donor_id, hospital_id, blood_group, units_donated, donation_date)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "issis",
            $logged_donor_id, $hospital_id, $blood_group, $units_donated, $donation_date
        );
        $stmt->execute();

        // Update inventory
        $stmt_inv = $conn->prepare(
            "INSERT INTO blood_inventory (hospital_id, blood_group, units_available)
             VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE units_available = units_available + ?"
        );
        $stmt_inv->bind_param(
            "ssii",
            $hospital_id, $blood_group, $units_donated, $units_donated
        );
        $stmt_inv->execute();

        $conn->commit();
        header("Location: history.php?msg=don_success");
        exit;

    } catch (mysqli_sql_exception $e) {
        $conn->rollback();
        header("Location: donate.php?msg=don_error");
        exit;
    }
}
