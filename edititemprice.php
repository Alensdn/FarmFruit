<?php
require "FruitSQLcon.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_id = $_POST["item_id"];
    $new_price = $_POST["new_price"];

    // Validate that the new price is a positive number
    if (!is_numeric($new_price) || $new_price <= 0) {
        echo "Invalid price. Please enter a positive number.";
        exit;
    }

    // Update the item price in the item table
    $sqlUpdatePrice = "UPDATE item SET Item_price = ? WHERE Item_ID = ?";
    $stmtUpdatePrice = $conn->prepare($sqlUpdatePrice);
    $stmtUpdatePrice->bind_param("di", $new_price, $item_id);
    
    if ($stmtUpdatePrice->execute()) {
        header("Location: adminitems.php");
        exit();
    } else {
        echo "Error updating item price: " . $conn->error;
    }

    $stmtUpdatePrice->close();
}

$conn->close();
?>
