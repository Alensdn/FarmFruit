<?php
require "sqlcon2.php";
session_start();

// Sanitize user inputs to prevent XSS
$username = htmlspecialchars($_POST['userN']);
$email = htmlspecialchars($_POST['email']);
$firstname = htmlspecialchars($_POST['Fname']);
$lastname = htmlspecialchars($_POST['Lname']);
$baranggay = htmlspecialchars($_POST['brgy']);
$street = htmlspecialchars($_POST['street']);
$housenumber = htmlspecialchars($_POST['hnum']);
$Mnumber = htmlspecialchars($_POST['Mnum']);
$password = htmlspecialchars($_POST['pass']); // Sanitize password too

// Generate authentication code
$authCode = '';
for ($i = 0; $i < 6; $i++) {
    $authCode .= mt_rand(0, 9); // Concatenate random numbers to create a 6-digit code
}

// Hash the password
$hashpass = password_hash($password, PASSWORD_DEFAULT);

try {
    // Check if email or phone number already exists
    $sqlCheck = "SELECT COUNT(*) FROM users WHERE email = :email OR phone_number = :phone_number OR username = :username";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->execute([':email' => $email, ':phone_number' => $Mnumber, ':username' => $username]);
    $count = $stmtCheck->fetchColumn();

    if ($count > 0) {
        $_SESSION['error'] = "error"; 
        echo "<script> alert('Please try again The Email or Phone Number or Username you Provide already registered.'); 
        window.location.href = 'FarmLogin.php'; </script>";
        exit(); 
    }

    // SQL query with prepared statement
    $sql = "INSERT INTO users (user_type, username, password, email, fname, lname, barangay, house_number, street, phone_number) 
            VALUES (:user_type, :username, :password, :email, :fname, :lname, :barangay, :house_number, :street, :phone_number)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':user_type' => 'customer',
        ':username' => $username,
        ':password' => $hashpass,
        ':email' => $email,
        ':fname' => $firstname,
        ':lname' => $lastname,
        ':barangay' => $baranggay,
        ':house_number' => $housenumber,
        ':street' => $street,
        ':phone_number' => $Mnumber,
    ]);

    echo "<script> alert('account registered'); 
        window.location.href = 'FarmLogin.php'; </script>";
    exit();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$conn = null;
?>
