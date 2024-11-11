<?php
require "FruitSQLcon.php";
// Assuming the form data has been posted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usertype = $_POST['position'];
    $username = $_POST['Uname'];
    $password = $_POST['Pass'];
    $email = $_POST['Email'];
    $firstname = $_POST['Fname'];
    $lastname = $_POST['Lname'];
    $phonenumber = $_POST['Pnum'];

    // Hash the password
    $hash1pass = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO users (user_type, username, password, email, fname, lname, phone_number) 
                                VALUES (:usertype, :username, :password, :email, :firstname, :lastname, :phonenumber)");

        // Bind parameters
        $stmt->bindParam(':usertype', $usertype);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hash1pass);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':firstname', $firstname);
        $stmt->bindParam(':lastname', $lastname);
        $stmt->bindParam(':phonenumber', $phonenumber);

        // Execute the statement
        if ($stmt->execute()) {
            $lastInsertedId = $conn->lastInsertId();
            $id = $lastInsertedId;
            $action = 'Account Created';
            $user_query = "SELECT user_type FROM users WHERE user_id = :user_id";
            $stmt = $conn->prepare($user_query);
            $stmt->execute([':user_id' => $id]);
            if ($stmt && $stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $userType = $row['user_type'];
                
                // Insert audit trail record for the action using PDO
                $audit_query = "INSERT INTO audit_trail (userID, action, userType) VALUES (:user_id, :action, :user_type)";
                $audit_stmt = $conn->prepare($audit_query);
                $audit_stmt->execute([
                    ':user_id' => $id ,
                    ':action' => $action,
                    ':user_type' => $userType
                ]);
            }
            $notify = 'Created Successfully.';
            echo "<script> alert('$notify'); window.location.href = 'adminusers.php'; </script>";
            exit();
        } else {
            echo "Error executing the query.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
// Close the connection
$conn = null;
?>
