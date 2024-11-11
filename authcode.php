<?php
session_start();
$email = $_SESSION['email'];

if (!$email) {
    echo "No email in session.";
    exit();
}

// Generate a 6-digit numeric token
$token = random_int(100000, 999999);
$expiry = date("Y-m-d H:i:s", time() + 60 * 30); // Token valid for 30 minutes

$conn = require __DIR__ . "/sqlcon3.php";

$sql = "UPDATE users SET reset_token_hash = ?, reset_token_expires_at = ? WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $token, $expiry, $email);
$stmt->execute();

if ($stmt->affected_rows) {
    $mail = require __DIR__ . "/mailer.php";

    $mail->setFrom("noreply@example.com", "Your Website");
    $mail->addAddress($email);
    $mail->Subject = "Authentication Code";
    $mail->Body = <<<END
Enter this Code: $token.
END;

    try {
        $mail->send();
        echo "<script>
                alert('Code sent, please check your inbox.');
                window.location.href = 'authpage.php';
              </script>";
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
    }
} else {
    echo "Failed to update token.";
}

$conn->close();
?>
