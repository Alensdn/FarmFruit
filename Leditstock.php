<?php
require "FruitSQLcon.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $box_name = $_POST["box_name"];
    $edit_quantity = $_POST["edit_quantity"];
    $operation = $_POST["operation"];

    // Fetch the current stock of the box and associated item ID
    $sql = "SELECT remaining_quantity, id, quantity FROM item_history WHERE boxname = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$box_name]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    // Close the statement
    unset($stmt);

    if ($row !== false) {
        $current_stock = $row["remaining_quantity"];
        $item_id = $row["id"];
        $quantity = $row["quantity"];

        if ($operation == "add") {
            // Update the stock in the `item_history` table
            $new_stock = $current_stock + $edit_quantity;
        } elseif ($operation == "subtract" && $edit_quantity <= $current_stock) {
            $new_stock = $current_stock - $edit_quantity;
            // Subtract the quantity only if new stock is greater than or equal to 0
            if ($new_stock >= 0) {
                $new_quantity = $quantity - $edit_quantity;
            } else {
                echo "Cannot subtract more than the current stock!";
                exit();
            }
        } else {
            echo "Cannot subtract more than the current stock!";
            exit();
        }

        // Update the stock in the `item_history` table
        if ($operation == "subtract") {
            $sqlUpdateStock = "UPDATE item_history SET remaining_quantity = ?, quantity = ?, date_updated = NOW() WHERE boxname = ?";
            $stmtUpdateStock = $conn->prepare($sqlUpdateStock);
            if ($stmtUpdateStock === false) {
                die("Error preparing statement: " . $conn->errorInfo()[2]);
            }
            $stmtUpdateStock->execute([$new_stock, $new_stock, $box_name]);
        } else {
            $sqlUpdateStock = "UPDATE item_history SET remaining_quantity = ?, quantity = ?, date_updated = NOW() 
            WHERE boxname = ?";
            $stmtUpdateStock = $conn->prepare($sqlUpdateStock);
            if ($stmtUpdateStock === false) {
                die("Error preparing statement: " . $conn->errorInfo()[2]);
            }
            $stmtUpdateStock->execute([$new_stock, $new_stock, $box_name]);
        }
        // Close the statement
        unset($stmtUpdateStock);
        $notify = 'Updated successfully.';
        echo "<script>alert('$notify'); window.location.href = 'adminitems.php'; </script>";
        exit();
    } else {
        echo "Box not found!";
    }
}
$conn = null;
?>