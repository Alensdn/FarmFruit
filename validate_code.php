<?php
session_start();
$email = $_SESSION['email'];

if (!$email) {
    echo "No email in session.";
    exit();
}

$code = $_POST['code'];

$conn = require __DIR__ . "/sqlcon3.php";

$sql = "SELECT reset_token_hash, reset_token_expires_at FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($stored_token, $expires_at);
$stmt->fetch();

if ($stored_token && $stored_token === $code && new DateTime($expires_at) > new DateTime()) {
    // Token is valid
    echo "<script>
            alert('Code validated successfully. Welcome, you are logged in.');
            window.location.href = 'Ltrail.php';
          </script>";
} else {
    // Token is invalid or expired
    echo "<script>
            alert('Invalid or expired code.');
            window.location.href = 'authpage.php';
          </script>";
}

$stmt->close();
$conn->close();
?>
