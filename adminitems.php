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

    // Fetch items
    $sql = "SELECT * FROM item";
    $itemStmt = $conn->query($sql);
    $itemResult = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch boxes
    $sqlBoxes = "SELECT ih.boxname, i.Item_name FROM item_history ih INNER JOIN item i ON ih.Item_ID = i.Item_ID
    WHERE ih.date_expiration IS NULL OR ih.date_expiration > NOW()";
    $boxStmt = $conn->query($sqlBoxes);
    $boxResult = $boxStmt->fetchAll(PDO::FETCH_ASSOC);


    // Fetch items expiring within the current week
    $currentDate = date('Y-m-d');
    $endOfWeek = date('Y-m-d', strtotime('next Monday'));
    $sqlExpiringItems = "SELECT ih.boxname, i.Item_name, ih.date_expiration FROM item_history ih INNER JOIN item i ON ih.Item_ID = i.Item_ID WHERE ih.date_expiration BETWEEN :currentDate AND :endOfWeek";
    $expiringStmt = $conn->prepare($sqlExpiringItems);
    $expiringStmt->bindParam(':currentDate', $currentDate);
    $expiringStmt->bindParam(':endOfWeek', $endOfWeek);
    $expiringStmt->execute();
    $expiringItemsResult = $expiringStmt->fetchAll(PDO::FETCH_ASSOC);

    
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Stock</title>
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

        h2 {
            margin-left: 30%;
            color: #333;
        }
        .form-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 20px;
        }
        form {
            background-color: white;
            padding: 20px;
            margin: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            margin-left: 30%;
            align-items: center;
            text-align:center;
        }
        form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        form input, form select {
            width: 60%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        form input[type="submit"] {
            margin: 10px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: black;
            text-decoration: none;
            border-radius: 5px;
            border: 1px solid #333;
            text-align: center;
            cursor: pointer;
        }
        form input[type="submit"]:hover {
            background-color: #333;
            color: #fff;
        }
        table {
            width: 80%;
            border-collapse: collapse;
            margin: 20px auto;
        }
        table caption {
            margin-bottom: 10px;
            font-weight: bold;
        }
        table, th, td {
            border: 1px solid #ddd;
            background-color: white;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
        }
        td img {
            width: 50px;
            height: auto;
            display: block;
        }
        .form-button {
            display: inline-block;
            margin: 10px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: black;
            text-decoration: none;
            border-radius: 5px;
            border: 1px solid #333;
            text-align: center;
            cursor: pointer;
        }
        .form-button:hover {
            background-color: #333;
            color: #fff;
        }
        .hidden {
            display: none;
        }
        .visible {
            display: block;
        }
        .button1{
            height: 60px;
            width: 55%;
            margin-left: 31%;
        }
        form input[type="number"] {
            width: 20%;
        }
        .radio-inline {
            display: inline-block;
            margin-right: 10px;
        }
        .close-button{
            margin-left: 95%;
            background-color: #4CAF50;
            border-radius: 5px;
            cursor: pointer;
            border: 1px solid #333;
            color: black;
        }
        .close-button:hover{
            background-color: #333;
            color: #fff;
        }
        .notification {
            background-color: #4CAF50;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            text-align: center;

        }

        .notification h3 {
            margin-top: 0;
            color: #333;
        }

        .notification ul {
            list-style-type: none;
            padding: 0;
        }

        .notification ul li {
            margin-bottom: 5px;
            color: black;
        }
        .close1{
            margin-left: 45%;
            background-color: #4CAF50;
            border-radius: 5px;
            cursor: pointer;
            border: 1px solid #333;
            color: black;
            text-decoration: none;
        }
        .close1:hover{
            background-color: #333;
            color: white;
        }
        
    </style>
    <script>
        function toggleForm(formId) {
            var formSections = document.querySelectorAll('.form-section');
                formSections.forEach(function(form) {
                    if (form.id === formId) {
                        if (form.classList.contains('hidden')) {
                            form.classList.remove('hidden');
                        } else {
                            form.classList.add('hidden');
                        }
                    } else {
                        form.classList.add('hidden');
                    }
                });
            }
            function hideDiv(button) {
                var div = button.closest('.form-section, notification');
                div.classList.add('hidden');
            }
            function hideDiv1(button) {
                var div = button.closest('.notification');
                div.style.display = 'none';
            }
            function toggleExpiredItems() {
            var expiredItemsDiv = document.getElementById('expiredItems');
            if (expiredItemsDiv.style.display === 'none') {
                expiredItemsDiv.style.display = 'block';
            } else {
                expiredItemsDiv.style.display = 'none';
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
    <div id="notif" class="notification">
        <button type="button" class="close-button" onclick="hideDiv1(this)">X</button>
        <h3>Expiring Items within this week:</h3>
        <ul>
        <?php if (!empty($expiringItemsResult)): ?>
            <?php foreach ($expiringItemsResult as $row): ?>
                <li><?php echo $row['Item_name'] . " in BOX " . $row['boxname'] . " will expire on " . $row['date_expiration']; ?></li>
            <?php endforeach; ?>
        <?php else: ?>
            <li>No items are expiring within this week.</li>
        <?php endif; ?>
        </ul>
    </div>
    <div class="button1">
        <div class="form-button" onclick="toggleForm('addStockForm')">Add Box</div>
        <div class="form-button" onclick="toggleForm('removeStockForm')">Edit Stock</div>
        <div class="form-button" onclick="toggleForm('addItemForm')">Add Item</div>
        <div class="form-button" onclick="toggleForm('editPriceForm')">Edit Item Price</div>
    </div>
    <div id="addStockForm" class="form-section hidden">
        <h2>Add Box</h2>
        <form action="addstock.php" method="post">
        <button type="button" class="close-button" onclick="hideDiv(this)">X</button>
            <label for="item_id">Item:</label>
            <select id="item_id" name="item_id">
                <?php if (!empty($itemResult)): ?>
                    <?php foreach ($itemResult as $row): ?>
                        <option value="<?php echo $row['Item_ID']; ?>"><?php echo $row['Item_name']; ?></option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="">No items available</option>
                <?php endif; ?>
            </select>
            <br>
            <label for="box_name">Box Name:</label>
            <input type="text" id="box_name" name="box_name" required>
            <br>
            <label for="expiration_date">Expiration Date:</label>
            <input type="date" id="expiration_date" name="expiration_date" required>
            <br>
            <label for="stock">Stock to Add:</label>
            <input type="number" id="stock" name="stock" required>
            <br><br>
            <input type="submit" value="Add Stock">
        </form>
    </div>
    <div id="removeStockForm" class="form-section hidden">
        <h2>Edit Stock</h2>
        <form action="Leditstock.php" method="post">
        <button type="button" class="close-button" onclick="hideDiv(this)">X</button>
            <label for="box_name">Box Name:</label>
            <select id="box_name" name="box_name">
                <?php if (!empty($boxResult)): ?>
                    <?php foreach ($boxResult as $row): ?>
                        <option value="<?php echo $row['boxname']; ?>"><?php echo $row['Item_name'] . " - " . $row['boxname']; ?></option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="">No boxes available</option>
                <?php endif; ?>
            </select>
            <br>
            <label for="edit_quantity">Quantity to Edit:</label>
            <input type="number" id="edit_quantity" name="edit_quantity" required>
            <br>
            <div class="radio-inline">
                <input type="radio" id="add" name="operation" value="add" required>
                <label for="add">Add</label>
            </div>
            <div class="radio-inline">
                <input type="radio" id="subtract" name="operation" value="subtract" required>
                <label for="subtract">Subtract</label>
            </div>
            <br>
            <input type="submit" value="Edit Stock">
        </form>
    </div>
    <div id="addItemForm" class="form-section hidden">
        <h2>Add Item</h2>
        <form method="POST" action="additem.php" enctype="multipart/form-data">
        <button type="button" class="close-button" onclick="hideDiv(this)">X</button>
            <label>Item Name</label>
            <br>
            <input type="text" name="Iname" required>
            <br>
            <label>Item Price</label>
            <br>
            <input type="text" name="Iprice" required>
            <br>
            <label for="fileToUpload">Item image:</label><br>
            <input type="file" name="fileToUpload" id="fileToUpload" required><br>
            <br>
            <input type="submit" value="Add Item">
        </form>
    </div>
    <div id="editPriceForm" class="form-section hidden">
        <h2>Edit Item Price</h2>
        <form action="Ledititem.php" method="post" enctype="multipart/form-data">
            <button type="button" class="close-button" onclick="hideDiv(this)">X</button>
            <label for="item_id">Item:</label>
            <select id="item_id" name="item_id">
            <?php if (!empty($itemResult)): ?>
                <?php foreach ($itemResult as $row): ?>
                    <option value="<?php echo $row['Item_ID']; ?>"><?php echo $row['Item_name']; ?></option>
                <?php endforeach; ?>
            <?php else: ?>
                <option value="">No items available</option>
            <?php endif; ?>
            </select>
            <br>
            <label for="new_price">New Price:</label>
            <input type="number" id="new_price" name="new_price" required>
            <br><br>
            <input type="submit" value="Update Price">
        </form>
    </div>
    <table>
        <caption> Items Details</caption>
        <tr>
            <th>Item Image</th>
            <th>Item Name</th>
            <th>Item Price</th>
            <th>Item Stocks</th>
        </tr>
        <?php
             $sql = " SELECT 
                 item.Item_ID, 
                 item.Item_name, 
                 item.Item_price, 
                 item.Item_image, 
                 item.Item_imagename,
                 IFNULL(SUM(item_history.remaining_quantity), 0) AS Item_stocks
             FROM 
                 item
             LEFT JOIN 
                 item_history ON item.Item_ID = item_history.item_id
                 AND (item_history.date_expiration IS NULL OR item_history.date_expiration > NOW())
             GROUP BY 
                 item.Item_ID, item.Item_name, item.Item_price, item.Item_image, item.Item_imagename
             ORDER BY 
                 item.Item_ID ASC
         ";
     
         $result = $conn->query($sql);
     
         if ($result->rowCount() > 0) {
             while ($row = $result->fetch()) {
                 echo "<tr>";
                 echo "<td><img src='data:image/jpeg;base64," . base64_encode($row['Item_image']) . "' alt='Item Image' class='item-image'/></td>";
                 echo "<td>" . htmlspecialchars($row["Item_name"]) . "</td>";
                 echo "<td>₱" . htmlspecialchars($row["Item_price"]) . "</td>";
                 echo "<td>" . htmlspecialchars($row["Item_stocks"]) . "</td>";
                 echo "</tr>";
                 error_log("Fetched row: " . print_r($row, true));
             }
         } else {
             echo "<tr><td colspan='4'>0 results</td></tr>";
         }
        ?>
    </table>
    <?php
        $sqlCurrentItems = "SELECT ih.*, i.Item_name FROM item_history ih INNER JOIN item i ON ih.Item_ID = i.Item_ID WHERE ih.date_expiration > NOW()";
        $resultCurrentItems = $conn->query($sqlCurrentItems);
        $currentItems = $resultCurrentItems->fetchAll(PDO::FETCH_ASSOC);

        $sqlExpiredItems = "SELECT ih.*, i.Item_name FROM item_history ih INNER JOIN item i ON ih.Item_ID = i.Item_ID WHERE ih.date_expiration <= NOW()";
        $resultExpiredItems = $conn->query($sqlExpiredItems);
        $expiredItems = $resultExpiredItems->fetchAll(PDO::FETCH_ASSOC);
        ?>
    <a href="#expiredItems" onclick="toggleExpiredItems()" class="close1">Go to Expired Items</a>
    <table>
        <caption>Current Box Details</caption>
        <tr>
            <th>Box Name</th>
            <th>Item Name</th>
            <th>Remaining Quantity</th>
            <th>Quantity</th>
            <th>Date Expiration</th>
            <th>Date Added</th>
            <th>Date Updated</th>
        </tr>
            <?php if (!empty($currentItems)): ?>
                <?php foreach ($currentItems as $row): ?>
                    <tr>
                        <td><?php echo $row["boxname"]; ?></td>
                        <td><?php echo $row["Item_name"]; ?></td>
                        <td><?php echo $row["remaining_quantity"]; ?></td>
                        <td><?php echo $row["quantity"]; ?></td>
                        <td><?php echo $row["date_expiration"]; ?></td>
                        <td><?php echo $row["date_added"]; ?></td>
                        <td><?php echo $row["date_updated"]; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7">No current items</td></tr>
            <?php endif; ?>
    </table>
    <div id="expiredItems" style="display: none;">
    <table>
        <caption>Expired Items</caption>
        <tr>
            <th>Box Name</th>
            <th>Item Name</th>
            <th>Remaining Quantity</th>
            <th>Quantity</th>
            <th>Date Expiration</th>
            <th>Date Added</th>
            <th>Date Updated</th>
        </tr>
        <?php if (!empty($expiredItems)): ?>
            <?php foreach ($expiredItems as $row): ?>
                <tr>
                    <td><?php echo $row["boxname"]; ?></td>
                    <td><?php echo $row["Item_name"]; ?></td>
                    <td><?php echo $row["remaining_quantity"]; ?></td>
                    <td><?php echo $row["quantity"]; ?></td>
                    <td><?php echo $row["date_expiration"]; ?></td>
                    <td><?php echo $row["date_added"]; ?></td>
                    <td><?php echo $row["date_updated"]; ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="7">No expired items</td></tr>
        <?php endif; ?>
    </table>
</div>
</body>
</html>

<?php
$conn = null;
?>
