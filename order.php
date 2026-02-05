<?php
include 'db.php';
// Allow guests to add items to cart; login will be required at payment time

if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
?>
<!DOCTYPE html>
<html>
<head>
<title>Order - Little Lemon</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<style>
	.menu-table tbody tr:hover { background-color: #f8f9fa; }
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
        $check = mysqli_query($conn, "SELECT id FROM menu WHERE id=$id");
        if(mysqli_num_rows($check) > 0) {
            $_SESSION['cart'][$id] = (isset($_SESSION['cart'][$id]) ? $_SESSION['cart'][$id] : 0) + $qty;
            $msg = "Item added to cart!";
        } else {
            $error = "Menu item not found";
        }
    }
    
    if(isset($_POST['checkout'])){
        // Prepare pending order and redirect to payment
        $type = $_POST['type'] ?? '';
        if(empty($_SESSION['cart'])) {
            $error = "Cart is empty";
        } else {
            $total = 0;
            foreach($_SESSION['cart'] as $mid => $q){
                $p = mysqli_fetch_assoc(mysqli_query($conn, "SELECT price FROM menu WHERE id=$mid"));
                if($p) {
                    $total += $p['price'] * $q;
                }
            }

            // store pending order in session for payment step
            $_SESSION['pending_order'] = [
                'user_id' => isset($_SESSION['user']) ? $_SESSION['user']['id'] : null,
                'type' => mysqli_real_escape_string($conn, $type),
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

// Get all menu items
$menu_result = mysqli_query($conn, "SELECT m.*, c.name as category FROM menu m JOIN categories c ON m.category_id = c.id");
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
<div class="card shadow-sm mb-4">
<div class="card-header bg-primary text-white"><i class="bi bi-list"></i> Available Menu</div>
<div class="card-body p-0">
<table class="table table-hover menu-table mb-0">
<thead class="table-light">
<tr>
<th><i class="bi bi-cup-hot"></i> Item</th>
<th>Category</th>
<th>Price</th>
<th>Qty</th>
<th>Action</th>
</tr>
</thead>
<tbody>
<?php while($menu = mysqli_fetch_assoc($menu_result)): ?>
<tr>
<form method="post" class="w-100">
<td><strong><?= htmlspecialchars($menu['name']) ?></strong></td>
<td><span class="badge bg-info"><?= htmlspecialchars($menu['category']) ?></span></td>
<td><span class="fw-bold text-success">$<?= number_format($menu['price'], 2) ?></span></td>
<td><input type="number" name="qty" value="1" min="1" class="form-control qty-input"></td>
<td>
<input type="hidden" name="menu_id" value="<?= $menu['id'] ?>">
<button type="submit" name="add_to_cart" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg"></i></button>
</td>
</form>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
</div>
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
        $m = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM menu WHERE id=$id"));
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
