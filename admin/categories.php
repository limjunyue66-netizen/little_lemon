<?php 
include '../db.php';

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$msg = '';
$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['name']);
    
    if(empty($name)) {
        $error = "Category name is required";
    } else {
        $name = $conn->real_escape_string($name);
        $stmt = $conn->prepare("INSERT INTO categories(name) VALUES(?)");
        $stmt->bind_param("s", $name);
        if($stmt->execute()) {
            $msg = "Category added successfully!";
        } else {
            $error = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Categories</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-3"><a href="javascript:history.back()" class="btn btn-secondary btn-sm">← Back</a></div>

<div class="container mt-5">
<div class="card shadow">
<div class="card-body">
<h3>Manage Categories</h3>

<?php if($msg): ?>
<div class="alert alert-success"><?= $msg ?></div>
<?php endif; ?>

<?php if($error): ?>
<div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form method="post" class="mb-4">
<div class="input-group">
<input type="text" name="name" class="form-control" placeholder="Category name" required>
<button type="submit" name="add_category" class="btn btn-success">Add Category</button>
</div>
</form>

<h5>Existing Categories</h5>
<table class="table table-striped">
<tr>
<th>ID</th>
<th>Category Name</th>
</tr>
<?php
$c = $conn->query("SELECT * FROM categories ORDER BY id DESC");
while($row = $c->fetch_assoc()):
?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= htmlspecialchars($row['name']) ?></td>
</tr>
<?php endwhile; ?>
</table>
</div>
</div>
</div>

</body>
</html>
