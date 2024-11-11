<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "del";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $conn;
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
