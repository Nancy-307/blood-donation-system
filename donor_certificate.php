// ---------------- CERTIFICATE DATA FETCHING ----------------
$latest_donation_date = null;
$donor_name = 'Valued Donor'; // Default
$hospital_name = 'Blood Donation Center'; // Default hospital name
if ($is_donor) {
    // Fetch the latest donation date and hospital for the current user
    $stmt = $conn->prepare("SELECT d.donation_date, h.name as hospital_name 
                           FROM donations d 
                           LEFT JOIN hospitals h ON d.hospital_id = h.id 
                           WHERE d.donor_id = ? 
                           ORDER BY d.donation_date DESC LIMIT 1");
    $stmt->bind_param("i", $logged_donor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $donation_data = $result->fetch_assoc();
        $latest_donation_date = $donation_data['donation_date'];
        $hospital_name = $donation_data['hospital_name'] ?? 'Blood Donation Center';
        // Format the date to a more readable style (e.g., May 05, 2025)
        $latest_donation_date = date('F d, Y', strtotime($latest_donation_date));
    }
    $stmt->close();

    // Fetch Donor Name for Certificate/Profile
    $stmt_d = $conn->prepare("SELECT name, blood_group FROM donors WHERE id = ?");
    $stmt_d->bind_param("i", $logged_donor_id);
    $stmt_d->execute();
    $result_d = $stmt_d->get_result();
    if ($result_d->num_rows > 0) {
        $donor_data = $result_d->fetch_assoc();
        $donor_name = $donor_data['name'];
    }
    $stmt_d->close();
}
// ---------------- END CERTIFICATE DATA FETCHING ----------------


?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>🩸 Blood Donation Management</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Playfair+Display:ital,wght@1,700&display=swap');
    
/* ORIGINAL DARK THEME */
body {
    font-family: "Poppins", sans-serif;
    /* Placeholder for your background image */
    background: url('blood-donation-182573750.webp') no-repeat center center fixed; 
    background-size: cover;
    color: #fff;
    margin: 0;
}
.container { width: 92%; margin: auto; text-align: center; padding: 30px 0 60px 0; }
.header { display:flex; align-items:center; gap:12px; padding: 10px 40px; background: rgba(0,0,0,0.60); }
.header h1 { margin:0; font-size:32px; color:#fff; }

/* MODIFIED STYLES (CENTERED BOXES) */
.boxes { 
    display:flex; 
    gap: 30px; 
    justify-content: center; 
    padding: 30px 40px; 
    flex-wrap: wrap; 
}
.box {
    width: 220px; height: 120px; border-radius: 14px;
    background: rgba(0,0,0,0.60);
    color: #fff; display:flex; align-items:center; justify-content:center;
    box-shadow: 0 10px 30px rgba(0,0,0,0.4); cursor:pointer; border:1px solid rgba(255,255,255,0.07);
    text-align: center; 
    padding: 10px; 
}
.box:hover{ transform:translateY(-6px); transition:0.18s; background:rgba(0,0,0,0.75);}

.glass { background: rgba(0,0,0,0.75); border-radius: 12px; margin: 20px auto; padding: 26px; width: 85%; color:#fff; }
.hidden { display:none; }
input, select, textarea { width:70%; padding:12px; margin:10px 0; border-radius:8px; border:none; background: rgba(255,255,255,0.06); color:#fff; }
button { padding:10px 18px; border-radius:8px; border:none; background:#e43; color:#fff; cursor:pointer; }
table { width:100%; border-collapse:collapse; margin-top:18px; background: rgba(0,0,0,0.86); border-radius:8px; overflow:hidden; }
th,td { padding:12px 10px; border-bottom:1px solid rgba(255,255,255,0.06); color:#fff; text-align:center; }
th { background: rgba(255,255,255,0.06); font-weight:600; }
.actions a { color:#ffd; margin:0 6px; text-decoration:none; }
.small { width: 120px; }
.auth-link { color:#ccc; text-decoration:none; margin: 0 10px; font-size:14px; }
.auth-link:hover { color: #e43; }
.nav-bar { padding: 10px 40px; background: rgba(0,0,0,0.8); text-align: left; }
.nav-bar a { color: white; padding: 8px 15px; text-decoration: none; display: inline-block; transition: background 0.3s; border-radius: 4px; }
.nav-bar a.active { background: #e43; }


/* --------------------------------- ENHANCED CERTIFICATE STYLES WITH BLOOD THEME --------------------------------- */
.certificate-page {
    max-width: 900px;
    height: 650px;
    margin: 40px auto;
    background: linear-gradient(135deg, #8B0000 0%, #B22222 50%, #DC143C 100%);
    color: #fff;
    padding: 40px;
    box-shadow: 0 0 40px rgba(139, 0, 0, 0.7);
    border: 15px double #FFD700;
    border-radius: 15px;
    font-family: 'Poppins', sans-serif;
    position: relative;
    overflow: hidden;
}

/* Gold decorative corners */
.certificate-page::before {
    content: '';
    position: absolute;
    top: 20px;
    left: 20px;
    right: 20px;
    bottom: 20px;
    border: 2px solid rgba(255, 215, 0, 0.3);
    border-radius: 8px;
    pointer-events: none;
}

/* Blood drop pattern background */
.certificate-page::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: 
        radial-gradient(circle at 20% 30%, rgba(255, 255, 255, 0.1) 2%, transparent 2.5%),
        radial-gradient(circle at 80% 70%, rgba(255, 255, 255, 0.1) 2%, transparent 2.5%),
        radial-gradient(circle at 40% 80%, rgba(255, 255, 255, 0.1) 1.5%, transparent 2%),
        radial-gradient(circle at 60% 20%, rgba(255, 255, 255, 0.1) 1.5%, transparent 2%);
    background-size: 200px 200px;
    opacity: 0.4;
    z-index: 0;
}

.cert-content {
    position: relative;
    z-index: 1;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.cert-header {
    text-align: center;
    border-bottom: 3px solid #FFD700;
    padding-bottom: 20px;
    margin-bottom: 25px;
    position: relative;
}

.cert-header h2 {
    color: #FFD700;
    font-size: 32px;
    margin: 0 0 15px 0;
    font-weight: 700;
    text-transform: uppercase;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    letter-spacing: 2px;
}

.cert-address {
    text-align: center;
    font-size: 16px;
    color: #FFB6C1;
    margin: 8px 0;
    line-height: 1.4;
    font-weight: 500;
}

.cert-phone {
    text-align: center;
    font-size: 16px;
    color: #FFB6C1;
    margin: 8px 0 25px 0;
    font-weight: 500;
}

.cert-title {
    font-size: 36px;
    font-weight: 700;
    text-align: center;
    margin: 35px 0;
    color: #FFD700;
    text-decoration: none;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    position: relative;
}

.cert-title::before, .cert-title::after {
    content: '❦';
    color: #FFD700;
    margin: 0 25px;
    font-size: 28px;
}

.cert-body {
    font-size: 20px;
    line-height: 2.2;
    text-align: center;
    color: #fff;
    margin: 45px 0;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
}

.cert-name {
    font-size: 26px;
    font-weight: 700;
    color: #FFD700;
    border-bottom: 3px solid #FFD700;
    padding: 0 25px;
    margin: 0 15px;
    display: inline-block;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    background: linear-gradient(135deg, rgba(139, 0, 0, 0.3), rgba(178, 34, 34, 0.3));
    border-radius: 8px;
}

.cert-date {
    font-size: 22px;
    font-weight: 600;
    color: #FFD700;
    border-bottom: 2px solid #FFD700;
    padding: 0 20px;
    margin: 0 10px;
    display: inline-block;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
    background: rgba(139, 0, 0, 0.3);
    border-radius: 6px;
}

.cert-appreciation {
    font-size: 18px;
    text-align: center;
    color: #FFB6C1;
    margin: 35px 0;
    font-style: italic;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
    background: rgba(139, 0, 0, 0.4);
    padding: 15px;
    border-radius: 10px;
    border-left: 4px solid #FFD700;
    border-right: 4px solid #FFD700;
}

.cert-signature {
    text-align: right;
    margin-top: 50px;
    padding-top: 25px;
    border-top: 2px solid #FFD700;
    position: relative;
}

.cert-officer {
    font-size: 18px;
    font-weight: 600;
    color: #FFD700;
    margin: 12px 0 6px 0;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
}

.cert-blood-bank {
    font-size: 18px;
    font-weight: 700;
    color: #FFD700;
    text-transform: uppercase;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
}

/* Red ribbon effect */
.cert-signature::before {
    content: '';
    position: absolute;
    top: -2px;
    left: 25%;
    right: 25%;
    height: 4px;
    background: linear-gradient(90deg, transparent, #FFD700, transparent);
}

/* Golden seal effect */
.cert-content::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 200px;
    height: 200px;
    background: radial-gradient(circle, rgba(255, 215, 0, 0.15) 0%, rgba(255, 215, 0, 0) 70%);
    transform: translate(-50%, -50%);
    border-radius: 50%;
    z-index: -1;
}

/* Forgot Password Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.7);
}

.modal-content {
    background: rgba(0,0,0,0.85);
    margin: 10% auto;
    padding: 30px;
    border-radius: 12px;
    width: 80%;
    max-width: 500px;
    border: 2px solid #e43;
    box-shadow: 0 0 30px rgba(228, 67, 67, 0.3);
}

.close {
    color: #fff;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover {
    color: #e43;
}

.security-note {
    background: rgba(228, 67, 67, 0.2);
    border-left: 4px solid #e43;
    padding: 15px;
    margin: 15px 0;
    border-radius: 8px;
    font-size: 14px;
    line-height: 1.5;
}

.security-note ul {
    text-align: left;
    margin: 10px 0;
    padding-left: 20px;
}

.security-note li {
    margin: 8px 0;
}
</style>
</head>
<body>

