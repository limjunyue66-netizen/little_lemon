<?php
include 'db.php';

if(!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user']['id'];
$message = '';

// Get user's orders
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id=? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
<title>My Orders - Little Lemon</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="assets/css/responsive.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-3"><a href="javascript:history.back()" class="btn btn-secondary btn-sm">← Back</a></div>

<div class="container mt-4 mb-5">
    <h2 class="mb-4"><i class="bi bi-receipt"></i> My Orders</h2>

    <?php if($message): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="bi bi-info-circle"></i> <?= $message ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if($orders_result->num_rows > 0): ?>

    <?php while($order = $orders_result->fetch_assoc()): ?>

    <div class="card order-card mb-3 shadow-sm">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="card-title mb-1">
                        <i class="bi bi-bag"></i> Order #<?= $order['id'] ?>
                    </h5>
                    <p class="text-muted mb-2">
                        <i class="bi bi-calendar-event"></i> 
                        <?= date('F j, Y | g:i A', strtotime($order['created_at'])) ?>
                    </p>
                    <p class="mb-0">
                        <span class="badge bg-secondary"><?= ucfirst($order['order_type']) ?></span>
                    </p>
                </div>
                <div class="col-md-4">
                    <div class="text-md-end">
                        <p class="text-muted small mb-1">Total Amount</p>
                        <p class="h5 mb-0 text-success fw-bold">$<?= number_format($order['total'], 2) ?></p>
                    </div>
                </div>
            </div>

            <!-- Items in order -->
            <div class="mt-3 pt-3 border-top">
                <p class="small text-muted mb-2"><i class="bi bi-list-check"></i> Items:</p>
                <?php
                $stmt2 = $conn->prepare("SELECT oi.quantity, m.name, m.price FROM order_items oi JOIN menu m ON oi.menu_id = m.id WHERE oi.order_id = ?");
                $stmt2->bind_param("i", $order['id']);
                $stmt2->execute();
                $items = $stmt2->get_result();
                while($item = $items->fetch_assoc()):
                ?>
                    <div class="d-flex justify-content-between small">
                        <span><?= htmlspecialchars($item['name']) ?> x <?= $item['quantity'] ?></span>
                        <span>$<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                    </div>
                <?php endwhile; $stmt2->close(); ?>
            </div>
        </div>
    </div>

    <?php endwhile; $stmt->close(); ?>

    <?php else: ?>
    <div class="card text-center py-5">
        <div class="card-body">
            <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
            <p class="text-muted mt-3">You haven't placed any orders yet.</p>
            <a href="user_menu.php" class="btn btn-primary mt-3"><i class="bi bi-plus-circle"></i> Start Ordering</a>
        </div>
    </div>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
