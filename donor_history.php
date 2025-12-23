<?php
require_once "../includes/donor_auth.php";

/* ---------- DELETE ---------- */
if (isset($_GET['delete_donation'])) {

    $donation_id = intval($_GET['delete_donation']);

    $stmt = $conn->prepare(
        "DELETE FROM donations WHERE id=? AND donor_id=?"
    );
    $stmt->bind_param("ii", $donation_id, $logged_donor_id);
    $stmt->execute();

    header("Location: history.php?msg=del_success");
    exit;
}

/* ---------- UPDATE ---------- */
if (isset($_POST['update_donation'])) {

    $donation_id = intval($_POST['donation_id']);
    $hospital_id = $_POST['hospital_id'];
    $units_donated = intval($_POST['units_donated']);
    $donation_date = $_POST['donation_date'];
    $new_age = intval($_POST['age']);

    if ($new_age < 18) {
        header("Location: history.php?msg=age_error");
        exit;
    }

    if (strtotime($donation_date) < strtotime(date('Y-m-d'))) {
        header("Location: history.php?msg=date_error");
        exit;
    }

    $conn->begin_transaction();
    try {
        // Fetch original blood group
        $stmt_bg = $conn->prepare(
            "SELECT blood_group FROM donations WHERE id=? AND donor_id=?"
        );
        $stmt_bg->bind_param("ii", $donation_id, $logged_donor_id);
        $stmt_bg->execute();
        $blood_group = $stmt_bg->get_result()->fetch_assoc()['blood_group'];

        // Update donation
        $stmt = $conn->prepare(
            "UPDATE donations
             SET hospital_id=?, blood_group=?, units_donated=?, donation_date=?
             WHERE id=? AND donor_id=?"
        );
        $stmt->bind_param(
            "ssiisi",
            $hospital_id, $blood_group, $units_donated, $donation_date,
            $donation_id, $logged_donor_id
        );
        $stmt->execute();

        // Update donor age
        $stmt_age = $conn->prepare(
            "UPDATE donors SET age=? WHERE id=?"
        );
        $stmt_age->bind_param("ii", $new_age, $logged_donor_id);
        $stmt_age->execute();

        $conn->commit();
        header("Location: history.php?msg=upd_success");
        exit;

    } catch (mysqli_sql_exception $e) {
        $conn->rollback();
        header("Location: history.php?msg=upd_error");
        exit;
    }
}
