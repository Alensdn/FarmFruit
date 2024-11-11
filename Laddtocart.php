<?php
require "sqlcon2.php";
session_start();

// Check if user is logged in
if (!isset($_SESSION['U_id'])) {
    // Redirect to login page or handle as appropriate
    header("Location: Farmlogin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addToCart'])) {
    $user_id = $_SESSION['U_id'];
    $item_id = $_POST['id'];
    $quantity = $_POST['quantity'];

    // Check the available stock for the selected item
    $stockQuery = "SELECT COALESCE(SUM(CASE WHEN item_history.date_expiration > CURRENT_DATE 
    OR item_history.date_expiration IS NULL THEN item_history.quantity ELSE 0 END), 0) AS stock
                   FROM item
                   LEFT JOIN item_history ON item.Item_ID = item_history.item_id
                   WHERE item.Item_ID = :item_id
                   GROUP BY item.Item_ID";

    $stmt = $conn->prepare($stockQuery);
    $stmt->bindParam(":item_id", $item_id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // If the quantity exceeds the available stock, display an error message and exit
    if ($row && $quantity > $row['stock']) {
        $notify = 'Quantity exceeds available stock.';
        echo "<script>
            alert('$notify');
            window.location.href = 'order.php';
        </script>";
        exit();
    }

    // Check if the item is already in the cart
    $check_sql = "SELECT * FROM cart WHERE userID = :user_id AND ItemID = :item_id";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->execute([':user_id' => $user_id, ':item_id' => $item_id]);
    $check_result = $check_stmt->fetch(PDO::FETCH_ASSOC);

    if ($check_result) {
        // Item already in cart, update the quantity
        $update_sql = "UPDATE cart SET quantity = quantity + :quantity WHERE userID = :user_id AND ItemID = :item_id";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->execute([':quantity' => $quantity, ':user_id' => $user_id, ':item_id' => $item_id]);
    } else {
        // Item not in cart, insert new record
        $insert_sql = "INSERT INTO cart (userID, ItemID, quantity) VALUES (:user_id, :item_id, :quantity)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->execute([':user_id' => $user_id, ':item_id' => $item_id, ':quantity' => $quantity]);
    }

    // No need to close PDO statements, it is handled by PDO itself
    $conn = null; // Close the connection

    $_SESSION['action'] = 'Item Added to Cart';
    $_SESSION['location'] = 'order.php';
    $notify = 'Item has been added to cart.';
    echo "<script>
        alert('$notify');
        window.location.href = 'Ltrail.php';
    </script>";
    exit();
}
?>

