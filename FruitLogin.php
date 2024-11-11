<?php 
    require "FruitSQLcon.php";
    session_start(); 
    $un = $_POST['userN'];
    $pw = $_POST['Pass'];

    // Check if the user has reached the maximum login attempts for today
    $today = date('Y-m-d');
    $failed_login_query = "SELECT COUNT(*) as attempts FROM audit_trail WHERE userID IN (SELECT user_id FROM users WHERE username=:username) AND action='Login failed' AND DATE(date) = :today";
    $stmt = $conn->prepare($failed_login_query);
    $stmt->execute([':username' => $un, ':today' => $today]);
    $failed_login_data = $stmt->fetch(PDO::FETCH_ASSOC);
    $failed_attempts_today = $failed_login_data['attempts'];

    $remaining_attempts = 5 - $failed_attempts_today;

    if ($remaining_attempts <= 0) {
        $error = "You have reached the maximum login attempts for today. Please try again tomorrow.";
        $_SESSION['errorm'] = $error;
        header("Location: FarmLogin.php");
        exit();
    }

    $query = "SELECT * FROM users WHERE username=:username";
    $stmt = $conn->prepare($query);
    $stmt->execute([':username' => $un]);

    if ($stmt && $stmt->rowCount() == 1) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $hashed_password = $row['password'];
        
        if (password_verify($pw, $hashed_password)) {
            $_SESSION['U_id'] = $row['user_id'];
            $_SESSION['U_type'] = $row['user_type'];

            switch ($row['user_type']) {
                case 'admin':
                    $_SESSION['U_id'] = $row['user_id'];
                    $notify = 'Welcome Admin, you are now logged in.';
                    echo "<script>
                        alert('$notify');
                        window.location.href = 'adminhome.php';
                    </script>";
                    break;
                case 'manager':
                    $_SESSION['U_id'] = $row['user_id'];
                    $notify = 'Welcome Manager, you are now logged in.';
                    echo "<script>
                        alert('$notify');
                        window.location.href = 'adminhome.php';
                    </script>";
                    break;
                case 'rider':
                    $_SESSION['U_id'] = $row['user_id'];
                    $_SESSION['action'] = 'Login successful';
                    $_SESSION['location'] = 'riderorders.php';
                    $notify = 'Welcome Rider, you are now logged in.';
                    echo "<script>
                        alert('$notify');
                        window.location.href = 'Ltrail.php';
                    </script>";
                    break;
                case 'customer':
                        $_SESSION['U_id'] = $row['user_id'];
                        $_SESSION['action'] = 'Login successful';
                        $_SESSION['location'] = 'FarmHome.php';
                        //$notify = 'Welcome, you are now logged in.';
                        //echo "<script>
                            //alert('$notify');
                            //window.location.href = 'Ltrail.php';
                        //</script>";
                        $_SESSION['email'] = $row['email'];
                        header("Location: authcode.php");
                        exit();
                    break;
                default:
                    echo "Unknown user type";
                    break;
            }
            exit();
        } else {
            // Insert audit trail record for failed login attempt
            $_SESSION['U_id'] = $row['user_id'];
            $_SESSION['action'] = 'Login failed';
            $_SESSION['location'] = 'FarmLogin.php';
            $error = "Incorrect username/password. You have " . ($remaining_attempts - 1) . " attempts remaining to login.";
            $_SESSION['errorm'] = $error;
            header("Location: Ltrail.php");
            exit();
        }
    } else {
        $error = "This account doesn't exist.";
        $_SESSION['errorm'] = $error;
        header("Location: FarmLogin.php");
        exit();
    }
?>
