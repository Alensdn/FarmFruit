<?php
require "FruitSQLcon.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve item ID and new price from the form
    $item_id = $_POST['item_id'];
    $new_price = $_POST['new_price'];

    // Validate input (you may want to add more validation)
    if (!empty($item_id) && is_numeric($new_price)) {
        try {
            // Prepare SQL statement
            $sql = "UPDATE item SET Item_price = ? WHERE Item_ID = ?";
            $stmt = $conn->prepare($sql);

            // Bind parameters
            $stmt->bindParam(1, $new_price);
            $stmt->bindParam(2, $item_id);

            // Execute the statement
            if ($stmt->execute()) {
                $notify = 'Updated successfully.';
                echo "<script>alert('$notify'); window.location.href = 'adminitems.php'; </script>";
            exit();
            } else {
                echo "Error updating price: " . $stmt->errorInfo()[2];
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Invalid data. Please make sure item ID is selected and new price is numeric.";
    }
}
?>
