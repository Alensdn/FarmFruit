<?php
    require "FruitSQLcon.php";
    session_start();
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
    $currentDate = date('Y-m-d');

    // Query to count orders placed today for each status
    $sql_today_orders = "SELECT Order_status, COUNT(DISTINCT Order_number) AS order_count 
                        FROM orders 
                        WHERE DATE(Ordered_date_time) = :currentDate 
                        AND Order_status IN ('pending', 'preparing', 'on_delivery')
                        GROUP BY Order_status";
    $stmt_today_orders = $conn->prepare($sql_today_orders);
    $stmt_today_orders->bindParam(':currentDate', $currentDate);
    $stmt_today_orders->execute();

    // Initialize counts
    $pending_count = 0;
    $preparing_count = 0;
    $on_delivery_count = 0;

    // Fetch counts
    while ($row_today_orders = $stmt_today_orders->fetch(PDO::FETCH_ASSOC)) {
        // Update counts based on status
        switch ($row_today_orders['Order_status']) {
            case 'pending':
                $pending_count = $row_today_orders['order_count'];
                break;
            case 'preparing':
                $preparing_count = $row_today_orders['order_count'];
                break;
            case 'on_delivery':
                $on_delivery_count = $row_today_orders['order_count'];
                break;
            default:
                break;
        }
    }

    function generateOrderHistoryTable() {
        global $conn;
        echo "<table id='order-history-table' class='status-table' style='display: none;'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>Order number</th>";
        echo "<th>Total items</th>";
        echo "<th>Amount</th>";
        echo "<th>Date order</th>";
        echo "<th>Status</th>";
        echo "<th>Action</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
    
        // SQL query to fetch all orders with their items
        $sql_history = "SELECT o.Order_status, DATE_FORMAT(o.Ordered_date_time, '%d %M %Y') AS Ordered_date_time, o.Order_number, 
        SUM(o.Quantity) AS total_quantity, SUM(o.Total_amount) AS total_amount
        FROM orders o
        WHERE DATE(o.Ordered_date_time) < CURDATE()
        GROUP BY o.Order_number
        ORDER BY o.Ordered_date_time DESC, o.Order_number";
        $stmt_history = $conn->prepare($sql_history);
        $stmt_history->execute();
    
        // Check if there are orders
        if ($stmt_history->rowCount() > 0) {
            while($row = $stmt_history->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . $row["Order_number"] . "</td>";
                echo "<td>" . $row["total_quantity"] . "</td>";
                echo "<td>₱" . $row["total_amount"] . "</td>";
                echo "<td>" . $row["Ordered_date_time"] . "</td>";
                echo "<td>" . $row["Order_status"] . "</td>";
                echo "<td>";
                echo "<button onclick='toggleDetails(\"details_" . $row["Order_number"] . "\")'>See Details</button>";
                echo "</td>";
                echo "</tr>";
    
                // Additional row to display details, initially hidden
                echo "<tr class='details' id='details_" . $row["Order_number"] . "' style='display: none;'>";
                echo "<td colspan='6'>";
                
                // Fetch and display details for this order
                $sql_details = "SELECT o.*, c.*, SUM(o.Total_amount) AS overalltotal, 
                DATE_FORMAT(Ordered_date_time, '%d %M %Y') AS Ordered_date_time
                FROM orders o
                JOIN users c ON o.Customer_ID = c.user_id
                WHERE o.Order_number = :order_number
                GROUP BY o.Order_number";
                $stmt_details = $conn->prepare($sql_details);
                $stmt_details->bindParam(':order_number', $row['Order_number']);
                $stmt_details->execute();
                if ($stmt_details->rowCount() > 0) {
                    while($detail_row = $stmt_details->fetch(PDO::FETCH_ASSOC)) {
                        echo "<div class='container01'>";
                        echo "<div>";
                        echo "<p>Customer ID <br>" . $detail_row["Customer_ID"] . "</p>";
                        echo "<p>Customer name <br>" . $detail_row["fname"] . " " . $detail_row["lname"] ."<br></p>";
                        echo "<p>Total Price: ₱" . $detail_row["overalltotal"] . "</p>";
                        echo "<p>Address <br>" . $detail_row["Delivery_barangay"] .
                        ", ". $detail_row["Delivery_street"] .", ". $detail_row["house_number"] ."<p>";
                        echo "<p>Status: " . $detail_row["Order_status"] . "</p>";
                        $items_query = "SELECT item.*, orders.Quantity FROM item INNER JOIN orders ON 
                        item.Item_ID = orders.Item_ID WHERE orders.Order_number = :order_number";
                        $stmt_items = $conn->prepare($items_query);
                        $stmt_items->bindParam(':order_number', $row['Order_number']);
                        $stmt_items->execute();
                        echo "</div>";
                       
                        // Display items in the order
                        echo "<div>
                        <p><strong>Items</strong></p>";
                        echo "<ul>";
                        while ($item_row = $stmt_items->fetch(PDO::FETCH_ASSOC)) {
                            echo "<li>" . $item_row['Item_name'] . " (Quantity: " . $item_row['Quantity'] . ")</li>";
                        }
                        echo "</ul>";
                        echo "</div>";
                        echo "</div>";
                    }
                } else {
                    echo "No details found";
                }
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No orders found</td></tr>";
        }
    
        echo "</tbody>";
        echo "</table>";
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Orders Table</title>
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
            width: 30%;
            position: relative;
            left: 29%;
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
        table {
            width: 80%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-left: 10%;
        }
        th, td {
            background-color: white;
            padding: 8px 12px;
            border: 1px solid #ccc;
            text-align: center;
        }
        th {
            background-color: #4CAF50;
        }
        .details {
            display: none;
        }
        .status-table {
            display: none;

        }
        .h11{
            text-align: center;
        }
        .button01 {
            display: flex;
            justify-content: center;
        }

        .button01 > * {
            margin: 0 20px; /* Adjust as needed */
        }
        .button01 button{
            padding: 8px 16px;
            border: 1px solid #333;
            border-radius: 4px;
            color: black;
            transition: background-color 0.3s, color 0.3s;
            background-color: #4CAF50;
            cursor: pointer;
        }
        .button01 button:hover{
            background-color: #333;
            color: #fff;
        }
        .notify{
            text-align: center;
            background-color: #4CAF50;
            font-size: 20px;
            display: flex;
            align-items: center;
            border: 1px solid #333;
            border-radius: 4px;
            width: 40%;
            height: auto;
            margin-left: 30%;
            margin-top: 10px;
        }
        .notify p{

        }
        .button02{
            padding: 4px 8px;
            border: 1px solid #333;
            border-radius: 4px;
            color: black;
            transition: background-color 0.3s, color 0.3s;
            background-color: #4CAF50;
            cursor: pointer;
            margin-left: 95%;
        }
        .button02:hover{
            background-color: #333;
            color: #fff;
        }
        .container01{
            display:flex;
            width: 50%;
            background-color: #4CAF50;
            margin-left: 30%;
            border-radius: 5px;
            justify-content: center;
        }
    </style>
    <script>
        function toggleDetails(orderNumber) {
            var details = document.getElementById(orderNumber);
            details.style.display = (details.style.display === "none") ? "table-row" : "none";
        }

        function toggleStatus(status) {
            var table = document.getElementById(status + "-table");
            var currentDisplay = table.style.display;
            
            if (currentDisplay === "none") {
                // If currently hidden, display the table
                table.style.display = "table";
            } else {
                // If currently displayed, hide the table
                table.style.display = "none";
            }
            
            // Hide all other tables
            var tables = document.getElementsByClassName("status-table");
            for (var i = 0; i < tables.length; i++) {
                if (tables[i] !== table) {
                    tables[i].style.display = "none";
                }
            }
        }
        function showNotification() {
            var notification = document.getElementById('notify');
            notification.style.display = 'block';
        }

        // Function to close the notification
        function closeNotification() {
            var notification = document.getElementById('notify');
            notification.style.display = 'none';
        }
        window.onload = function() {
            showNotification();
            // Automatically close the notification after 1 minute (60,000 milliseconds)
            setTimeout(closeNotification, 30000);
        };

        function toggleOrderHistory() {
            var table = document.getElementById('order-history-table');
            var currentDisplay = table.style.display;
            
            if (currentDisplay === "none") {
                // If currently hidden, display the table
                table.style.display = "table";
            } else {
                // If currently displayed, hide the table
                table.style.display = "none";
            }
        }
    </script>
</head>
<body>
<nav class="nav1">
        <img src="styles/images/icon-fruits2.png" alt="fruits">
        <p><i>Farm Fruit Store<br>Online Delivery</i></p>
        <label><i>Welcome<br><?php echo htmlspecialchars($type ) . " " . htmlspecialchars($fname); ?></i></label>
        <ul>
            <li><a href="adminhome.php">Dashboard</a></li>
            <li><a href="adminitems.php">Items</a></li>
            <li><a href="adminusers.php">UserDetails</a></li>
            <li><a href="adminorders.php">Orders</a></li>
            <li><a href="Llogout.php">Logout</a></li>
        </ul>
    </nav>
    <h1 class="h11">Orders Details</h1>
    <div class="button01">
        <!-- Buttons for status -->
        <button onclick="toggleStatus('pending')">Pending</button>
        <button onclick="toggleStatus('preparing')">Preparing</button>
        <button onclick="toggleStatus('on_delivery')">On Delivery</button>
        <button onclick="toggleStatus('delivered')">Delivered</button>
        <button onclick="toggleStatus('cancelled')">Cancelled</button>
        <button onclick="toggleStatus('received')">Recieved</button>
        <button onclick="toggleOrderHistory()">Order History</button>
    </div>
    <div id="notify" class="notify">
        <button class="button02" onclick="closeNotification()">X</button>
        <p>TODAY ORDERS</p>
        <p><strong><?php echo $pending_count; ?> </strong>orders pending.</p>
        <p><strong><?php echo $preparing_count; ?> </strong>orders preparing.</p>
        <p><strong><?php echo $on_delivery_count; ?> </strong>orders on delivery.</p>
    </div>
    <!-- Tables to Show -->
    <?php
    function generateStatusTable($status, $status_display, $currentDate, $conn) {
        echo "<table id='{$status}-table' class='status-table' style='display: none;'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>Order number</th>";
        echo "<th>Total items</th>";
        echo "<th>Amount</th>";
        echo "<th>Status</th>";
        echo "<th>Payment option</th>";
        echo "<th>Payment status</th>";
        echo "<th>Action</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
    
        // SQL query to fetch today's orders for this status
        $sql_status = "SELECT paymentstatus ,paymentoption, Order_status, DATE_FORMAT(Ordered_date_time, '%d %M %Y') AS Ordered_date_time, 
        Order_number, SUM(Quantity) AS total_quantity, SUM(Total_amount) AS total_amount FROM orders 
        WHERE Order_status = :status AND DATE(Ordered_date_time) = :currentDate 
        GROUP BY Order_number";
        $stmt_status = $conn->prepare($sql_status);
        $stmt_status->bindParam(':status', $status);
        $stmt_status->bindParam(':currentDate', $currentDate);
        $stmt_status->execute();
    
        // Check if there are orders for this status
        if ($stmt_status->rowCount() > 0) {
            // Output data of each order
            while($row = $stmt_status->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . $row["Order_number"]. "</td>";
                echo "<td>" . $row["total_quantity"] . "</td>";
                echo "<td>₱" . $row["total_amount"] . "</td>";
                echo "<td>" . $row["Order_status"] . "</td>";
                echo "<td>" . $row["paymentoption"] . "</td>";
                echo "<td>" . $row["paymentstatus"] . "</td>";
                if ($status !== 'delivered' && $status !== 'cancelled'&& $status !== 'received') {
                    echo "<td>";
                    echo "<button onclick='toggleDetails(\"details_" . $row["Order_number"] . "\")'>See Details</button>";
                    echo "<form id='cancel_order_form' action='Lcancel2.php' method='post' style='display: inline;' 
                    onsubmit='return confirm(\"Cancel Order?\");'>";
                    echo "<input type='hidden' name='order_number' value='" . $row["Order_number"] . "'>";
                    echo "<input type='hidden' name='cancel' value='cancel'>";
                    echo "<button type='submit'>Cancel</button>";
                    echo "</form>";
                    
                    if ($row["paymentstatus"] !== 'paid') {
                        echo "<form id='paid_order_form' action='Lcancel2.php' method='post' style='display: inline;'>";
                        echo "<input type='hidden' name='order_number' value='" . $row["Order_number"] . "'>";
                        echo "<input type='hidden' name='NotPaid' value='NotPaid'>";
                        echo "<button type='submit'>Paid</button>";
                        echo "</form>";
                    } else {
                        echo "<form id='paid_order_form' action='Lcancel2.php' method='post' style='display: inline;'>";
                        echo "<input type='hidden' name='order_number' value='" . $row["Order_number"] . "'>";
                        echo "<input type='hidden' name='Paid' value='Paid'>";
                        echo "<button type='submit'>Undo Paid</button>";
                        echo "</form>";
                    }
                    echo "</td>";
                } else {
                    echo "<td>";
                    echo "<button onclick='toggleDetails(\"details_" . $row["Order_number"] . "\")'>See Details</button>";
                    echo "</td>";
                }
                echo "</tr>";
                
               echo "<tr class='details' id='details_" . $row["Order_number"] . "' style='display: none;'>";
               echo "<td colspan='7'>";
               // Fetch and display details for this order
               $sql_details = "SELECT o.*, c.*, SUM(o.Total_amount) AS overalltotal, 
               DATE_FORMAT(Ordered_date_time, '%d %M %Y') AS Ordered_date_time
               FROM orders o
               JOIN users c ON o.Customer_ID = c.user_id
               WHERE o.Order_number = :order_number
               GROUP BY o.Order_number";
               $stmt_details = $conn->prepare($sql_details);
               $stmt_details->bindParam(':order_number', $row['Order_number']);
               $stmt_details->execute();
               if ($stmt_details->rowCount() > 0) {
                   while($detail_row = $stmt_details->fetch(PDO::FETCH_ASSOC)) {
                       echo "<div class='container01'>";
                       echo "<div>";
                       echo "<p>Customer ID <br>" . $detail_row["Customer_ID"] . "</p>";
                       echo "<p>Customer name <br>" . $detail_row["fname"] . " " . $detail_row["lname"] ."<br></p>";
                       echo "<p>Total Price: ₱" . $detail_row["overalltotal"] . "</p>";
                       echo "<p>Address <br>" . $detail_row["Delivery_barangay"] .
                       ", ". $detail_row["Delivery_street"] .", ". $detail_row["house_number"] ."<p>";
                       echo "<p>Status: " . $detail_row["Order_status"] . "</p>";
                       echo "<p>Date Order<br>" . $detail_row["Ordered_date_time"] . "</p>";
                       $order_number = $row['Order_number'];
                       $items_query = "SELECT item.*, orders.Quantity FROM item INNER JOIN orders ON 
                       item.Item_ID = orders.Item_ID WHERE orders.Order_number = :order_number";
                       $stmt_items = $conn->prepare($items_query);
                       $stmt_items->bindParam(':order_number', $row['Order_number']);
                       $stmt_items->execute();
                       echo "</div>";
                       
                       // Display items in the order
                       echo "<div>
                       <p><strong>Items</strong></p>";
                       echo "<ul>";
                       while ($item_row = $stmt_items->fetch(PDO::FETCH_ASSOC)) {
                           echo "<li>" . $item_row['Item_name'] . " (Quantity: " . $item_row['Quantity'] . ")</li>";
                       }
                       echo "</ul>";
                       echo "</div>";
                       echo "</div>";
                   }
               } else {
                   echo "No details found";
               }
               echo "</td>";
               echo "</tr>";
           }
       } else {
           echo "<tr><td colspan='5'>No $status_display orders found Today</td></tr>";
       }

       echo "</tbody>";
       echo "</table>";
   }
              
    // Generate tables for Preparing, On Delivery, Delivered, and Cancelled orders
    generateStatusTable('pending', 'Pending', $currentDate, $conn);
    generateStatusTable('preparing', 'Preparing', $currentDate, $conn);
    generateStatusTable('on_delivery', 'On Delivery', $currentDate, $conn);
    generateStatusTable('delivered', 'Delivered', $currentDate, $conn);
    generateStatusTable('cancelled', 'Cancelled', $currentDate, $conn);
    generateStatusTable('received', 'Received', $currentDate, $conn);

    generateOrderHistoryTable();
    // Close connection
    $conn = null;
    ?>
</body>
</html>
