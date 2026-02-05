<?php
include 'db.php';

if(!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user']['id'];
$message = '';

// Handle order cancellation
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order'])) {
    $order_id = intval($_POST['order_id']);
    
    // Verify order belongs to user
    $check = mysqli_query($conn, "SELECT id, status FROM orders WHERE id=$order_id AND user_id=$user_id");
    $order = mysqli_fetch_assoc($check);
    
    if($order && $order['status'] !== 'cancelled' && $order['status'] !== 'completed') {
        if(mysqli_query($conn, "UPDATE orders SET status='cancelled' WHERE id=$order_id")) {
            $message = "Order #$order_id has been cancelled successfully.";
        } else {
            $message = "Error cancelling order: " . mysqli_error($conn);
        }
    } elseif($order && $order['status'] === 'completed') {
        $message = "Cannot cancel completed orders.";
    } elseif($order && $order['status'] === 'cancelled') {
        $message = "Order is already cancelled.";
    } else {
        $message = "Order not found.";
    }
}

// Get user's orders
$orders_result = mysqli_query($conn, "SELECT * FROM orders WHERE user_id=$user_id ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>My Orders - Little Lemon</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<style>
    .order-card { border-left: 4px solid #28a745; }
    .order-card.cancelled { border-left-color: #dc3545; opacity: 0.8; }
    .order-card.completed { border-left-color: #007bff; }
    .order-card.pending { border-left-color: #ffc107; }
    .status-badge { padding: 8px 12px; border-radius: 20px; font-size: 0.85rem; }
</style>
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

    <?php if(mysqli_num_rows($orders_result) > 0): ?>
    
    <?php while($order = mysqli_fetch_assoc($orders_result)): 
        $status_class = $order['status'];
        $status_color = 'secondary';
        $status_icon = 'hourglass-split';
        
        if($order['status'] === 'completed') {
            $status_color = 'success';
            $status_icon = 'check-circle';
        } elseif($order['status'] === 'cancelled') {
            $status_color = 'danger';
            $status_icon = 'x-circle';
        } elseif($order['status'] === 'pending') {
            $status_color = 'warning';
            $status_icon = 'clock-history';
        }
    ?>
    
    <div class="card order-card <?= $status_class ?> mb-3 shadow-sm">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
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
                <div class="col-md-3">
                    <div class="text-md-end mb-2 mb-md-0">
                        <p class="text-muted small mb-1">Total Amount</p>
                        <p class="h5 mb-0 text-success fw-bold">$<?= number_format($order['total'], 2) ?></p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-md-end">
                        <span class="status-badge bg-<?= $status_color ?> text-white">
                            <i class="bi bi-<?= $status_icon ?>"></i> <?= ucfirst($order['status']) ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Items in order -->
            <div class="mt-3 pt-3 border-top">
                <p class="small text-muted mb-2"><i class="bi bi-list-check"></i> Items:</p>
                <?php
                $items = mysqli_query($conn, "SELECT oi.quantity, m.name, m.price FROM order_items oi JOIN menu m ON oi.menu_id = m.id WHERE oi.order_id = " . $order['id']);
                while($item = mysqli_fetch_assoc($items)):
                ?>
                    <div class="d-flex justify-content-between small">
                        <span><?= htmlspecialchars($item['name']) ?> x <?= $item['quantity'] ?></span>
                        <span>$<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                    </div>
                <?php endwhile; ?>
            </div>

            <!-- Cancel button -->
            <?php if($order['status'] !== 'completed' && $order['status'] !== 'cancelled'): ?>
            <div class="mt-3 pt-3 border-top">
                <form method="post" style="display: inline;">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <button type="submit" name="cancel_order" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to cancel this order?');">
                        <i class="bi bi-x-lg"></i> Cancel Order
                    </button>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php endwhile; ?>

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
