<?php
require "FruitSQLcon.php";
session_start();
if (isset($_SESSION['U_id'])) {
    $userID = $_SESSION['U_id'];
    $action = isset($_SESSION['action']) ? $_SESSION['action'] : '';

    // Fetch the user type from the database using PDO
    $user_query = "SELECT user_type FROM users WHERE user_id = :user_id";
    $stmt = $conn->prepare($user_query);
    $stmt->execute([':user_id' => $userID]);
    
    if ($stmt && $stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $userType = $row['user_type'];
        
        // Insert audit trail record for the action using PDO
        $audit_query = "INSERT INTO audit_trail (userID, action, userType) VALUES (:user_id, :action, :user_type)";
        $audit_stmt = $conn->prepare($audit_query);
        $audit_stmt->execute([
            ':user_id' => $userID,
            ':action' => $action,
            ':user_type' => $userType
        ]);
    }

    if (isset($_SESSION['location'])) {
        $location = $_SESSION['location'];
        header("Location: $location");
        exit();
    }
}
?>


