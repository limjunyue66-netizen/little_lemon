<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
<title>Menu</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="assets/css/responsive.css" rel="stylesheet">
<style>
	.menu-card:hover{ transform: translateY(-4px); box-shadow: 0 6px 18px rgba(0,0,0,0.06); transition: all 0.3s ease; }
	.price-badge{ font-size: 1rem; }
</style>
</head>
<body class="bg-light">

<div class="container mt-3"><a href="javascript:history.back()" class="btn btn-secondary btn-sm">← Back</a></div>

<div class="container mt-4">
	<div class="d-flex justify-content-between align-items-center mb-4">
		<h2 class="mb-0"><i class="bi bi-lemon"></i> Our Menu</h2>
	</div>

	<?php
	// selected category filter
	$selected_cat = isset($_GET['cat']) ? intval($_GET['cat']) : 0;

	// categories
	$cats = mysqli_query($conn, "SELECT * FROM categories ORDER BY id");
	echo '<div class="mb-4">';
	echo '<a href="user_menu.php" class="btn btn-sm btn-outline-primary me-2' . ($selected_cat==0? ' active':'') . '"><i class="bi bi-grid-fill"></i> All</a>';
	while($cat = mysqli_fetch_assoc($cats)){
		$active = ($selected_cat == $cat['id']) ? ' active' : '';
		echo '<a href="user_menu.php?cat='.$cat['id'].'" class="btn btn-sm btn-outline-primary me-2'.$active.'">'.htmlspecialchars($cat['name']).'</a>';
	}
	echo '</div>';

	// menu items
	if($selected_cat>0){
		$items = mysqli_query($conn, "SELECT * FROM menu WHERE category_id=$selected_cat ORDER BY id DESC");
	} else {
		$items = mysqli_query($conn, "SELECT m.*, c.name as category FROM menu m JOIN categories c ON m.category_id=c.id ORDER BY m.id DESC");
	}
	?>

	<div class="row">
		<?php while($m = mysqli_fetch_assoc($items)): ?>
		<div class="col-md-4 col-sm-6 mb-4">
			<div class="card menu-card h-100">
				<div class="card-body d-flex flex-column">
					<div class="d-flex justify-content-between align-items-start mb-2">
						<h5 class="card-title mb-0"><?= htmlspecialchars($m['name']) ?></h5>
						<span class="badge bg-success price-badge">$<?= number_format($m['price'],2) ?></span>
					</div>
					<?php if(isset($m['category'])): ?>
					<p class="text-muted small mb-3"><i class="bi bi-tags"></i> <?= htmlspecialchars($m['category']) ?></p>
					<?php endif; ?>

					<div class="mt-auto">
						<form method="post" action="order.php" class="d-flex gap-2">
							<input type="hidden" name="menu_id" value="<?= $m['id'] ?>">
							<input type="number" name="qty" value="1" min="1" class="form-control" style="width:90px;">
							<button class="btn btn-primary" type="submit" name="add_to_cart"><i class="bi bi-cart-plus"></i> Add</button>
						</form>
					</div>
				</div>
			</div>
		</div>
		<?php endwhile; ?>
	</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>