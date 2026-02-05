<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
<title>Little Lemon</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-success">
<div class="container">
<a class="navbar-brand"><i class="bi bi-lemon"></i> Little Lemon</a>

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

<div class="container text-center mt-5">
<h1>Welcome to Little Lemon 🍋</h1>
<p class="text-muted">Reserve table & order food online</p>

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

</body>
</html>
