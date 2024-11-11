<?php
    require "FruitSQLcon.php"; 
    session_start();
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
    <title>Customer Information</title>
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
            color: #333;
            text-align: center;
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
            margin-left: 29%;
        }

        .form-flex {
            display: flex;
            gap: 20px;
        }

        .form-column {
            flex: 1;
        }

        form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        form input, form select {
            width: 100%;
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
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        table caption {
            margin-bottom: 10px;
            font-weight: bold;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
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
            margin-left: 45%;
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
        .pagination {
            text-align: center;
            margin-top: 20px;
        }
        .pagination button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            margin: 5px;
            cursor: pointer;
        }
        .pagination button:hover {
            background-color: #45a049;
        }
        .hidden {
            display: none;
        }
        .visible {
            display: block;
        }
        .scrollable-table {
            max-height: 400px; /* Adjust the height as needed */
            overflow-y: auto;
            background-color: white;
            width: 80%;
            margin-left: 10%;
            border-radius: 10px;
        }
        .scrollable table {
            width: 100%;
            border-collapse: collapse;
        }
        .scrollable th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .scrollable th {
            background-color: #4CAF50;
        }
        .scrollable-table2 {
            height: 300px; /* Adjust the height as needed */
            overflow-y: auto;
            background-color: white;
            width: 80%;
            margin-left: 10%;
            border-radius: 10px;
            background-color: #00000000;
        }
        .scrollable2 table {
            width: 100%;
            border-collapse: collapse;
        }
        .scrollable2 th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .scrollable2 th {
            background-color: #4CAF50;
        }
    </style>
    <script>
        function toggleAuditTrail() {
            var auditTrailDiv = document.getElementById('auditTrailDiv');
            auditTrailDiv.classList.toggle('hidden');
        }
        function toggleForm(formId) {
            var form = document.getElementById(formId);
            if (form.classList.contains('hidden')) {
                form.classList.remove('hidden');
                form.classList.add('visible');
            } else {
                form.classList.remove('visible');
                form.classList.add('hidden');
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
    <button class="form-button" onclick="toggleForm('add-worker-form')">Add Employee</button>
    <div id="add-worker-form" class="hidden">
        <form method="POST" action="addemployee.php">
            <?php if ($type === 'admin'): ?>
            <h2>Add Employee</h2>
            <?php elseif ($type === 'manager'): ?>
                <h2>Add Rider</h2>
            <?php endif; ?>
            <div class="form-flex">
                <div class="form-column">
                    <label for="Fname">First Name</label>
                    <input type="text" id="Fname" name="Fname" pattern="[A-Za-z]+" required title="Please enter letters only">
                    <label for="Uname">Username</label>
                    <input type="text" id="Uname" name="Uname" pattern=".{6,}" title="Username must be at least 6 characters long" required>
                    <label for="Pass">Password</label>
                    <input type="password" id="Pass" name="Pass" placeholder="Password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Password must be at least 8 characters long and contain one uppercase letter, one lowercase letter, one digit and one special character" required>
                </div>
                <div class="form-column">
                    <label for="Lname">Last Name</label>
                    <input type="text" id="Lname" name="Lname" pattern="[A-Za-z]+" required title="Please enter letters only">
                    <label for="Email">Email</label>
                    <input type="email" id="Email" name="Email" required>
                    <label for="Pnum">Phone Number</label>
                    <input type="text" id="Pnum" name="Pnum" pattern="09\d{9}" title="Please enter a 11-digit phone number starting with '09'" required>
                    <br>
                    <?php if ($type === 'admin'): ?>
                    <select id="position" name="position" required>
                        <option value="manager">Manager</option>
                        <option value="rider">Rider</option>
                    </select>
                    <?php elseif ($type === 'manager'): ?>
                    <input type="hidden" name="position" value="rider">
                    <?php endif; ?>
                    <input type="submit" value="Submit">
                </div>
            </div>
        </form>
    </div>
    <div id="otherTableDiv" class="scrollable-table2">
        <table>
            <caption>Customers Details</caption>
            <tr>
                <th>User Id</th>
                <th>User Name</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Barangay</th>
                <th>Street</th>
                <th>House Number</th>
            </tr>
            <?php
                $stmt = $conn->prepare("SELECT * FROM users WHERE user_type = 'customer'");
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (count($result) > 0) {
                    foreach ($result as $row) {
                        echo "<tr>";
                        echo "<td>" . $row["user_id"] . "</td>";
                        echo "<td>" . $row["username"] . "</td>";
                        echo "<td>" . $row["fname"] . "</td>";
                        echo "<td>" . $row["lname"] . "</td>";
                        echo "<td>" . $row["email"] . "</td>";
                        echo "<td>" . $row["phone_number"] . "</td>";
                        echo "<td>" . $row["barangay"] . "</td>";
                        echo "<td>" . $row["street"] . "</td>";
                        echo "<td>" . $row["house_number"] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='9'>0 results</td></tr>";
                }
            ?>
        </table>
    </div>
    <div id="otherTableDiv" class="scrollable-table2">
        <table>
            <caption>Rider Details</caption>
            <tr>
                <th>User Id</th>
                <th>User Name</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Phone Number</th>
            </tr>
            <?php
                $stmt = $conn->prepare("SELECT * FROM users WHERE user_type NOT IN ('admin', 'customer','manager')");
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (count($result) > 0) {
                    foreach ($result as $row) {
                        echo "<tr>";
                        echo "<td>" . $row["user_id"] . "</td>";
                        echo "<td>" . $row["username"] . "</td>";
                        echo "<td>" . $row["fname"] . "</td>";
                        echo "<td>" . $row["lname"] . "</td>";
                        echo "<td>" . $row["email"] . "</td>";
                        echo "<td>" . $row["phone_number"] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>0 results</td></tr>";
                }
            ?>
        </table>
    </div>
    <div id="auditTrailDiv" class="scrollable-table">
    <form method="GET" action="">
        <div class="form-flex">
            <div class="form-column">
                <label for="user_id">User ID</label>
                <input type="text" id="user_id" name="user_id">
            </div>
            <div class="form-column">
                <label for="date">Date</label>
                <input type="date" id="date" name="date">
            </div>
        </div>
        <input type="submit" value="Filter">
    </form>
    <table>
        <caption>Audit Trail</caption>
        <tr>
            <th>ID</th>
            <th>User ID</th>
            <th>Action</th>
            <th>Date</th>
            <th>User Type</th>
        </tr>
        <?php
        // Get filter inputs
        $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : '';
        $date = isset($_GET['date']) ? $_GET['date'] : '';

        // Prepare SQL query with filters
        $query = "SELECT id, userID, action, DATE_FORMAT(date, '%M %d, %Y') as formatted_date, userType 
                  FROM audit_trail 
                  WHERE userType IN ('rider', 'customer')";

        if ($user_id) {
            $query .= " AND userID = :user_id";
        }
        if ($date) {
            $query .= " AND DATE(date) = :date";
        }

        $query .= " ORDER BY id DESC";
        $stmt = $conn->prepare($query);

        if ($user_id) {
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
        }
        if ($date) {
            $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        }

        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) > 0) {
            foreach ($result as $row) {
                echo "<tr>";
                echo "<td>" . $row["id"] . "</td>";
                echo "<td>" . $row["userID"] . "</td>";
                echo "<td>" . $row["action"] . "</td>";
                echo "<td>" . $row["formatted_date"] . "</td>";
                echo "<td>" . $row["userType"] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>0 results</td></tr>";
        }
        ?>
    </table>
</div>


</body>
</html>
<?php
$conn = null;
?>
