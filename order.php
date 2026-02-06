<?php
include 'db.php';
// Allow guests to add items to cart; login will be required at payment time

if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
?>
<!DOCTYPE html>
<html>
<head>
<title>Order - Little Lemon</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="assets/css/responsive.css" rel="stylesheet">
<style>
	.menu-card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
	.menu-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.1) !important; }
	.cart-card { border-top: 3px solid #28a745; }
	.qty-input { max-width: 70px; }
	.order-header { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; padding: 30px 0; margin-bottom: 30px; border-radius: 0 0 15px 15px; }
</style>
</head>
<body class="bg-light">

<?php

$msg = '';
$error = '';

if($_SERVER['REQUEST_METHOD']=='POST'){
    if(isset($_POST['add_to_cart'])) {
        $id = intval($_POST['menu_id']);
        $qty = intval($_POST['qty']);
        
        // Verify menu item exists
        $stmt = $conn->prepare("SELECT id FROM menu WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0) {
            $_SESSION['cart'][$id] = (isset($_SESSION['cart'][$id]) ? $_SESSION['cart'][$id] : 0) + $qty;
            $msg = "Item added to cart!";
        } else {
            $error = "Menu item not found";
        }
        $stmt->close();
    }
    
    if(isset($_POST['checkout'])){
        // Prepare pending order and redirect to payment
        $type = $_POST['type'] ?? '';
        if(empty($_SESSION['cart'])) {
            $error = "Cart is empty";
        } else {
            $total = 0;
            foreach($_SESSION['cart'] as $mid => $q){
                $stmt = $conn->prepare("SELECT price FROM menu WHERE id=?");
                $stmt->bind_param("i", $mid);
                $stmt->execute();
                $result = $stmt->get_result();
                $p = $result->fetch_assoc();
                if($p) {
                    $total += $p['price'] * $q;
                }
                $stmt->close();
            }

            // store pending order in session for payment step
            $_SESSION['pending_order'] = [
                'user_id' => isset($_SESSION['user']) ? $_SESSION['user']['id'] : null,
                'type' => $conn->real_escape_string($type),
                'total' => $total,
                'cart' => $_SESSION['cart']
            ];

            // If user not logged in, go to login so they can continue to payment after auth
            if(!isset($_SESSION['user'])){
                header('Location: login.php');
                exit();
            } else {
                header('Location: payment.php');
                exit();
            }
        }
    }
}

// Get all menu items (DISTINCT to prevent duplicates)
$menu_result = $conn->query("SELECT DISTINCT m.*, c.name as category FROM menu m JOIN categories c ON m.category_id = c.id GROUP BY m.id");
?>

<div class="order-header">
	<div class="container d-flex justify-content-between align-items-center">
		<div>
			<h2 class="mb-0"><i class="bi bi-bag-check"></i> Place Your Order</h2>
			<p class="mb-0 mt-2 opacity-75">Browse menu, add items to cart, and checkout</p>
		</div>
		<a href="javascript:history.back()" class="btn btn-light btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
	</div>
</div>

<div class="container">

<?php if($msg): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
	<i class="bi bi-check-circle"></i> <?= $msg ?>
	<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if($error): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
	<i class="bi bi-exclamation-circle"></i> <?= $error ?>
	<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row">
<div class="col-lg-8">
<h4 class="mb-4"><i class="bi bi-list"></i> Available Menu</h4>
<div class="row g-4">
<?php while($menu = $menu_result->fetch_assoc()): ?>
<div class="col-md-6 col-lg-4">
<div class="card shadow-sm menu-card h-100 text-center">
	<div class="card-body d-flex flex-column">
		<h5 class="card-title"><?= htmlspecialchars($menu['name']) ?></h5>
		<p class="text-muted small mb-2"><span class="badge bg-info"><?= htmlspecialchars($menu['category']) ?></span></p>
		<p class="h5 text-success fw-bold mb-3">$<?= number_format($menu['price'], 2) ?></p>
		<form method="post" class="mt-auto">
			<div class="input-group input-group-sm mb-2">
				<input type="number" name="qty" value="1" min="1" class="form-control" placeholder="Qty">
				<button type="submit" name="add_to_cart" class="btn btn-primary"><i class="bi bi-cart-plus"></i> Add</button>
			</div>
			<input type="hidden" name="menu_id" value="<?= $menu['id'] ?>">
		</form>
	</div>
</div>
</div>
<?php endwhile; ?>
</div>
</div>

<div class="col-lg-4">
<div class="card cart-card shadow-sm position-sticky" style="top: 20px;">
<div class="card-header bg-success text-white"><i class="bi bi-cart3"></i> Your Cart</div>
<div class="card-body" style="max-height: 500px; overflow-y: auto;">
<?php 
$cart_total = 0;
if(!empty($_SESSION['cart'])): 
    foreach($_SESSION['cart'] as $id => $q):
        $stmt = $conn->prepare("SELECT * FROM menu WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $m = $result->fetch_assoc();
        if($m):
            $subtotal = $m['price'] * $q;
            $cart_total += $subtotal;
?>
<div class="d-flex justify-content-between mb-2">
<span><?= htmlspecialchars($m['name']) ?> x <?= $q ?></span>
<span>$<?= number_format($subtotal, 2) ?></span>
</div>
<?php 
        endif;
    endforeach;
endif;
?>
</div>
<hr class="my-3">
<div class="d-flex justify-content-between align-items-center mb-4">
	<span class="h6 mb-0"><i class="bi bi-calculator"></i> Total:</span>
	<span class="h5 mb-0 text-success fw-bold">$<?= number_format($cart_total, 2) ?></span>
</div>

<?php if(!empty($_SESSION['cart'])): ?>
<form method="post">
<div class="mb-3">
	<label class="form-label"><i class="bi bi-geo-alt"></i> Order Type</label>
	<select name="type" class="form-select" required>
		<option value="">-- Select --</option>
		<option value="dine-in"><i class="bi bi-cup-straw"></i> Dine In</option>
		<option value="takeaway"><i class="bi bi-box-seam"></i> Takeaway</option>
	</select>
</div>
<button type="submit" name="checkout" class="btn btn-success w-100 btn-lg"><i class="bi bi-credit-card"></i> Proceed to Payment</button>
</form>
<?php else: ?>
<p class="text-muted text-center"><i class="bi bi-inbox"></i> Cart is empty. Add items to continue.</p>
<?php endif; ?>
</div>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
