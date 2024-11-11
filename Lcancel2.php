<?php
require "sqlcon2.php";

// Function to handle order status update
function updateOrderStatus($sql, $orderNumber, $successMessage) {
    global $conn;

    try {
        // Prepare the SQL statement
        $stmt = $conn->prepare($sql);

        // Bind the order number parameter
        $stmt->bindParam(1, $orderNumber);

        // Execute the statement
        if ($stmt->execute()) {
            echo "<script>
                    alert('$successMessage');
                    window.location.href = 'adminorders.php';
                  </script>";
            exit();
        } else {
            echo "Error updating order status: " . $stmt->errorInfo()[2];
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    } finally {
        // Close the statement
        $stmt = null;
    }
}

// Check if order number is passed as a POST parameter
if (isset($_POST['order_number'])) {
    $orderNumber = $_POST['order_number'];

    if (isset($_POST['cancel'])) {
        try {
            // Begin transaction
            $conn->beginTransaction();

            // Fetch items in the order along with the item history IDs from which they were subtracted
            $items_query = "SELECT o.Item_ID, o.Quantity, h.id AS history_id
                            FROM orders o
                            INNER JOIN item_history h ON o.history_id = h.id
                            WHERE o.Order_number = ?";
            $stmt_items = $conn->prepare($items_query);
            $stmt_items->execute([$orderNumber]);

            // Add back items to the item history entries from which they were subtracted
            while ($item_row = $stmt_items->fetch(PDO::FETCH_ASSOC)) {
                $item_id = $item_row['Item_ID'];
                $quantity = $item_row['Quantity'];
                $history_id = $item_row['history_id'];

                // Update order status to "cancelled"
                $cancel_query = "UPDATE orders SET Order_status = 'cancelled' WHERE Order_number = ?";
                $stmt_cancel = $conn->prepare($cancel_query);
                $stmt_cancel->execute([$orderNumber]);

                // Add back remaining quantity to the specific item history entry
                $update_history_query = "UPDATE item_history SET remaining_quantity = remaining_quantity + ? WHERE id = ?";
                $stmt_update_history = $conn->prepare($update_history_query);
                $stmt_update_history->execute([$quantity, $history_id]);
            }

            // Commit transaction
            $conn->commit();

            $notify = 'Order cancelled';
            echo "<script>alert('$notify'); window.location.href = 'adminorders.php'; </script>";
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            // Display the error message
            echo "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['NotPaid'])) {
        $sql = "UPDATE orders SET paymentstatus = 'paid' WHERE Order_number = ?";
        updateOrderStatus($sql, $orderNumber, "Order with order number $orderNumber has been marked as paid successfully.");
    } elseif (isset($_POST['Paid'])) {
        $sql = "UPDATE orders SET paymentstatus = 'not paid' WHERE Order_number = ?";
        updateOrderStatus($sql, $orderNumber, "Order with order number $orderNumber has been marked as not paid successfully.");
    }
}
?>
