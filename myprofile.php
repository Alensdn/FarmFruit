<?php 
require "sqlcon2.php";
session_start();

// Check if the form is submitted to update the profile
if (isset($_POST['update_prof'])) {
    // Collect form data
    $fname = $_POST["fname"];
    $lname = $_POST["lname"];
    $house_number = $_POST["house_number"];
    $street = $_POST["street"];
    $barangay = $_POST["barangay"];

    // Assuming you have a user ID stored in a session
    $user_id = $_SESSION["U_id"];

    // Update the user profile in the database using prepared statements
    $sql = "UPDATE users SET fname=?, lname=?, house_number=?, street=?, barangay=? WHERE user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$fname, $lname, $house_number, $street, $barangay, $user_id]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['action'] = 'Customer changed profile details';
        $_SESSION['location'] = 'myprofile.php';
        $notify = 'changed profile details successfully.';

        // Redirect with alert
        echo "<script>
            alert('$notify');
            window.location.href = 'Ltrail.php';
        </script>";
        exit();
    } else {
        echo "Error updating profile.";
    }
}

// Assuming you have a user ID stored in a session or obtained in some way
$user_id = $_SESSION["U_id"] ?? null;

if ($user_id) {
    // Retrieve user information
    $sql = "SELECT * FROM users WHERE user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $_SESSION['fname'] = $result['fname'];
        $_SESSION['lname'] = $result['lname'];
        $fname = $_SESSION['fname'];
        $lname = $_SESSION['lname'];
    } else {
        echo "User not found.";
    }
} else {
    echo "No user ID found in session.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
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

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .container2{
            display: none;
            max-width: 700px;
            width: 100%;
            background: #fff;
            padding: 25px 30px;
            border-radius: 5px;

        }
        .container2 .title{
            font-size: 25px;
            font-weight: 500;
            position: relative;
        }
        .container2 .title::before{
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            height: 3px;
            width: 30px;
            background: linear-gradient(135deg, #4CAF50, blue)
        }
        .container2 form .user-update{
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin: 20px 0 12px 0;
        }
        form .user-update .input-box{
            
            margin-bottom: 15px;
            width: 300px;
        }
        .user-details .input-box .details{
            display: block;
            font-weight: 500;
            margin-bottom: 5px;
        }
        .user-update .input-box input{
            height: 45px;
            width: 100%;
            outline: none;
            border-radius: 5px;
            border: 1px solid #ccc;
            padding-left: 15px;
            font-size: 16px;
            border-bottom-width: 2px;
            transition: all 0.3s ease;
        }
        .user-update .input-box input:focus,
        .user-update .input-box input:valid{
            border-color: #4CAF50;
        }
        form .button{
            height: 45px;
            width: 45px 0;
        }
        form .button input{
            height:100%;
            width: 100%;
            text-decoration: none;
            padding: 8px 16px;
            border: 1px solid #333;
            border-radius: 4px;
            color: #333;
            background-color: #4CAF50;
            transition: background-color 0.3s, color 0.3s;
            margin-bottom: -3%;
            
        }
        form .button input:hover{
            background-color: #333;
            color: #fff;
        }
        .container3 .title{
            font-size: 25px;
            font-weight: 500;
            position: relative;
        }
        .container3 .title::before{
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            height: 3px;
            width: 30px;
            background: linear-gradient(135deg, #4CAF50, blue)
        }
        .container3 form .button{
            height: 45px;
            width: 45px 0;
        }
        .container3 form .button input{
            height:100%;
            width: 10%;
            text-decoration: none;
            padding: 8px 16px;
            border: 1px solid #333;
            border-radius: 4px;
            color: #333;
            background-color: #4CAF50;
            transition: background-color 0.3s, color 0.3s;
            margin-bottom: -3%;
            
        }
        .container3 form .button input:hover{
            background-color: #333;
            color: #fff;
        }
        .container3{
            display: none;
            max-width: 700px;
            width: 100%;
            background: #fff;
            padding: 25px 30px;
            border-radius: 5px;

        }
        .activate{            
        }
        .activate button{
            margin-left: 6%;
            margin-bottom: 20px;
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
    <div class="container">
        <h2>User Profile</h2>
       <?php
            if ($user_id) {
                $sql = "SELECT * FROM users WHERE user_id=?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$user_id]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($result) {
                    echo "<div class='user-info'>";
                    echo "User name " . htmlspecialchars($result["username"]) . "<br>";
                    echo "First Name: " . htmlspecialchars($result["fname"]) . "<br>";
                    echo "Last Name: " . htmlspecialchars($result["lname"]) . "<br>";
                    echo "Email: " . htmlspecialchars($result["email"]) . "<br>";
                    echo "Phone Number: " . htmlspecialchars($result["phone_number"]) . "<br>";
                    echo "Address: " . htmlspecialchars($result["house_number"]) . ", " . htmlspecialchars($result["street"]) . ", " . htmlspecialchars($result["barangay"]) . "<br><br>";
                    echo "</div>";
                
                } else {
                    echo "User not found.";
                }
            } else {
                echo "No user ID found in session.";
                exit();
            }
?>
        <!-- Update Profile Form -->
        <button class="update-button" onclick="showUpdateForm('2')">Update Profile Details</button>
        <button class="update-button" onclick="showUpdateForm('3')">Change Password</button>

    <div class="container2" style="display: none;">
        <div class="title">Update Profile</div>
        <form action="" method="post">
            <div class="user-update">
                <div class="input-box">
                    <span class="details">First Name:</span>
                    <input type="text" id="fname" name="fname" pattern="[A-Za-z]+" required 
                    title="Please enter letters only" value="<?php echo htmlspecialchars($result["fname"]); ?>">
                </div>

                <div class="input-box">
                    <span class="details">Last Name:</span>
                    <input type="text" id="lname" name="lname"pattern="[A-Za-z]+" required 
                    title="Please enter letters only" value="<?php echo htmlspecialchars($result["lname"]); ?>">
                </div>

                <div class="input-box">
                    <span class="details">House Number:</span>
                    <input type="text" id="house_number" name="house_number" required 
                    value="<?php echo htmlspecialchars($result["house_number"]); ?>">
                </div>

                <div class="input-box">
                    <span class="details">Street:</span>
                    <input type="text" id="street" name="street" required 
                    value="<?php echo htmlspecialchars($result["street"]); ?>">
                </div>

                <div class="input-box">
                    <span class="details">Barangay:</span>
                    <input type="text" id="barangay" name="barangay" required 
                    value="<?php echo htmlspecialchars($result["barangay"]); ?>">
                </div>
                <div class="button">
                    <input type="hidden" name="update_prof" value="1">
                    <input type="submit" value="Update">
                </div>
            </div>
        </form>
    </div>

    <div class="container3" style="display: none;">
        <div class="title">Change Password</div>
        <form action="Lupdatecred.php" method="post">
            <div class="user-update">
                <div class="input-box">
                    <span class="details">Current Password:</span>
                    <input type="password" id="current_password" name="current_password" required>
                </div>

                <div class="input-box">
                    <span class="details">New Password:</span>
                    <input type="password" id="password" name="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
                    title="Password must be at least 8 characters long and contain one uppercase letter, one lowercase 
                    letter, one digit and one special character " required>
                </div>

                <div class="button">
                    <input type="hidden" name="update_cred" value="1">
                    <input type="submit" value="Update">
                </div>
            </div>
        </form>
    </div>

    <script>
    function showUpdateForm(formType) {
        var forms = document.querySelectorAll('.container2, .container3');
        forms.forEach(function(form) {
            form.style.display = 'none';
        });

        var formToShow = document.querySelector('.container' + formType);
        if (formToShow) {
            formToShow.style.display = 'block';
        }
    }
    </script>
</body>
</html>
<?php 
$conn = null; // Close the PDO connection
?>

