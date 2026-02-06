<?php
include 'db.php';

if(!isset($_SESSION['pending_order'])) {
    header('Location: order.php');
    exit();
}

$pending = $_SESSION['pending_order'];
$cart = $pending['cart'];
$total = $pending['total'];

$error = '';

// require login for finalizing order
if(!isset($_SESSION['user'])){
    header('Location: login.php');
    exit();
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $method = $_POST['payment_method'] ?? 'card';

    $uid = $_SESSION['user']['id'];
    $type = mysqli_real_escape_string($conn, $pending['type']);
    $total = $pending['total'];

    $res = mysqli_query($conn, "INSERT INTO orders(user_id,order_type,total) VALUES('$uid','$type','$total')");
    if($res) {
        $oid = mysqli_insert_id($conn);
        foreach($cart as $mid => $q){
            mysqli_query($conn, "INSERT INTO order_items(order_id,menu_id,quantity) VALUES('$oid','$mid','$q')");
        }

        // clear cart and pending
        unset($_SESSION['cart']);
        unset($_SESSION['pending_order']);

        $_SESSION['order_success'] = "Order placed (method: $method). Order ID: $oid";
        header('Location: index.php');
        exit();
    } else {
        $error = 'Error processing order: ' . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/responsive.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5 col-md-6">
    <div class="card shadow">
    <div class="card-body">
    <h3 class="mb-3">Complete Payment</h3>

    <?php if($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <p><strong>Total:</strong> $<?= number_format($total,2) ?></p>

    <form method="post">
    <div class="mb-3">
        <label class="form-label">Payment Method</label>
        <select name="payment_method" class="form-select">
            <option value="card">Card</option>
            <option value="cod">Cash on Delivery</option>
        </select>
    </div>
    <button class="btn btn-success w-100">Confirm & Pay</button>
    </form>

    <p class="mt-3 text-muted">Select a payment method and click Confirm. No card details are required for this demo.</p>
    </div>
    </div>
</div>

</body>
</html>
