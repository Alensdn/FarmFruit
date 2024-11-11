<?php
require "sqlcon2.php";
session_start();

$user_id = $_SESSION["U_id"];

if (isset($_POST['update_cred'])) {
    // Collect form data
    $currentPassword = $_POST["current_password"];
    $newPassword = password_hash($_POST["password"], PASSWORD_DEFAULT); // Hash the password for security

    try {
        // Check if the current password matches the one in the database
        $sql = "SELECT password FROM users WHERE user_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$user_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $hashedPassword = $row['password'];

        if (password_verify($currentPassword, $hashedPassword)) {
            // Update the username and password in the database using prepared statements
            $sql = "UPDATE users SET password=? WHERE user_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$newPassword, $user_id]);

            $_SESSION['action'] = 'Customer changed username and password';
            $_SESSION['location'] = 'myprofile.php';
            $notify = 'Changed password successfully.';

            // Redirect with alert
            echo "<script>
                alert('$notify');
                window.location.href = 'Ltrail.php';
            </script>";
            exit();
        } else {
            $notify = 'Current password is incorrect.';
            echo "<script> alert('$notify');
            window.location.href = 'myprofile.php';
            </script>";
            exit();
        }
    } catch (PDOException $e) {
        echo "Error updating username and password: " . $e->getMessage();
    }
}
?>
