<?php
session_start();
require "sqlcon1.php";
$id = $_SESSION['U_id'];
if (isset($_POST['mark_delivered'])) {
    $order_number = $_POST['order_number'];

    $update_query = "UPDATE orders SET Order_status = 'delivered', paymentstatus = 'paid' WHERE Order_number = ?";
    $stmt_update = $conn->prepare($update_query);
    $stmt_update->execute([$order_number]);

    $_SESSION['U_id'] = $id;
    $_SESSION['action'] = 'rider delivered the items';
    $_SESSION['location'] = 'riderorders.php';
    $notify = 'Updated Successfully';
    echo "<script> alert('$notify');
    window.location.href = 'Ltrail.php';</script>";
    exit();
}

if (isset($_POST['take_order'])) {
    $order_number = $_POST['order_number'];

    // Update Rider_ID to 1
    $update_rider_query = "UPDATE orders SET Rider_ID = ?, Order_status = 'preparing' WHERE Order_number = ?";
    $stmt_update_rider = $conn->prepare($update_rider_query);
    $stmt_update_rider->execute([$id, $order_number]);

    $_SESSION['U_id'] = $id;
    $_SESSION['action'] = 'rider preparing the order';
    $_SESSION['location'] = 'riderorders.php';
    $notify = 'Updated Successfully';
    echo "<script> alert('$notify');
    window.location.href = 'Ltrail.php';</script>";
    exit();
    
}   

if (isset($_POST['mark_on_delivery'])) {
    $order_number = $_POST['order_number'];

    // Update order status to 'On Delivery'
    $update_query = "UPDATE orders SET Order_status = 'On Delivery' WHERE Order_number = ?";
    $stmt_update = $conn->prepare($update_query);
    $stmt_update->execute([$order_number]);

    // Redirect
    $_SESSION['U_id'] = $id;
    $_SESSION['action'] = 'rider is on delivery';
    $_SESSION['location'] = 'riderorders.php';
    $notify = 'Updated Successfully';
    echo "<script> alert('$notify');
    window.location.href = 'Ltrail.php';</script>";
    exit();
}
?>
