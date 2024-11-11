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

// Retrieve cart items for the logged-in user
$sql = "SELECT item.Item_ID, item.Item_name, cart.quantity, item.Item_price, 
(cart.quantity * item.Item_price) AS total_price
        FROM cart
        INNER JOIN item ON cart.ItemID = item.Item_ID
        WHERE cart.userID=?";
$stmt = $conn->prepare($sql);
$stmt->execute([$id]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$cart_items = [];

$total_price = 0; // Initialize overall price

if ($result) {
    foreach ($result as $row) {
        $cart_items[] = $row;
        $total_price += $row['total_price']; // Accumulate total price
    }
}

$sql = "SELECT * FROM users WHERE user_id=?";
$stmt = $conn->prepare($sql);
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    $_SESSION['U_id'] = $row['user_id'];
    $_SESSION['U_type'] = $row['user_type'];
    $_SESSION['fname'] = $row['fname'];
    $_SESSION['lname'] = $row['lname'];
    $_SESSION['brgy'] = $row['barangay'];
    $_SESSION['st'] = $row['street'];
    $_SESSION['hn'] = $row['house_number'];
}

$fname = $_SESSION['fname'];
$lname = $_SESSION['lname'];
$B = $_SESSION['brgy'];
$S = $_SESSION['st'];
$HN = $_SESSION['hn'];

$conn = null; // Close the connection
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cart</title>
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
        .cart-container {
            margin-top: 2%;
            background-color: #ffffffaa;
            padding: 20px;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
            border-radius: 40px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        #gcash-details {
            display: none;
            background-color: #007bff; /* GCash primary color */
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            margin-top: 20px;
            color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 25%;
        }

        #gcash-details h3 {
            color: white;
            margin-bottom: 20px;
        }

        #gcash-details label {
            display: block;
            margin-bottom: 10px;
            color: white;
        }

        #gcash-details input[type="text"],
        #gcash-details input[type="number"] {
            width: calc(100% - 24px);
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        #gcash-details button {
            background-color: #0056b3; /* GCash button color */
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        #gcash-details button:hover {
            background-color: #004494; /* GCash button hover color */
        }

        #gcash-error {
            color: red;
            display: none;
            margin-top: 10px;
        }

        button:disabled {
            background-color: grey;
            cursor: not-allowed;
        }

    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paymentMethodSelect = document.getElementById('payment_method');
            const gcashDetailsDiv = document.getElementById('gcash-details');
            const gcashDoneBtn = document.getElementById('gcash-done-btn');
            const gcashAmountInput = document.getElementById('gcash_amount');
            const overallPrice = <?php echo json_encode(number_format($total_price, 2)); ?>;
            const gcashError = document.getElementById('gcash-error');
            const submitButton = document.getElementById('submit-button');

            paymentMethodSelect.addEventListener('change', function() {
                if (this.value === 'gcash') {
                    gcashDetailsDiv.style.display = 'block';
                    submitButton.disabled = true; // Disable submit button when GCash is selected
                } else {
                    gcashDetailsDiv.style.display = 'none';
                    submitButton.disabled = false; // Enable submit button for other payment methods
                }
            });

            gcashDoneBtn.addEventListener('click', function() {
                const enteredAmount = parseFloat(gcashAmountInput.value);
                const formattedOverallPrice = parseFloat(overallPrice.replace(/,/g, ''));

                if (enteredAmount === formattedOverallPrice) {
                    gcashDetailsDiv.style.display = 'none';
                    gcashError.style.display = 'none';
                    submitButton.disabled = false;
                } else {
                    gcashError.style.display = 'block';
                    submitButton.disabled = true; // Keep submit button disabled if amount is incorrect
                }
            });
        });
    </script>
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
    <div class="cart-container">
        <h2>Your Cart</h2>
        <table>
            <tr>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Item Price</th>
                <th>Total Price</th>
                <th>Action</th>
            </tr>
            <?php
            if (!empty($cart_items)) {
                foreach ($cart_items as $item) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($item['Item_name']) . "</td>";
                    echo "<td><form action='Lcart_actions.php' method='post'>
                    <input type='hidden' name='item_id' value='" . htmlspecialchars($item['Item_ID']) . "'/>
                    <input type='number' style='width: 40px;' name='new_quantity' value='" . htmlspecialchars($item['quantity']) . "' min='1'/>
                    <input type='submit' name='update_quantity' value='Update'/></form></td>";
                    echo "<td>PHP" . htmlspecialchars($item['Item_price']) . "</td>";
                    echo "<td>PHP" . htmlspecialchars($item['total_price']) . "</td>";
                    echo "<td><a href='Lcart_actions.php?remove=true&item_id=" . htmlspecialchars($item['Item_ID']) . "'>Remove</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>Your cart is empty.</td></tr>";
            }
            ?>
            <tr>
                <td colspan="3" align="right"><b>Overall Price:</b></td>
                <td><b>PHP <?php echo number_format($total_price, 2); ?></b></td>
                <td></td>
            </tr>
        </table>
        <label>Address:</label>
        <form action="Lcart_actions.php" method="post">
            <input type="text" name="B" value="<?php echo htmlspecialchars($B); ?>" placeholder="Barangay" required/>
            <input type="text" name="S" value="<?php echo htmlspecialchars($S); ?>" placeholder="Street" required/>
            <input type="text" name="HN" value="<?php echo htmlspecialchars($HN); ?>" placeholder="House number" required/><br>
            <label for="payment_method">Select Payment Method:</label>
            <select name="PM" id="payment_method" required>
                <option value="COD">Cash on Delivery</option>
                <option value="gcash">GCash</option>
            </select><br>
            <input type="hidden" name="submit_order"/>
            <button type="submit" id="submit-button">Submit Order</button>
        </form>
            <div id="gcash-details">
                <h3>GCash Payment</h3>
                <label for="gcash_number">Enter GCash Number:</label>
                <input type="text" id="gcash_number" name="gcash_number" placeholder="GCash Number" 
                required pattern="[0-9]{11}" title="Please enter 11 digits"/>
                <p>Overall Price: PHP <?php echo number_format($total_price, 2); ?></p>
                <label>Enter Amount:</label>
                <input type="number" id="gcash_amount" name="gcash_amount" placeholder="Amount" required/>
                <p>Receiver GCash Number: 09123456789</p>
                <p id="gcash-error">Amount must be equal to the overall price.</p>
                <button type="button" id="gcash-done-btn">Done</button>
            </div>
    </div>
</body>
</html>


