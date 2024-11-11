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
        $_SESSION['fname'] = $result['fname'];
        $_SESSION['lname'] = $result['lname'];
    }

    $fname = $_SESSION['fname'];
    $lname = $_SESSION['lname'];
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Welcome to Farm Fruits</title>
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
            .item-image {
                width: 100px;
                height: auto;
            }
            .intro {
                background-color: rgba(255, 255, 255, 0); /* Semi-transparent white background */
                padding: 40px;
                margin: 50px auto; /* Center the intro section */
                max-width: 800px; /* Limit width for better readability */
                border-radius: 20px;
                text-align: center;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Add shadow */
            }

            .intro h1 {
                font-size: 60px;
                margin-bottom: 20px;
                color: #046114;/* Added color */
            }

            .intro p {
                margin-top: 50px;
                font-size: 20px;
                margin-bottom: 30px; /* Increased margin bottom */
                color: black; /* Added color */

            }

            .intro button {
                padding: 15px 30px; /* Increased padding */
                background-color: #4CAF50;
                border: none;
                border-radius: 30px; /* Increased border radius */
                color: white;
                font-size: 18px;
                cursor: pointer;
                transition: background-color 0.3s;
                text-decoration: none; /* Remove underline */
            }

            .intro button:hover {
                background-color: #45a049;
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

        <div class="intro">
        <h1>Welcome to Farm Fruit Store</h1>
        <p><b>Our fruit is fresh straight from the farm, ensuring the highest quality and taste.
                <br>Delivery Available from 8 am to 5 pm.</b></p>
        <button><a href="order.php" style="text-decoration: none; color: inherit;">Order Now</a></button>
    </div>
    </body>
</html>
<?php 
$conn = null;
?>