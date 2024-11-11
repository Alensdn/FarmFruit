<?php
require "FruitSQLcon.php";
session_start();
$fname = $_SESSION['fname'];
$lname = $_SESSION['lname'];

if (isset($_POST['orderstat'])) {
    $order_number = $_POST['order_number'];
    $stat = $_POST['status'];

    if ($stat == 'cancelled') {
        try {
            // Begin transaction
            $conn->beginTransaction();

            // Fetch items in the order along with the item history IDs from which they were subtracted
            $items_query = "SELECT o.Item_ID, o.Quantity, h.id AS history_id
                            FROM orders o
                            INNER JOIN item_history h ON o.history_id = h.id
                            WHERE o.Order_number = ?";
            $stmt_items = $conn->prepare($items_query);
            $stmt_items->execute([$order_number]);

            // Add back items to the item history entries from which they were subtracted
            while ($item_row = $stmt_items->fetch(PDO::FETCH_ASSOC)) {
                $item_id = $item_row['Item_ID'];
                $quantity = $item_row['Quantity'];
                $history_id = $item_row['history_id'];

                // Update order status to "cancelled"
                $cancel_query = "UPDATE orders SET Order_status = ? WHERE Order_number = ?";
                $stmt_cancel = $conn->prepare($cancel_query);
                $stmt_cancel->execute([$stat, $order_number]);

                // Add back remaining quantity to the specific item history entry
                $update_history_query = "UPDATE item_history SET remaining_quantity = remaining_quantity + ? WHERE id = ?";
                $stmt_update_history = $conn->prepare($update_history_query);
                $stmt_update_history->execute([$quantity, $history_id]);
            }

            // Commit transaction
            $conn->commit();

            $notify = 'Order cancelled';
            echo "<script>alert('$notify');</script>";
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            // Display the error message
            echo "Error: " . $e->getMessage();
        }
    } 
    else if($stat == 'received'){
        $cancel_query = "UPDATE orders SET Order_status = ? WHERE Order_number = ?";
        $stmt = $conn->prepare($cancel_query);
        $stmt->execute([$stat, $order_number]);
        $notify = 'Item Received';
        echo "<script> alert('$notify');</script>";  
    }   
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>my orders</title>
    <style>
            body {
                margin: 0px;
                background-image: url('styles/images/fruit2.jpg');
                background-size: cover;
            }
            .nav1 {
                background-color: #4CAF50;
                width: 100%;
                height: 50px;
                display: flex;
            }
            .nav1 ul {
                list-style-type: none;
                margin-left: 100px;
                display: flex;
                width: 30%;
                position: relative;
                left: 26%;
            }

            .nav1 ul li {
                margin-right: 20px;
            }

            .nav1 ul li:last-child {
                margin-right: 0;
            }
            .nav1 p {
                margin-top: 8px;
            }
            .nav1 label {
                margin-top: 8px;
                margin-left: 20px;
            }

            .nav1 ul li a {
                text-decoration: none;
                padding: 8px 16px;
                border: 1px solid #333;
                border-radius: 4px;
                color: #333;
                transition: background-color 0.3s, color 0.3s;
            }

            .nav1 ul li a:hover {
                background-color: #333;
                color: #fff;
            }
            .divcon1 {
                margin-top: 2%;
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 20px;
                width: 90%;
                margin: 2% auto;
                padding: 20px;
            }

            .grid-item {
                background-color: #f9f9f9;
                border: 1px solid #ddd;
                border-radius: 5px;
                padding: 20px;
                padding: 20px;
                font-size: 16px;
                text-align: left;
                display: flex; /* Add display: flex; */
                flex-direction: column; /* Set flex-direction to column */
            }

            .grid-item h2 {
                margin-top: 0;
                font-size: 20px;
                color: #333;
            }

            .grid-item p {
                margin: 10px 0;
                color: #666;
            }

            .grid-item ul {
                padding: 0;
            }

            .grid-item ul li {
                margin-bottom: 5px;
            }
            .grid-item form {
                margin-top: auto; /* Align form to the end */
                order: 3; /* Adjust the order of form */
            }
            h2 {
                text-align: center;
                margin-top: 20px;
                color: #333;
            }
        </style>
</head>
<body>

<nav class="nav1">
    <img src="styles/images/icon-fruits2.png" alt="fruits">
    <p><i>Farm Fruit Store<br>Online Delivery</i></p>
    <label><i>Welcome<br><?php echo htmlspecialchars($fname) . " " . htmlspecialchars($lname); ?></i></label>
    <ul>
        <li><a href="Farmhome.php">home</a></li>
        <li><a href="order.php">order</a></li>
        <li><a href="cart.php">cart</a></li>
        <li><a href="myorders.php">myorders</a></li>
        <li><a href="myprofile.php">myprofile</a></li>
        <li><a href="Llogout.php">Logout</a></li>
    </ul>
</nav>
<h2>Your Orders</h2>
<div class="divcon1">
<?php
$id = $_SESSION['U_id']; // Retrieve the logged-in user's ID

// Fetch all orders excluding delivered and canceled orders
$query = "SELECT Order_number, paymentoption, paymentstatus, Delivery_barangay, Delivery_street, Order_status, Ordered_date_time, SUM(Total_amount) AS Overall_total_amount 
          FROM orders 
          WHERE Customer_ID = ? AND Order_status NOT IN ('d', 'cancelled')
          GROUP BY Order_number";
$stmt = $conn->prepare($query);
$stmt->execute([$id]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($result) {
    foreach ($result as $row) {
        echo "<div class='grid-item'>";
        echo "Order Number: " . htmlspecialchars($row['Order_number']) . "<br>";
        echo "Overall Total Amount: " . htmlspecialchars($row['Overall_total_amount']) . "<br>";
        echo "Order Status: " . htmlspecialchars($row['Order_status']) . "<br>";
        echo "Ordered Date Time: " . htmlspecialchars($row['Ordered_date_time']) . "<br>";
        echo "Payment option: " . htmlspecialchars($row['paymentoption']) . "<br>";
        echo "Payment status: " . htmlspecialchars($row['paymentstatus']) . "<br>";

        // Fetch items for the current order
        $order_number = $row['Order_number'];
        $items_query = "SELECT item.*, orders.Quantity 
                        FROM item 
                        INNER JOIN orders ON item.Item_ID = orders.Item_ID 
                        WHERE orders.Order_number = ?";
        $items_stmt = $conn->prepare($items_query);
        $items_stmt->execute([$order_number]);
        $items_result = $items_stmt->fetchAll(PDO::FETCH_ASSOC);

        // Display items in the order
        echo "<ul>";
        foreach ($items_result as $item_row) {
            echo "<li>" . htmlspecialchars($item_row['Item_name']) . " (Quantity: " . htmlspecialchars($item_row['Quantity']) . ")</li>";
        }
        echo "</ul>";
            // Show "Cancel" button
            echo "<form method='post'>";
            echo "<input type='hidden' name='order_number' value='" . htmlspecialchars($row['Order_number']) . "' />";
            echo "<input type='hidden' name='status' value='cancelled'/>";
            echo "<input type='submit' name='orderstat' value='cancel' />";
            echo "</form>";

        echo "</div>";
    }
} else {
    echo "No active orders found for this user.";
}
?>

</div>
</body>
</html>
