<?php
session_start();
require_once "../config/db.php";

$login_error = $register_error = "";

/* ================= REGISTER ================= */
if (isset($_POST['register'])) {

    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $email    = trim($_POST['email']);

    if (strlen($username) < 3 || strlen($password) < 4) {
        $register_error = "Username 3+ chars & Password 4+ chars required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $register_error = "Invalid email";
    } else {

        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $conn->begin_transaction();

            // Insert user
            $stmt = $conn->prepare(
                "INSERT INTO users (username, password_hash, email) VALUES (?, ?, ?)"
            );
            $stmt->bind_param("sss", $username, $password_hash, $email);
            $stmt->execute();

            $user_id = $conn->insert_id;

            // Create donor profile
            $stmt = $conn->prepare(
                "INSERT INTO donors (name, age, gender, blood_group, contact, address, user_id)
                 VALUES ('New Donor', 0, 'Other', 'Unknown', '', '', ?)"
            );
            $stmt->bind_param("i", $user_id);
            $stmt->execute();

            $conn->commit();
            header("Location: auth.php?success=registered");
            exit;

        } catch (mysqli_sql_exception $e) {
            $conn->rollback();
            $register_error = "Username already exists";
        }
    }
}

/* ================= LOGIN ================= */
if (isset($_POST['login'])) {

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Hardcoded Admin
    if ($username === "admin" && $password === "1234") {
        $_SESSION['logged_in'] = true;
        $_SESSION['user_type'] = "admin";
        header("Location: ../admin/dashboard.php");
        exit;
    }

    // Normal user
    $stmt = $conn->prepare(
        "SELECT u.id, u.password_hash, d.id AS donor_id
         FROM users u
         LEFT JOIN donors d ON u.id = d.user_id
         WHERE u.username = ?"
    );
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['logged_in'] = true;
        $_SESSION['user_type'] = "donor";
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['donor_id']  = $user['donor_id'];

        header("Location: ../donor/profile.php");
        exit;
    } else {
        $login_error = "Invalid username or password";
    }
}

/* ================= LOGOUT ================= */
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: auth.php");
    exit;
}
?>

<!-- ================= SIMPLE UI ================= -->

<h2>Blood Donation System</h2>

<?php if ($register_error) echo "<p style='color:red'>$register_error</p>"; ?>
<?php if ($login_error) echo "<p style='color:red'>$login_error</p>"; ?>

<h3>Login</h3>
<form method="post">
    <input type="text" name="username" placeholder="Username" required><br><br>
    <input type="password" name="password" placeholder="Password" required><br><br>
    <button name="login">Login</button>
</form>

<hr>

<h3>Register</h3>
<form method="post">
    <input type="text" name="username" placeholder="Username" required><br><br>
    <input type="email" name="email" placeholder="Email" required><br><br>
    <input type="password" name="password" placeholder="Password" required><br><br>
    <button name="register">Register</button>
</form>
