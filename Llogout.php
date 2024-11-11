<?php
require "FruitSQLcon.php";
session_start();

if (isset($_SESSION['U_id'])) {
    $userID = $_SESSION['U_id'];
    $action = 'Logout';

    try {
        // Fetch the user type from the database
        $user_query = "SELECT user_type FROM users WHERE user_id = :user_id";
        $stmt = $conn->prepare($user_query);
        $stmt->execute([':user_id' => $userID]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $userType = $result['user_type'];

            // Insert audit trail record for logout
            $audit_query = "INSERT INTO audit_trail (userID, action, userType) VALUES (:userID, :action, :userType)";
            $stmt = $conn->prepare($audit_query);
            $stmt->execute([':userID' => $userID, ':action' => $action, ':userType' => $userType]);
        }

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    session_destroy();
    header("Location: FarmLogin.php");
    exit();
}
?>
