<?php
require "FruitSQLcon.php";
session_start();
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
        .farm-fruit-div {
            background-color: #4CAF50;
            height: 70px;
            display: flex;
        }

        .farm-fruit-div p {
            font-style: italic;
            font-size: 20px;
            margin-top: 15px;
        }

        .farm-fruit-div a {
            text-decoration: none;
            color: #333;
            font-weight: bold;
            font-size: 20px;
            padding: 4px 10px;
            border: 1px solid #333;
            border-radius: 4px;
            transition: background-color 0.3s, color 0.3s;
            position: relative;
            left: 850px;
            width: 80px;
            height: 25px;
            margin-top: 19px;
            text-align: center;
            margin-right: 10px;
        }

        .farm-fruit-div a:hover {
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
            height: 150px;
            border-radius: 40px;
            margin-left: 8px;
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
        .a1{
            text-decoration: none;
            color: #333;
            font-weight: bold;
            font-size: 20px;
            padding: 4px 10px;
            border: 1px solid #333;
            border-radius: 4px;
            transition: background-color 0.3s, color 0.3s;
            position: relative;
            width: 80px;
            height: 25px;
            text-align: center;
            margin-left: 45%;
            margin-top: 10px;
            background-color: #4CAF50;
        }

        .a1:hover {
            background-color: #333;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="farm-fruit-div">
        <img src="styles/images/icon-fruits2.png" alt="fruits">
        <p><i>Farm Fruit Store<br>Online Delivery</i></p>
        <a href="seeitems.php">See items</a>
        <a href="FarmLogin.php">Login</a>
    </div>
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
                        echo "<input type='hidden' name='id' value='".$row["Item_ID"]."'>";
                        echo "<input type='hidden' name='price' value='".$row["Item_price"]."'>";
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
    <a href="javascript:void(0);" onclick="orderNow()" class="a1">Order Here</a>

<script>
    function orderNow() {
        // Check if user is logged in
        <?php if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) { ?>
            alert("You need to login first to order.");
            window.location.href = "FarmLogin.php";
        <?php }?>

    }
</script>
</body>
</html>
<?php 
$conn = null;
?>
