<?php
    session_start();
    if (isset($_SESSION['errorm'])) {
        $registrationSuccessMessage = $_SESSION['errorm'];
        unset($_SESSION['errorm']);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Farm Fruit Store</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-image: url('styles/images/farmfruit-bg2.jpg');
            background-size: cover;
        }
        .loginNregister{
            width: 350px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            background-color: rgba(0, 0, 0, 0.3);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            margin-top: 10px;
        }

        .loginNregister input{
            width: 70%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            margin-left: 50px;
        }
        .loginNregister label{
            margin-left: 55px;
            color: rgba(255, 255, 255, 0.9);
            font-weight: bold;
        }

        .loginNregister button{
            margin-left: 80px;
            width: 50%;
            padding: 10px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 10px;
        }

        button:hover {
            background-color: #45a049;
        }

        .loginNregister h2{
            margin-left: 135px;
            color: rgba(255, 255, 255, 0.9);
        }

        .farm-fruit-div {
            background-color:  #4CAF50;
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


        .loginNregister2{
            width: 300px;
            margin: 50px auto;
            padding: 20px;
            margin-top: 10px;
        }
        .loginNregister2 label{
            margin-left: 82px;
            font-weight: bold;
            font-size: 15px;
        }
        .loginNregister2 button{
            margin-left: 67px;
        }
        .notify-register{
            background-color: rgba(0, 128, 0, 0.9);
            color: white; 
            text-align: center; 
            padding: 10px;
            margin-top: -810px;
            width: 200px;
            margin-left: 41%;
            border-radius: 10px;
            display: none;
        }
        .forgot-password p {
            text-decoration: none; /* Remove underline */
            color: white; /* Set link color */
            font-size: 14px; /* Adjust font size */
            margin-top: 5px; /* Adjust spacing */
            display: block; /* Make it a block element */
            margin-left: 87px;
        }

        .forgot-password a {
            text-decoration: none; /* Remove underline */
            color: white; /* Set link color */
            font-size: 14px; /* Adjust font size */
            margin-top: -17px; /* Adjust spacing */
            display: block; /* Make it a block element */
            margin-left: 106px;
        }

        .forgot-password a:hover {
            color: #666; /* Change color on hover */
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

    <div class="loginNregister" id="register" style="display: none;">
        <h2>Register</h2>
        <form method="POST" action="FruitRegister.php">
            <label>User Name</label>
            <input type="text" name="userN" placeholder="username" 
            required pattern=".{6,}" title="Username must be at least 6 characters long"
            required>
            <label>Password</label>
            <input type="password" name="pass" placeholder="Password" 
            pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
            title="Password must be at least 8 characters long and contain
            one uppercase letter, one lowercase letter, one digit and one special character " 
            required>
            <label>Email</label>
            <input type="email" name="email" placeholder="email" required>
            <label>Mobile Number</label>
            <input type="text" name="Mnum" placeholder="number" 
            pattern="\d{11}" pattern="09\d{9}" title="Please enter a 11-digit phone number starting with '09'" required>
            <label>First Name</label>
            <input type="text" name="Fname" placeholder="first name" pattern="[A-Za-z]+" required 
            title="Please enter letters only">
            <label>Last Name</label>
            <input type="text" name="Lname" placeholder="last name" pattern="[A-Za-z]+" required 
            title="Please enter letters only">
            <label>Baranggay</label>
            <input type="text" name="brgy" placeholder="baranggay" required>
            <label>Street</label>
            <input type="text" name="street" placeholder="street" required>
            <label>House Number</label>
            <input type="text" name="hnum" placeholder="house number" required>
            <button type="submit">Create</button>
            <button id="register-button" onclick="showRegister()">Back to Login</button>
        </form>
    </div>

    <div class="loginNregister" id="login">
        <img src="styles/images/Farmfruit3.png" alt="Farm fruit">
        <form method="POST" action="Fruitlogin.php">
            <h2>Login</h2>
            <label>User Name</label>
            <input type="text" name="userN" placeholder="username" required>
            <label for="Pass">Password</label>
            <input type="password" name="Pass" placeholder="Password" required>
            <button type="submit">Login</button>
            <div class="forgot-password">
                <p>Forgot Password?<a href="forgotpass.php">click here</a></p>
            </div>
            <div class="loginNregister2">
                <label class="create-label">Create Account?</label>
                <br>
                <button class="button1" id="login-button" onclick="showLogin()">Create</button>
            </div>
        </form>
    </div>

    <?php if (isset($registrationSuccessMessage)) : ?>
        <div class="notify-register">
            <span><?php echo $registrationSuccessMessage; ?></span>
        </div>
    <?php endif; ?>
    <script>
        function showRegister() {
            document.getElementById('login').style.display = 'block';
            document.getElementById('register').style.display = 'none';
        }

        function showLogin() {
            document.getElementById('login').style.display = 'none';
            document.getElementById('register').style.display = 'block';
        }

        document.addEventListener('DOMContentLoaded', function() {
            var registrationMessage = document.querySelector('.notify-register');
            if (registrationMessage) {
                registrationMessage.style.display = 'block';
                setTimeout(function() {
                    registrationMessage.style.display = 'none'; 
                }, 5000);
            }
        });

    </script>
    <?php
        if (isset($_SESSION['error']))  {
            echo "<script> showLogin();</script>";
        }
    ?>
</body>
</html>