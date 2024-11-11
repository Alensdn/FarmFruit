<?php
require "FruitSQLcon.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_id = intval($_POST['item_id']);

    if ($item_id > 0) {
        try {
            // Begin a transaction
            $conn->beginTransaction();

            // Prepare the delete statement for item_history
            $stmt1 = $conn->prepare("DELETE FROM item_history WHERE item_id = ?");
            $stmt1->bindParam(1, $item_id, PDO::PARAM_INT);

            // Execute the statement
            if ($stmt1->execute()) {
                // Prepare the delete statement for item
                $stmt2 = $conn->prepare("DELETE FROM item WHERE Item_ID = ?");
                $stmt2->bindParam(1, $item_id, PDO::PARAM_INT);

                // Execute the statement
                if ($stmt2->execute()) {
                    // Commit the transaction
                    $conn->commit();

                    $notify = 'Item deleted successfully.';
                    echo "<script>alert('$notify'); window.location.href = 'adminitems.php';</script>";
                    exit();
                } else {
                    // Rollback the transaction
                    $conn->rollBack();
                    echo "Error deleting item: " . $stmt2->errorInfo()[2];
                }
            } else {
                // Rollback the transaction
                $conn->rollBack();
                echo "Error deleting item history: " . $stmt1->errorInfo()[2];
            }
        } catch (PDOException $e) {
            // Rollback the transaction in case of an error
            $conn->rollBack();
            echo "Error: " . $e->getMessage();
        } finally {
            // Close the statements
            $stmt1 = null;
            $stmt2 = null;
        }
    } else {
        echo "Invalid item ID.";
    }
}

$conn = null;
?>

