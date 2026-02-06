<?php
include '../db.php';

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">
<div class="container">
<span class="navbar-brand mb-0 h1">Little Lemon - Admin</span>
<div>
<a href="../index.php" class="btn btn-light btn-sm">Home</a>
<a href="../logout.php" class="btn btn-danger btn-sm">Logout</a>
</div>
</div>
</nav>

<div class="container mt-3"><a href="javascript:history.back()" class="btn btn-secondary btn-sm">← Back</a></div>

<div class="container mt-5">
<h2 class="mb-4">Admin Dashboard</h2>

<div class="row">
<div class="col-md-3 mb-3">
<div class="card bg-primary text-white">
<div class="card-body">
<h5 class="card-title">Total Users</h5>
<h3>
<?php
$result = $conn->query("SELECT COUNT(*) as count FROM users");
$row = $result->fetch_assoc();
echo $row['count'];
?>
</h3>
</div>
</div>
</div>

<div class="col-md-3 mb-3">
<div class="card bg-success text-white">
<div class="card-body">
<h5 class="card-title">Total Orders</h5>
<h3>
<?php
$result = $conn->query("SELECT COUNT(*) as count FROM orders");
$row = $result->fetch_assoc();
echo $row['count'];
?>
</h3>
</div>
</div>
</div>

<div class="col-md-3 mb-3">
<div class="card bg-warning text-white">
<div class="card-body">
<h5 class="card-title">Total Reservations</h5>
<h3>
<?php
$result = $conn->query("SELECT COUNT(*) as count FROM reservations");
$row = $result->fetch_assoc();
echo $row['count'];
?>
</h3>
</div>
</div>
</div>

<div class="col-md-3 mb-3">
<div class="card bg-info text-white">
<div class="card-body">
<h5 class="card-title">Menu Items</h5>
<h3>
<?php
$result = $conn->query("SELECT COUNT(*) as count FROM menu");
$row = $result->fetch_assoc();
echo $row['count'];
?>
</h3>
</div>
</div>
</div>
</div>

<hr>

<div class="row mt-4">
<div class="col-md-6 mb-3">
<div class="card">
<div class="card-body">
<h5 class="card-title">Management</h5>
<ul class="list-group list-group-flush">
<li class="list-group-item"><a href="menu.php" class="btn btn-link w-100 text-start">Manage Menu</a></li>
<li class="list-group-item"><a href="categories.php" class="btn btn-link w-100 text-start">Manage Categories</a></li>
<li class="list-group-item"><a href="orders.php" class="btn btn-link w-100 text-start">View Orders</a></li>
<li class="list-group-item"><a href="reservations.php" class="btn btn-link w-100 text-start">View Reservations</a></li>
</ul>
</div>
</div>
</div>
</div>

</div>

</body>
</html>
