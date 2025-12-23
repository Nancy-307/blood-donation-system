<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "blood_donation_system";
$port = 3306;

$conn = new mysqli($servername, $username, $password, "", $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->query("CREATE DATABASE IF NOT EXISTS $dbname");
$conn->select_db($dbname);
?>
