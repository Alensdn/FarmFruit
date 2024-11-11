<?php
session_start();
require "sqlcon1.php";
    // Check if user is logged in
    if (!isset($_SESSION['U_id'])) {
        // Redirect to login page or handle as appropriate
        header("Location: Farmlogin.php");
        exit();
    }
    $id = $_SESSION['U_id'];
    // Retrieve user information
    $sql = "SELECT * FROM users WHERE user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]); // Pass the user_id directly to execute
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $_SESSION['U_id'] = $result['user_id'];
        $_SESSION['U_type'] = $result['user_type'];
        $_SESSION['fname'] = $result['fname'];
        $_SESSION['lname'] = $result['lname'];
    }
        $fname = $_SESSION['fname'];
        $lname = $_SESSION['lname'];
        $type = $_SESSION['U_type'];
    $stmt->closeCursor();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders</title>
    <style>
        body{
            margin: 0px;
            background-image: url('styles/images/fruit2.jpg');
            background-size: cover;
            background-attachment: fixed;
        }
        .nav1{
            background-color: #4CAF50;
            width: 100%;
            height: 50px;
            display: flex;
        }
        .nav1 ul {
            list-style-type: none;
            margin-left: 100px;
            display: flex;
            width: 7%;
            position: relative;
            left: 52%;
        }

        .nav1 ul li {
            margin-right: 20px;
        }

        .nav1 ul li:last-child {
            margin-right: 0;
        }
        .nav1 p{
            margin-top: 8px;
        }
        .nav1 label{
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
        .div01{
            display: flex;
            background-color: white;
            justify-content: space-between;
            margin-top: 5px;
            background-color:#00000000;
        }
        .div02{
            background-color: rgba(0, 0, 0, 0.301);
            width: 650px;
            height: 550px;
            overflow: auto;
            flex-wrap: wrap;
            border-radius: 10px;
        }
        .div03{
            background-color: rgba(0, 0, 0, 0.301);
            width: 650px;
            height: 550px;
            overflow: auto;
            border-radius: 10px;
        }
        .div04{
            width: 100%;
            display: flex;
            margin-top: 20px;
            
        }
        .div04 h2{
            margin: auto;
        }

        /* Style for the order details */
        .div05 {
            border: 1px solid #ccc; /* Add a border */
            padding: 10px; /* Add some padding */
            margin-bottom: 10px; /* Add some space between orders */
            width: 300px;
            height: auto;
            margin-left:25%;
            margin-top: 10px; 
            border-radius: 10px;
            background-color: white;
        }
        tr td:first-child {
            text-align: center;
            background-color: #4CAF50;
            border-radius: 10px;
        }
        table{
            border-collapse: collapse;
        }
        button{
            padding: 8px 16px;
            border: 1px solid #333;
            border-radius: 4px;
            color: black;
            transition: background-color 0.3s, color 0.3s;
            background-color: #4CAF50;
        }
        button:hover{
            background-color: #333;
            color: #fff;
        }
        .rider-info{
            margin-left: 20px;
            margin-top: 7px;
        }
        b{
            font-size: 30px;
            margin-left: 35%;
            color: white;
        }
    </style>
</head>
<body>
    <nav class="nav1">
        <img src="styles/images/icon-fruits2.png" alt="fruits">
        <p><i>Farm Fruit Store<br>Online Delivery</i></p>
        <label><i>Welcome<br><?php echo htmlspecialchars($type ) . " " . htmlspecialchars($fname); ?></i></label>
        <?php
            try {
                // Function to fetch the total count of delivered and received orders by the rider
                function fetchCompletedOrderCount($conn, $riderID) {
                    $query = "SELECT COUNT(*) AS total_completed_orders
                                FROM orders
                                WHERE Rider_ID = ? AND (Order_status = 'delivered' OR Order_status = 'received')
                                GROUP BY Order_number";
                    
                    $stmt = $conn->prepare($query);
                    $stmt->execute([$riderID]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    // If no completed orders found, set count to 0
                    return $result ? $result['total_completed_orders'] : 0;
                }
                
                // Display the total count of delivered and received orders by the rider
                $riderID = $id; // Assuming the Rider ID is stored in $id
                $completed_order_count = fetchCompletedOrderCount($conn, $riderID);
                
                echo "<div class='rider-info'>";
                echo "<p> Your Delivery Count: " . $completed_order_count . "</p>";
                echo "</div>";
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }  
            
        ?>

        <ul>
            <li><a href="Llogout.php">Logout</a></li>
        </ul>
    </nav>
    <div id="reminder" class="reminder" style="background-color: #FFD700; padding: 10px; text-align: center;">
        <p style="margin: 0;"><strong>Note:</strong> If an order disappears from "Your Orders," it means the order has been cancelled.</p>
    </div>
    <div class="div04">
        <h2>Orders</h2>
        <h2>Your Orders</h2>
    </div>
    <div class="div01">
        <div class="div02">
            <?php
            try {
                // Fetch orders with Rider_ID equal to 0
                $query = "SELECT Order_number, Delivery_barangay, Delivery_street, Order_status, Ordered_date_time, house_number,
                SUM(Total_amount) AS Overall_total_amount 
                FROM orders 
                WHERE Rider_ID = 0 AND Order_status != 'cancelled' 
                GROUP BY Order_number";
                $stmt = $conn->prepare($query);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($result) {
                    // Output each order group
                    foreach ($result as $row) {
                        echo "<div class='div05'>";
                        echo "<table>";
                        echo "<tr><td>Order Number</td> <td>:" . $row['Order_number'] . "</td></tr>";
                        echo "<tr><td>Total Amount</td> <td>:" . $row['Overall_total_amount'] . "</td></tr>";
                        echo "<tr><td>Barangay </td> <td>:" . $row['Delivery_barangay'] . "</td></tr>";
                        echo "<tr><td>Street</td> <td>:" . $row['Delivery_street'] . "</td></tr>";
                        echo "<tr><td>House number</td> <td>:" . $row['house_number'] . "</td></tr>";
                        echo "<tr><td>Status </td> <td>:" . $row['Order_status'] . "<br>";
                        $orderedDateTime = date("M d H:i", strtotime($row['Ordered_date_time']));
                        echo "<tr><td>Ordered Date Time</td> <td>:" . $orderedDateTime . "</td></tr>";
                        echo "</table>";
                        
                        // Fetch items in the order
                        $order_number = $row['Order_number'];
                        $items_query = "SELECT item.*, orders.Quantity FROM item INNER JOIN orders ON 
                        item.Item_ID = orders.Item_ID WHERE orders.Order_number = ?";
                        $stmt_items = $conn->prepare($items_query);
                        $stmt_items->execute([$order_number]);
                        $items_result = $stmt_items->fetchAll(PDO::FETCH_ASSOC);
                        
                        // Display items in the order
                        echo "<ul>";
                        foreach ($items_result as $item_row) {
                            echo "<li>" . $item_row['Item_name'] . " (Quantity: " . $item_row['Quantity'] . ")</li>";
                        }
                        echo "</ul>";

                        // Button to take order
                        echo "<form method='post' action='Lriderorder.php'>";
                        echo "<input type='hidden' name='order_number' value='" . $row['Order_number'] . "'>";
                        echo "<input type='hidden' name='id' value='" . $id . "'>";
                        echo "<button type='submit' name='take_order'>Take Order</button>";
                        echo "</form>";
                        
                        echo "</div>";
                    }
                } else {
                    echo "<b>No orders yet</b>";
                }
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
            ?>
        </div>
        <div class="div03">
            <?php
            try {
                // Function to fetch orders by rider
                function fetchOrdersByRider($conn, $riderID) {
                    $query = "SELECT o.Order_number, o.paymentstatus, o.paymentoption, o.Delivery_barangay, o.Delivery_street, 
                    o.Order_status, o.house_number,  
                    o.Ordered_date_time, SUM(o.Total_amount) AS Overall_total_amount, u.fname, u.lname, u.phone_number
                    FROM orders AS o
                    INNER JOIN users AS u ON o.Customer_ID = u.user_id
                    WHERE o.Rider_ID = ? AND o.Order_status != 'delivered' AND o.Order_status != 'received' AND o.Order_status != 'cancelled'
                    GROUP BY o.Order_number";

                    $stmt = $conn->prepare($query);
                    $stmt->execute([$riderID]);
                    return $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
                
                // Display orders for a specific rider
                $riderID = $id; // Assuming the Rider ID is stored in $id
                $orders_result = fetchOrdersByRider($conn, $riderID);
                
                if ($orders_result) {
                    // Output each order group
                    foreach ($orders_result as $row) {
                        echo "<div class='div05'>";
                        echo "<table>";
                        echo "<tr><td>Order Number</td> <td>:" . $row['Order_number'] . "</td></tr>";
                        echo "<tr><td>Customer Name</td><td>:" . $row['fname'] . " " . $row['lname'] . "</td></tr>";
                        echo "<tr><td>Customer Name</td><td>:" . $row['phone_number'] . "</td></tr>";
                        echo "<tr><td>Total Amount</td> <td>:" . $row['Overall_total_amount'] . "</td></tr>";
                        echo "<tr><td>Barangay </td> <td>:" . $row['Delivery_barangay'] . "</td></tr>";
                        echo "<tr><td>Street</td> <td>:" . $row['Delivery_street'] . "</td></tr>";
                        echo "<tr><td>House number</td> <td>:" . $row['house_number'] . "</td></tr>";
                        echo "<tr><td>Status </td> <td>:" .$row['Order_status'] . "<br>";
                        echo "<tr><td>Payment Status </td> <td>:" .$row['paymentstatus'] . "<br>";
                        echo "<tr><td>Payment Method</td> <td>:" .$row['paymentoption'] . "<br>";
                        $orderedDateTime = date("M d H:i", strtotime($row['Ordered_date_time']));
                        echo "<tr><td>Ordered Date Time</td> <td>:" . $orderedDateTime . "</td></tr>";
                        echo "</table>";
            
                        // Fetch items in the order
                        $order_number = $row['Order_number'];
                        $items_query = "SELECT i.Item_name, o.Quantity 
                                        FROM item AS i 
                                        INNER JOIN orders AS o ON i.Item_ID = o.Item_ID 
                                        WHERE o.Order_number = ?";
                        $stmt_items = $conn->prepare($items_query);
                        $stmt_items->execute([$order_number]);
                        $items_result = $stmt_items->fetchAll(PDO::FETCH_ASSOC);
            
                        // Display items in the order
                        echo "<ul>";
                        foreach ($items_result as $item_row) {
                            echo "<li>" . $item_row['Item_name'] . " (Quantity: " . $item_row['Quantity'] . ")</li>";
                        }
                        echo "</ul>";
            
                        // Button to change order status
                        if ($row['Order_status'] != 'Preparing' && $row['Order_status'] != 'On Delivery') {
                            echo "<form method='post' action='Lriderorder.php'>";
                            echo "<input type='hidden' name='order_number' value='" . $row['Order_number'] . "'>";
                            echo "<input type='hidden' name='id' value='" . $id . "'>";
                            echo "<button type='submit' name='mark_on_delivery'>Mark On Delivery</button>";
                            echo "</form>";
                        }
                        if ($row['Order_status'] == 'On Delivery') {
                            echo "<form method='post' action='Lriderorder.php' >";
                            echo "<input type='hidden' name='order_number' value='" . $row['Order_number'] . "'>";
                            echo "<button type='submit' name='mark_delivered'>Mark Delivered</button>";
                            echo "</form>";
                        }
            
                        echo "</div>";
                    }
                } else {
                    echo "<b>Take a order</b>";
                }
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
            ?>
        </div>
    </div>
    <script>
        // Function to hide the reminder after 10 seconds
        setTimeout(function() {
            var reminderDiv = document.getElementById('reminder');
            if (reminderDiv) {
                reminderDiv.style.display = 'none';
            }
        }, 10000); // 10000 milliseconds = 10 seconds
    </script>

</body>
</html>

