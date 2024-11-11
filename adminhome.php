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
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        body{
            margin: 0px;
            background-image: url('Styles/images/fruit2.jpg');
            background-size: cover;
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
        .diva {
            margin-top: 50px;
            display: flex;
            background-color: rgba(12, 4, 4, 0);
            width: 90%;
            height: 80vh;
            justify-content: space-evenly;
            margin-left: 5%;
            position: relative; /* Add relative positioning to the container */
        }

        .div1 {
            overflow: auto;
            padding: 0;
            background-color: white;
            width: 300px;
            height: 420px;
            border-radius: 10px;
            background-color: rgba(0, 0, 0, 0.301);
        }
        .div1 h2{
            padding: 0;
            background-color: #4CAF50;
            color: rgb(0, 0, 0);
            height: 30px;
            width: 100%;
            margin-top: 0px;
            text-align: center;
            align-items: center;
        }
        .div1 table{
            background-color: white;
            margin-left: 89px;
            height: auto;
            text-align: center; /* Center align the text within the table */
            border-collapse: collapse;
            width: 120px;
        }
        .div1 th {
            font-size: x-large;
            background-color: #4CAF50;
            color: rgb(8, 8, 8);
            font-weight: normal;
            text-align: left;
            padding: 3px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        .div1 tr {
            background-color: white;
        }
        .div1 td {
            font-size: x-large;
            padding: 3px;
            border-bottom: 1px solid #ddd;
        }
        .table1{
            margin-top: 0px;
        }
        .table2{
            margin-top: 10px;
        }

        .div2 {
            overflow: hidden;
            background-color: rgba(0, 0, 0, 0.301);
            padding: 0;
            width: 300px;
            height: 250px;
            border-radius: 10px;
        }

        .div2 h2{
            padding: 0;
            background-color: #4CAF50;
            color: rgb(0, 0, 0);
            height: 30px;
            width: 100%;
            margin-top: 0px;
            text-align: center;
            align-items: center;
        }
        .div2 table{
            background-color: white;
            margin-left: 89px;
            height: auto;
            text-align: center; /* Center align the text within the table */
            border-collapse: collapse;
            width: 120px;
        }
        .div2 th {
            font-size: x-large;
            background-color: #4CAF50;
            color: rgb(8, 8, 8);
            font-weight: normal;
            text-align: left;
            padding: 3px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        .div2 tr {
            background-color: white;
        }
        .div2 td {
            font-size: x-large;
            padding: 3px;
            border-bottom: 1px solid #ddd;
        }

        .div3 {
            overflow: auto;
            padding: 0;
            background-color: rgba(0, 0, 0, 0.301);
            border-radius: 10px;
            width: 300px;
            height: 250px;
            position: absolute; /* Add absolute positioning to div3 */
            top: 50%; /* Position div3 relative to the bottom of div2 */
            left: 461px; /* Align div3 with the left edge of the container */
            text-align: center;
        }
        .div3 h3{
            padding: 0;
            background-color: #4CAF50;
            color: rgb(0, 0, 0);
            height: 20px;
            width: 100%;
            margin-top: 0px;
            text-align: center;
            align-items: center;
        }
        .div3 h1{
            color: rgb(0, 0, 0);
            margin-top: -20px;
            background-color: #4CAF50;
            width: 100%;
            align-self: center;
            height: auto;
        }
        .div3 table{
            background-color: white;
            margin-left: 55px;
            height: auto;
            text-align: center; /* Center align the text within the table */
            margin-top: -10px;
            border-collapse: collapse;
            width: 190px;
        }
        .div3 th {
            background-color: #4CAF50;
            color: rgb(8, 8, 8);
            font-weight: normal;
            text-align: left;
            padding: 3px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        .div3 tr {
            background-color: white;
        }
        .div3 td {
            padding: 3px;
            border-bottom: 1px solid #ddd;
        }

        .div4 {
            overflow: hidden;
            background-color: rgba(0, 0, 0, 0.301);
            padding: 0;
            border-radius: 10px;
            width: 300px;
            height: 250px;
            align-items: center;
        }

        .div4 h2{
            padding: 0;
            background-color: #4CAF50;
            color: rgb(0, 0, 0);
            height: 30px;
            width: 100%;
            margin-top: 0px;
            text-align: center;
            align-items: center;
        }

        .div4 table{
            background-color: white;
            margin-left: 89px;
            height: auto;
            text-align: center; /* Center align the text within the table */
            margin-top: 0px;
            border-collapse: collapse;
            width: 120px;
        }
        .div4 th {
            background-color: #4CAF50;
            color: rgb(8, 8, 8);
            font-weight: normal;
            text-align: left;
            padding: 3px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        .div4 tr {
            background-color: white;
        }
        .div4 td {
            
            padding: 3px;
            border-bottom: 1px solid #ddd;
        }

        .div2 a {
            text-decoration: none;
            border: 1px solid #0f0f0f;
            border-radius: 4px;
            color: #080808;
            background-color: #4CAF50;
            padding: 4px 8px;
            transition: background-color 0.3s, color 0.3s;
            margin-top: -10px;
            margin-left: 110px;
        }

        .div2 a:hover {
            background-color: #3d3939;
            color: #c5c3c3;
        }

        .table3{
            margin-top: -15px;
        }
        .table4{
            margin-top: 0px;
            margin-bottom: 15px;
        }
        .table5{
            margin-top: 12px;
            margin-bottom: 15px;
        }
        .table6{
            margin-top: 12px;
            margin-bottom: 30px;
        }

    </style>
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
    <div class="diva">
    <?php 
       // Get current date
       $current_date = date('Y-m-d');
       
       // Orders delivered today
       $sql_delivered_today = "SELECT COUNT(DISTINCT order_number) AS delivered_today 
       FROM orders 
       WHERE DATE(Ordered_date_time) = ? 
       AND Order_status IN ('delivered', 'received')";
       $stmt_delivered_today = $conn->prepare($sql_delivered_today);
       $stmt_delivered_today->execute([$current_date]);
       $result_delivered_today = $stmt_delivered_today->fetch(PDO::FETCH_ASSOC);
       $delivered_today = 0;
       
       if ($result_delivered_today) {
           $delivered_today = $result_delivered_today['delivered_today'];
       }
       
       // Total orders for today
       $sql_total_orders_today = "SELECT COUNT(DISTINCT order_number) AS total_orders_today FROM orders WHERE DATE(Ordered_date_time) = ?";
       $stmt_total_orders_today = $conn->prepare($sql_total_orders_today);
       $stmt_total_orders_today->execute([$current_date]);
       $result_total_orders_today = $stmt_total_orders_today->fetch(PDO::FETCH_ASSOC);
       $total_orders_today = 0;
       
       if ($result_total_orders_today) {
           $total_orders_today = $result_total_orders_today['total_orders_today'];
       }
       
       // Total sales for today
       $sql_total_sales_today = "SELECT SUM(Total_amount) AS total_sales_today 
       FROM orders 
       WHERE DATE(Ordered_date_time) = ? 
       AND Order_status IN ('delivered', 'received')";
       $stmt_total_sales_today = $conn->prepare($sql_total_sales_today);
       $stmt_total_sales_today->execute([$current_date]);
       $result_total_sales_today = $stmt_total_sales_today->fetch(PDO::FETCH_ASSOC);
       $total_sales_today = 0;
       
       if ($result_total_sales_today) {
           $total_sales_today = $result_total_sales_today['total_sales_today'];
       }

       $sql_canceled_orders_today = "SELECT COUNT(DISTINCT order_number) AS canceled_orders_today FROM orders WHERE DATE(Ordered_date_time) = ? AND Order_status = 'cancelled'";
        $stmt_canceled_orders_today = $conn->prepare($sql_canceled_orders_today);
        $stmt_canceled_orders_today->execute([$current_date]);
        $result_canceled_orders_today = $stmt_canceled_orders_today->fetch(PDO::FETCH_ASSOC);
        $canceled_orders_today = 0;

        if ($result_canceled_orders_today) {
            $canceled_orders_today = $result_canceled_orders_today['canceled_orders_today'];
        }
       
       echo "<div class='div1'>";
       echo "<h2>Orders Today</h2>";
       echo "<table class='table1'>";
       echo "<th>Delivered</th> <tr><td>".$delivered_today."</td></tr>";
       echo "</table>";
       echo "<table class='table2'>";
       echo "<th>Orders</th><tr><td>".$total_orders_today."</td></tr>";
       echo "</table>";
       echo "<table class='table5'>";
       echo "<th>Total Sales</th><tr><td>₱".$total_sales_today."</td></tr>";
       echo "</table>";
       echo "<table class='table6'>";
       echo "<th>Canceled</th><tr><td>".$canceled_orders_today."</td></tr>";
       echo "</table>";
       echo "</div>";

        // Get current month and last month in 'YYYY-MM' format
        $current_month = date('Y-m');
        $last_month = date('Y-m', strtotime('-1 month'));

        // Monthly sale Report for current and last month
        $sql_order_trends = "SELECT 
        DATE_FORMAT(Ordered_date_time, '%Y-%m') AS month_year,
        DATE_FORMAT(Ordered_date_time, '%M') AS month_name,
        SUM(CASE WHEN Order_status IN ('delivered', 'received') THEN Total_amount ELSE 0 END) AS monthly_sales
        FROM orders 
        WHERE DATE_FORMAT(Ordered_date_time, '%Y-%m') IN (?, ?)
        GROUP BY DATE_FORMAT(Ordered_date_time, '%Y-%m')
        ORDER BY Ordered_date_time";

        $stmt_order_trends = $conn->prepare($sql_order_trends);
        $stmt_order_trends->execute([$current_month, $last_month]);
        $result_order_trends = $stmt_order_trends->fetchAll(PDO::FETCH_ASSOC);

        $current_month_sales = [
            'month_name' => date('F'),
            'monthly_sales' => 0
        ];
    
        $last_month_sales = [
            'month_name' => date('F', strtotime('-1 month')),
            'monthly_sales' => 0
        ];
    
        if ($result_order_trends) {
            foreach ($result_order_trends as $row) {
                if ($row['month_year'] == $current_month) {
                    $current_month_sales['monthly_sales'] = $row['monthly_sales'];
                } elseif ($row['month_year'] == $last_month) {
                    $last_month_sales['monthly_sales'] = $row['monthly_sales'];
                }
            }
        }
        $currentMonth11 = date('m');
        $current_year11 = date('Y');
    
    
        // Query to find the most bought item of the current month
        $sql11 = "SELECT Item_name
        FROM item
        WHERE Item_ID = (
        SELECT Item_ID
        FROM orders
        WHERE DATE(Ordered_date_time) = CURDATE() AND Order_status IN ('delivered', 'received')
        GROUP BY Item_ID
        ORDER BY SUM(Quantity) DESC
        LIMIT 1
        )";
            $stmt11 = $conn->prepare($sql11);
            $stmt11->execute();
            $result11 = $stmt11->fetch(PDO::FETCH_ASSOC);
        
            echo "<div class='div2'>";
            echo "<h2>Sales Report</h2>";
        
            echo "<table class='table3'>";
            echo "<caption>Current Month</caption>";
            echo "<th>".$current_month_sales['month_name']."</th>";
            echo "<tr>";
            echo "<td>₱".$current_month_sales['monthly_sales']."</td>";
            echo "</tr>";
            echo "</table>";
        
            echo "<table class='table4'>";
            echo "<caption>Last Month</caption>";
            echo "<th>".$last_month_sales['month_name']."</th>";
            echo "<tr>";
            echo "<td>₱".$last_month_sales['monthly_sales']."</td>";
            echo "</tr>";
            echo "</table>";
            echo "<div><a href='adminsales.php'>see more</a></div>";
            echo "</div>";
        
            $stmt = $conn->prepare("SELECT item.Item_name, 
            SUM(CASE WHEN orders.Order_status IN ('delivered', 'received') THEN orders.quantity ELSE 0 END) AS total_quantity
            FROM item
            JOIN orders ON item.Item_ID = orders.Item_ID
            WHERE MONTH(orders.Ordered_date_time) = ? 
            AND YEAR(orders.Ordered_date_time) = ?
            GROUP BY item.Item_name
            ORDER BY total_quantity DESC");

            $stmt->execute([$currentMonth11, $current_year11]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
            echo "<div class='div3'>";
        
            if ($result11) {
                // Output the name of the most bought item of the month
                echo "<h3>most bought item Today</h3><h1>". $result11["Item_name"]."</h1>";
            } else {
                echo "<h3>most bought item Today:</h3><h1>No orders yet.</h1>";
            }
            $count = 1;
            if ($result) {
                echo "<table><th>Fruit</th><th>Total</th>
                <caption><strong style='color: white;'>This Month</strong></caption>";
        
                foreach ($result as $row) {
                    echo "<tr><td> ".$count++.".". $row["Item_name"]. "</td><td>" . $row["total_quantity"]. "</td></tr>";
                }
                echo "</table>";
            } else {
                echo "0 results";
            }
            echo "</div>";
            ?>
            <div class="div4">
                <table>
                    <h2>Users</h2>
                    <tr>
                        <th>User Type</th>
                        <th>Count</th>
                    </tr>
                    <?php
                    $stmt = $conn->prepare("SELECT user_type, COUNT(*) as count FROM users GROUP BY user_type");
                    $stmt->execute();
                    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    if ($users) {
                        foreach ($users as $row) {
                            echo "<tr><td>" . htmlspecialchars($row["user_type"]) . "</td><td>" . $row["count"] . "</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='2'>No results found</td></tr>";
                    }
                    ?>
                </table>
            </div>
        </div>
        </body>
</html>
<?php
$conn = null; // Close the database connection
?>        
