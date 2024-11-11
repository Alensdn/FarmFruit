<?php
require "sqlcon2.php";
session_start();

// Check if user is logged in
if (!isset($_SESSION['U_id'])) {
    // Redirect to login page or handle as appropriate
    header("Location: Farmlogin.php");
    exit();
}

$id = $_SESSION['U_id'];

// Check if an item needs to be removed
if (isset($_GET['remove']) && isset($_GET['item_id'])) {
    $removeItemId = $_GET['item_id'];
    $sql = "DELETE FROM cart WHERE userID=? AND ItemID=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id, $removeItemId]);
    
    $_SESSION['action'] = 'Customer removed an item from the cart';
    $_SESSION['location'] = 'cart.php';
    $notify = 'Item has been removed.';

    // Redirect with alert
    echo "<script>
        alert('$notify');
        window.location.href = 'Ltrail.php';
    </script>";
    exit();
}

// Check if quantity needs to be updated
if (isset($_POST['update_quantity']) && isset($_POST['item_id']) && isset($_POST['new_quantity'])) {
    $updateItemId = $_POST['item_id'];
    $newQuantity = $_POST['new_quantity'];

    $stockQuery = "SELECT COALESCE(SUM(CASE WHEN item_history.date_expiration > CURRENT_DATE 
    OR item_history.date_expiration IS NULL THEN item_history.quantity ELSE 0 END), 0) AS stock
                   FROM item
                   LEFT JOIN item_history ON item.Item_ID = item_history.item_id
                   WHERE item.Item_ID = :item_id
                   GROUP BY item.Item_ID";

    $stmt = $conn->prepare($stockQuery);
    $stmt->bindParam(":item_id", $updateItemId); // Corrected variable name
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // If the quantity exceeds the available stock, display an error message and exit
    if ($row && $newQuantity > $row['stock']) { // Corrected variable name
        $notify = 'Quantity exceeds available stock.';
        echo "<script>
            alert('$notify');
            window.location.href = 'order.php';
        </script>";
        exit();
    }
    
    if ($newQuantity > 0) {
        $sql = "UPDATE cart SET quantity=? WHERE userID=? AND itemID=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$newQuantity, $id, $updateItemId]);
    }

    $_SESSION['action'] = 'Customer has adjusted the quantity of an item in the cart';
    $_SESSION['location'] = 'cart.php';
    $notify = 'Item has been adjusted.';

    // Redirect with alert
    echo "<script>
        alert('$notify');
        window.location.href = 'Ltrail.php';
    </script>";
    exit();
} 


if (isset($_SESSION['U_id'], $_POST['submit_order'])) {
    // Retrieve user ID from session
    $customerId = $_SESSION['U_id'];

    // Sanitize and retrieve other form data
    $city = htmlspecialchars($_POST['B']);
    $barangay = htmlspecialchars($_POST['S']);
    $street = htmlspecialchars($_POST['HN']);
    $paymentoption = htmlspecialchars($_POST['PM']);
    $paymentstatus = "not paid";
    $status = "pending";

    // Retrieve cart items for the logged-in user
    $sql_cart = "SELECT * FROM cart WHERE userID=?";
    $stmt_cart = $conn->prepare($sql_cart);
    $stmt_cart->execute([$customerId]);
    $cart_items = $stmt_cart->fetchAll(PDO::FETCH_ASSOC);

    if ($cart_items) {
        $conn->beginTransaction(); // Start transaction to ensure data integrity

        try {
            // Generate unique order number once for the whole order
            $orderNumber = uniqid('ORD');

            foreach ($cart_items as $row_cart) {
                // Retrieve product details from the cart
                $productId = $row_cart['itemID'];
                $quantity = $row_cart['quantity'];

                // Retrieve item price from the 'item' table (assuming it exists)
                $sql_item = "SELECT Item_price FROM item WHERE Item_ID = ?";
                $stmt_item = $conn->prepare($sql_item);
                $stmt_item->execute([$productId]);
                $row_item = $stmt_item->fetch(PDO::FETCH_ASSOC);

                if ($row_item) {
                    $item_price = $row_item['Item_price'];
                } else {
                    throw new Exception("Item price not found for product ID: $productId");
                }

                $totalAmount = $quantity * $item_price;

                // Fetch the item history ID from the item_history table
                $item_history_query = "SELECT id FROM item_history WHERE item_id = ? AND remaining_quantity > 0 AND date_expiration > NOW() ORDER BY date_expiration ASC";
                $stmt_history = $conn->prepare($item_history_query);
                $stmt_history->execute([$productId]);
                
                // Check if there are non-expired item history entries
                if ($stmt_history->rowCount() == 0) {
                    throw new Exception("No available items for product ID: $productId");
                }

                // Fetch the first non-expired item history ID
                $item_history_row = $stmt_history->fetch(PDO::FETCH_ASSOC);
                $history_id = $item_history_row['id'];

                // Insert order details into orders table
                $sql_order = "INSERT INTO orders (Customer_ID, Item_ID, Quantity, Total_amount, Delivery_barangay, Delivery_street, house_number, Order_number, Order_status, paymentoption, paymentstatus, history_id) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt_order = $conn->prepare($sql_order);
                $stmt_order->execute([$customerId, $productId, $quantity, $totalAmount, $city, $barangay, $street, $orderNumber, $status, $paymentoption, $paymentstatus, $history_id]);

                // Fetch item history with remaining quantity
                $item_history_query = "SELECT * FROM item_history WHERE item_id = ? AND remaining_quantity > 0 ORDER BY date_expiration ASC";
                $stmt_history = $conn->prepare($item_history_query);
                $stmt_history->execute([$productId]);

                while ($item_history_row = $stmt_history->fetch(PDO::FETCH_ASSOC)) {
                    // Check if the item is expired
                    $expiration_date = $item_history_row['date_expiration'];
                    if (strtotime($expiration_date) < time()) {
                        // Skip subtracting from expired items
                        continue;
                    }
                
                    $item_history_id = $item_history_row['id'];
                    $item_remaining_quantity = $item_history_row['remaining_quantity'];
                
                    if ($quantity <= $item_remaining_quantity) { // Use $quantity instead of $remaining_quantity
                        // If order quantity is less than or equal to remaining quantity, subtract from this box
                        $update_history_query = "UPDATE item_history SET remaining_quantity = remaining_quantity - ? WHERE id = ?";
                        $stmt_update_history = $conn->prepare($update_history_query);
                        $stmt_update_history->execute([$quantity, $item_history_id]); // Use $quantity instead of $remaining_quantity
                        break; // Exit the loop as the quantity is fulfilled
                    } else {
                        // If order quantity is more than remaining quantity, subtract from this box and continue to the next box
                        $update_history_query = "UPDATE item_history SET remaining_quantity = 0 WHERE id = ?";
                        $stmt_update_history = $conn->prepare($update_history_query);
                        $stmt_update_history->execute([$item_history_id]);
                        $quantity -= $item_remaining_quantity; // Reduce quantity for the next iteration
                    }
                }
            }

            // Clear the cart after successful order placement
            $sql_clear_cart = "DELETE FROM cart WHERE userID=?";
            $stmt_clear_cart = $conn->prepare($sql_clear_cart);
            $stmt_clear_cart->execute([$customerId]);

            $conn->commit(); // Commit transaction if all orders are successfully inserted

            $_SESSION['action'] = 'Customer has submitted an order';
            $_SESSION['location'] = 'myorders.php';
            $notify = 'Order placed successfully.';

            // Redirect with alert
            echo "<script>
            alert('$notify');
            window.location.href = 'Ltrail.php';
            </script>";
            exit();

        } catch (Exception $e) {
            $conn->rollback(); // Rollback transaction if an error occurs
            $_SESSION['error'] = $e->getMessage();
            header("Location: myorders.php");
            exit();
        }
    } else {
        $_SESSION['action'] = 'Customer has order with empty cart failed';
        $_SESSION['location'] = 'cart.php';
        $notify = 'Your cart is empty!';

        // Redirect with alert
        echo "<script>
            alert('$notify');
            window.location.href = 'Ltrail.php';
        </script>";
        exit();
    }
} else {
    $_SESSION['error'] = "Invalid request!";
    header("Location: myorders.php");
    exit();
}


$conn = null; // Close the connection
?>

