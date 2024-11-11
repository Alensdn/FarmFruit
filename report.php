<?php
// Database connection parameters
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'farmfruit';

try {
    // Establish database connection using PDO
    $conn = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>