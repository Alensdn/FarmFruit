<?php
    // client privileges
   $servername = "localhost";
   $username = "root";
   $password = "";
   $dbname = "del";
   try {
      // Establish database connection using PDO
      $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
      // Set the PDO error mode to exception
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch(PDOException $e) {
      die("Connection failed: " . $e->getMessage());
  }