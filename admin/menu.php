<?php 
include '../db.php';

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$msg = '';
$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_menu'])) {
    $cat = intval($_POST['cat']);
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    
    if(empty($name) || $price <= 0 || $cat <= 0) {
        $error = "All fields are required. Price must be greater than 0";
    } else {
        $name = mysqli_real_escape_string($conn, $name);
        if(mysqli_query($conn, "INSERT INTO menu(category_id,name,price) VALUES('$cat','$name','$price')")) {
            $msg = "Menu item added successfully!";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Menu</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-3"><a href="javascript:history.back()" class="btn btn-secondary btn-sm">← Back</a></div>

<div class="container mt-5">
<div class="card shadow">
<div class="card-body">
<h3>Add Menu Item</h3>

<?php if($msg): ?>
<div class="alert alert-success"><?= $msg ?></div>
<?php endif; ?>

<?php if($error): ?>
<div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form method="post" class="mb-4">
<div class="row mb-3">
<div class="col-md-4">
<label class="form-label">Category</label>
<select name="cat" class="form-control" required>
<option value="">Select Category</option>
<?php
$c = mysqli_query($conn, "SELECT * FROM categories");
while($r = mysqli_fetch_assoc($c)):
?>
<option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['name']) ?></option>
<?php endwhile; ?>
</select>
</div>
<div class="col-md-4">
<label class="form-label">Item Name</label>
<input type="text" name="name" class="form-control" placeholder="Food name" required>
</div>
<div class="col-md-4">
<label class="form-label">Price</label>
<input type="number" name="price" class="form-control" placeholder="Price" step="0.01" min="0" required>
</div>
</div>
<button type="submit" name="add_menu" class="btn btn-success">Add Item</button>
</form>

<h5>Menu Items</h5>
<table class="table table-striped">
<tr>
<th>ID</th>
<th>Name</th>
<th>Category</th>
<th>Price</th>
</tr>
<?php
$menu = mysqli_query($conn, "SELECT m.*, c.name as category FROM menu m JOIN categories c ON m.category_id = c.id ORDER BY m.id DESC");
while($item = mysqli_fetch_assoc($menu)):
?>
<tr>
<td><?= $item['id'] ?></td>
<td><?= htmlspecialchars($item['name']) ?></td>
<td><?= htmlspecialchars($item['category']) ?></td>
<td>$<?= number_format($item['price'], 2) ?></td>
</tr>
<?php endwhile; ?>
</table>
</div>
</div>
</div>

</body>
</html>
