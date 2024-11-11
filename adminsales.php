<!DOCTYPE html>
<html>
<head>
    <title>Essential Report</title>
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
        .div5{
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.664);
            width: 90%;
            height: 80vh;
            margin-left: 68px;
            margin-top: 50px;
            border-radius: 10px;
        }

        .div5 h2{
            padding: 0;
            background-color: #4CAF50;
            color: rgb(0, 0, 0);
            height: 30px;
            width: 100%;
            margin-top: 0px;
            text-align: center;
            align-items: center;
        }

        .div5 table{
            background-color: white;
            margin-left: 21%;
            height: auto;
            text-align: center; /* Center align the text within the table */
            margin-top: 0px;
            border-collapse: collapse;
            width: 700px;
            margin-bottom: 20px;
        }
        .div5 th {
            background-color: #4CAF50;
            color: rgb(8, 8, 8);
            font-weight: normal;
            text-align: left;
            padding: 3px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        .div5 tr {
            background-color: white;
        }
        .div5 td {
            
            padding: 3px;
            border-bottom: 1px solid #ddd;
        }
        .div5 a {
            text-decoration: none;
            border: 1px solid #0f0f0f;
            border-radius: 4px;
            color: #080808;
            background-color: #4CAF50;
            padding: 8px 16px;
            transition: background-color 0.3s, color 0.3s;
            margin-top: 100px;
            margin-left: 43%;
        }

        .div5 a:hover {
            background-color: #3d3939;
            color: #c5c3c3;
        }
        canvas{
            background-color: white;
            width: 20%;
        }
        form{
            margin-left: 35%;
        }
    </style>
</head>
<body>
<nav class="nav1">
        <img src="styles/images/icon-fruits2.png" alt="fruits">
        <p><i>Farm Fruit Store<br>Online Delivery</i></p>
        <ul>
            <li><a href="adminhome.php">Dashboard</a></li>
            <li><a href="adminitems.php">Items</a></li>
            <li><a href="adminusers.php">UserDetails</a></li>
            <li><a href="adminorders.php">Orders</a></li>
            <li><a href="Llogout.php">Logout</a></li>
        </ul>
    </nav>
<?php
require "FruitSQLcon.php"; // Include your PDO connection script

// Get current year
$current_year = date('Y');

// Monthly sale Report for all months in current year, including most purchased item
$sql_order_trends = "SELECT 
DATE_FORMAT(o.Ordered_date_time, '%Y-%m') AS month_year,
DATE_FORMAT(o.Ordered_date_time, '%M') AS month_name,
COUNT(*) AS total_orders,
SUM(CASE WHEN o.Order_status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled_orders,
SUM(CASE WHEN o.Order_status = 'delivered' THEN 1 ELSE 0 END) AS delivered_orders,
SUM(CASE WHEN o.Order_status IN ('delivered', 'received') THEN o.Total_amount ELSE 0 END) AS monthly_sales,
(
    SELECT i.Item_name 
    FROM orders oi
    JOIN item i ON oi.Item_ID = i.Item_ID
    WHERE DATE_FORMAT(oi.Ordered_date_time, '%Y-%m') = month_year
    GROUP BY oi.Item_ID 
    ORDER BY SUM(oi.Quantity) DESC 
    LIMIT 1
) AS most_bought_item
FROM orders o
WHERE YEAR(o.Ordered_date_time) = ?
GROUP BY DATE_FORMAT(o.Ordered_date_time, '%Y-%m')
ORDER BY o.Ordered_date_time;";

$stmt = $conn->prepare($sql_order_trends);
$stmt->bindParam(1, $current_year);
$stmt->execute();
$result_order_trends = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<div class='div5'>";
echo "<h2>Sales Report for $current_year</h2>";
echo "<table>";
echo "<tr>";
echo "<th>Month</th>";
echo "<th>Total Orders</th>";
echo "<th>Monthly Sales</th>";
echo "<th>Most Bought Item</th>";
echo "<th>Canceled orders</th>";
echo "<th>Delivered order</th>";
echo "</tr>";

if ($result_order_trends) {
    foreach ($result_order_trends as $row) {
        echo "<tr>";
        echo "<td>".$row['month_name']."</td>";
        echo "<td>".$row['total_orders']."</td>";
        echo "<td>$".$row['monthly_sales']."</td>";
        echo "<td>".$row['most_bought_item']."</td>";
        echo "<td>".$row['cancelled_orders']."</td>";
        echo "<td>".$row['delivered_orders']."</td>";
        echo "</tr>";
    }
} else {
    echo "<tr>";
    echo "<td colspan='4'>No sales data available for $current_year</td>";
    echo "</tr>";
}

echo "</table>";
echo "<a href='gen_pdf.php' target='_blank'>Download_PDF</a>";
echo "</div>"; 

$sql_payment_option_trends = "SELECT 
DATE_FORMAT(Ordered_date_time, '%Y-%m') AS month_year,
DATE_FORMAT(Ordered_date_time, '%M') AS month_name,
paymentoption,
SUM(Total_amount) AS total_sales
FROM orders
WHERE YEAR(Ordered_date_time) = ?
GROUP BY DATE_FORMAT(Ordered_date_time, '%Y-%m'), paymentoption
ORDER BY Ordered_date_time;";

$stmt_payment_option = $conn->prepare($sql_payment_option_trends);
$stmt_payment_option->bindParam(1, $current_year);
$stmt_payment_option->execute();
$result_payment_option_trends = $stmt_payment_option->fetchAll(PDO::FETCH_ASSOC);

echo "<div class='div5'>";
echo "<h2>Monthly Sales by Payment Option for $current_year</h2>";
echo "<table>";
echo "<tr>";
echo "<th>Month</th>";
echo "<th>Payment Option</th>";
echo "<th>Total Sales</th>";
echo "</tr>";

if ($result_payment_option_trends) {
    foreach ($result_payment_option_trends as $row) {
        echo "<tr>";
        echo "<td>".$row['month_name']."</td>";
        echo "<td>".$row['paymentoption']."</td>";
        echo "<td>$".$row['total_sales']."</td>";
        echo "</tr>";
    }
} else {
    echo "<tr>";
    echo "<td colspan='3'>No sales data available for $current_year</td>";
    echo "</tr>";
}

echo "</table>";
echo "<a href='gen_pdf1.php' target='_blank'>Download_PDF</a>";
echo "</div>";

$current_date = date('Y-m-d');

// Check if a specific month is set, otherwise use the current month
if (isset($_POST['month']) && !empty($_POST['month'])) {
    $selected_month = $_POST['month'];
} else {
    $selected_month = date('Y-m');
}

// Fetch items that have expired and their remaining quantities for the selected month
$sql_expired_items = "SELECT 
    i.Item_name,
    ih.remaining_quantity,
    ih.date_expiration,
    ih.boxname
FROM 
    item_history ih
INNER JOIN 
    item i ON ih.item_id = i.Item_ID
WHERE 
    ih.date_expiration < :current_date
    AND DATE_FORMAT(ih.date_expiration, '%Y-%m') = :selected_month";

$stmt_expired_items = $conn->prepare($sql_expired_items);
$stmt_expired_items->bindParam(':current_date', $current_date);
$stmt_expired_items->bindParam(':selected_month', $selected_month);
$stmt_expired_items->execute();
$result_expired_items = $stmt_expired_items->fetchAll(PDO::FETCH_ASSOC);

echo "<div class='div5' id='expiredItems'>";
echo "<h2>Expired Items and Remaining Quantities</h2>";

// Month filter form
echo "<form method='post'>";
echo "<label for='month'>Select Month:</label>";
echo "<input type='month' id='month' name='month' value='".$selected_month."'>";
echo "<input type='submit' value='Filter'>";
echo "</form>";

echo "<table>";
echo "<tr>";
echo "<th>Item Name</th>";
echo "<th>Remaining Quantity</th>";
echo "<th>Date Expiration</th>";
echo "<th>Box Name</th>";
echo "</tr>";

if ($result_expired_items) {
    foreach ($result_expired_items as $row) {
        echo "<tr>";
        echo "<td>".$row['Item_name']."</td>";
        echo "<td>".$row['remaining_quantity']."</td>";
        echo "<td>".$row['date_expiration']."</td>";
        echo "<td>".$row['boxname']."</td>";
        echo "</tr>";
    }
} else {
    echo "<tr>";
    echo "<td colspan='4'>No expired items found for the selected month</td>";
    echo "</tr>";
}

echo "</table>";
echo "</div>";

?>
</body>
</html>

