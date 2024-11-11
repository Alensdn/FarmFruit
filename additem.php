<?php
require "FruitSQLcon.php";
session_start();

$item_name = $_POST['Iname'];
$item_price = $_POST['Iprice'];

// Validate new price
if ($item_price <= 0) {
    echo "Invalid price. Please enter a positive number.";
    exit();
}

// Initialize variables for file upload
$uploadOk = 1;
$imageData = null;
$imageName = null;

if (isset($_FILES["fileToUpload"]) && $_FILES["fileToUpload"]["name"] != '') {
    $targetDirectory = "uploads/";
    $targetFile = $targetDirectory . basename($_FILES["fileToUpload"]["name"]);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    
    // Check if the file is an image
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if ($check === false) {
        echo "File is not an image.";
        $uploadOk = 0;
    }
    
    // Allow only certain file formats
    $allowedFileTypes = ["jpg", "jpeg", "png", "gif"];
    if (!in_array($imageFileType, $allowedFileTypes)) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }
    
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 1) {
        // Read the uploaded file
        $imageData = file_get_contents($_FILES["fileToUpload"]["tmp_name"]);
        $imageName = $_FILES["fileToUpload"]["name"];
    }
} else {
    $uploadOk = 0;
    echo "No image uploaded. Please upload an image.";
    exit();
}

if ($uploadOk == 1) {
    try {
        // Prepare SQL statement
        $sql = "INSERT INTO item (Item_name, Item_price, Item_image, Item_imageName) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        // Bind parameters
        $stmt->bindParam(1, $item_name);
        $stmt->bindParam(2, $item_price);
        $stmt->bindParam(3, $imageData, PDO::PARAM_LOB);
        $stmt->bindParam(4, $imageName);

        // Execute the statement
        if ($stmt->execute()) {
            $notify = 'Item Added Successfully.';
                echo "<script>alert('$notify'); window.location.href = 'adminitems.php'; </script>";
            exit();
        } else {
            echo "Error: Unable to execute statement.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

$conn = null;
?>
