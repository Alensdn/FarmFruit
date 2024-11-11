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

// Retrieve user information
$sql = "SELECT * FROM users WHERE user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->execute([':user_id' => $id]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {
    $_SESSION['U_id'] = $result['user_id'];
    $_SESSION['U_type'] = $result['user_type'];
    $fname = $result['fname'];
    $lname = $result['lname'];
} else {
    // Handle case where user is not found
    header("Location: Farmlogin.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Menu</title>
    <style>
        body{
            margin: 0px;
            background-image: url('styles/images/fruit2.jpg');
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
            left: 26%;
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
        .div001{
            margin-top: 0%;
            display: grid;
            grid-template-columns: auto auto auto auto auto;
            background-color: #03030300;
            padding: 10px;
            gap: 1px;
            align-items: center;
            width: 90%;
            height: 50%;
            overflow: visible;
            margin-left: 3%;
        }
        .grid-item {
            background-color: white;
            border: 1px solid rgba(0, 0, 0, 0.8);
            padding: 20px;
            font-size: 20px;
            text-align: center;
            width: 200px;
            height: 220px;
            border-radius: 40px;
        }

        .grid-item img{
            width: 70px;
            height: 70px;
            object-fit: fill;
        }
        .grid-item p{
            font-size: 16px;
        } 
        .title1{
            margin-left: 45%;
            width: 100px;
            height: auto;
            margin-top: 10px;
        }
        .title1 span{
            font-style: italic;
            font-size: 50px; 
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
    <div class="title1">
        <span>Shop</span>
    </div>
    <div class="div001">
        <?php
            $sql = "SELECT item.*, COALESCE(SUM(CASE WHEN item_history.date_expiration > CURRENT_DATE 
            OR item_history.date_expiration IS NULL THEN item_history.remaining_quantity ELSE 0 END), 0) AS stock
            FROM item
            LEFT JOIN item_history ON item.Item_ID = item_history.item_id
            GROUP BY item.Item_ID";
           $result = $conn->query($sql);

           if ($result->rowCount() > 0) {
               while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                   echo "<div class='grid-item'>";
                   echo "<img src='data:image/jpeg;base64," . base64_encode($row['Item_image']) . "' alt='Item Image'/><br>";
                   echo "<label>".$row["Item_name"]."</label><br>";
                   echo "<label>PHP".$row["Item_price"]."</label><br>";
                   
                   if ($row['stock'] > 0) {
                       echo "<p>Stock: ".$row['stock']."</p>";
                       echo "<form method='POST' action='Laddtocart.php'>";
                       echo "<input type='hidden' name='id' value='".$row["Item_ID"]."'>";
                       echo "<input type='hidden' name='price' value='".$row["Item_price"]."'>";
                       echo "<p>Quantity: <input type='number' name='quantity' style='width: 40px;' pattern='\\d{3}' id='quantity' min='1' required></p>";
                       echo "<button type='submit' name='addToCart'>Add to Cart</button>";
                       echo "</form>";
                   } else {
                       echo "<p>Unavailable</p>";
                   }
                   
                   echo "</div>";
               }
           } else {
               echo "0 results";
           }
        ?>
    </div>

    <script>
    document.getElementById('quantity').addEventListener('input', function (e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
    </script>
</body>
</html>
<?php 
$conn = null;
?>
