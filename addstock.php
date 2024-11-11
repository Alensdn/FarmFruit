<?php
require "FruitSQLcon.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_id = $_POST['item_id'];
    $box_name = $_POST['box_name'];
    $expiration_date = $_POST['expiration_date'];
    $stock = $_POST['stock'];

    // Validate the POST data
    if (!empty($item_id) && !empty($box_name) && !empty($stock)) {
        // Insert the stock addition into the item_history table
        $sql2 = "INSERT INTO item_history (item_id, boxname, date_expiration, quantity, remaining_quantity) 
                 VALUES (:item_id, :box_name, :expiration_date, :stock, :stock)";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bindParam(':item_id', $item_id);
        $stmt2->bindParam(':box_name', $box_name);
        $stmt2->bindParam(':expiration_date', $expiration_date);
        $stmt2->bindParam(':stock', $stock);

        if ($stmt2->execute()) {
            $notify = 'BOX added successfully.';
            echo "<script>alert('$notify'); window.location.href = 'adminitems.php';</script>";
            exit();
        } else {
            echo "Error in executing the statement.";
        }
    } else {
        echo "Required fields are missing.";
    }
}

// Close connection
$conn = null;
?>
