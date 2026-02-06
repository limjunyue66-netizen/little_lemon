<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
<title>Little Lemon</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="assets/css/responsive.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-success">
<div class="container">
<div class="d-flex align-items-center">
	<a class="navbar-brand"><i class="bi bi-lemon"></i> Little Lemon</a>
	<a href="tel:<?= $contact_phone ?>" class="btn btn-outline-light btn-sm ms-2 d-none d-md-inline">Contact number : <?= $contact_phone ?></a>
</div>

<div>
<?php if(isset($_SESSION['user'])): ?>
<span class="text-white me-3"><?= $_SESSION['user']['name'] ?></span>
<a href="logout.php" class="btn btn-light btn-sm">Logout</a>
<?php else: ?>
<a href="login.php" class="btn btn-light btn-sm">Login</a>
<a href="register.php" class="btn btn-warning btn-sm ms-2">Register</a>
<?php endif; ?>
</div>
</div>
</nav>

<div class="container text-center mt-5" style="background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('https://tse4.mm.bing.net/th/id/OIP.BH6a5m-KkrTvCV2A943yWQHaE8?pid=Api&P=0&h=220') center/cover no-repeat; padding: 120px 20px; border-radius: 10px; position: relative; color: white; text-shadow: 2px 2px 4px rgba(0,0,0,0.5); min-height: 500px; display: flex; align-items: center; justify-content: center;">
<div style="position: relative; z-index: 1;">
<h1>Welcome to Little Lemon 🍋</h1>
<p class="text-light">Reserve table & order food online</p>

<div class="container text-center mt-5">

<?php if(isset($_SESSION['reservation_success'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
<strong>Success!</strong> <?= $_SESSION['reservation_success'] ?>
<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php unset($_SESSION['reservation_success']); ?>
<?php endif; ?>

<?php if(isset($_SESSION['reservation_error'])): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
<strong>Error!</strong> <?= $_SESSION['reservation_error'] ?>
<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php unset($_SESSION['reservation_error']); ?>
<?php endif; ?>

<?php if(isset($_SESSION['order_success'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
<strong>Success!</strong> <?= $_SESSION['order_success'] ?>
<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php unset($_SESSION['order_success']); ?>
<?php endif; ?>

<?php if(isset($_SESSION['order_error'])): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
<strong>Error!</strong> <?= $_SESSION['order_error'] ?>
<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php unset($_SESSION['order_error']); ?>
<?php endif; ?>

<!-- Removed separate View Menu button; use the Order button to browse/select food -->

<?php if(isset($_SESSION['user'])): ?>
<a href="reserve.php" class="btn btn-success m-2">
<i class="bi bi-calendar-check"></i> Reserve
</a>
<a href="order.php" class="btn btn-warning m-2">
<i class="bi bi-bag"></i> Order
</a>
<a href="my_orders.php" class="btn btn-info m-2">
<i class="bi bi-receipt"></i> My Orders
</a>

<?php if(isset($_SESSION['user']['role']) && $_SESSION['user']['role']=='admin'): ?>
<a href="admin/dashboard.php" class="btn btn-dark m-2">
<i class="bi bi-speedometer2"></i> Admin
</a>
<?php endif; ?>
<?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
