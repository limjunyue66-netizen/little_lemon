<?php 
include 'db.php';

if(!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$msg = '';
$error = '';

if($_SERVER['REQUEST_METHOD']=='POST'){
    $uid = $_SESSION['user']['id'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $guest = intval($_POST['guest']);
    
    // Validation
    if(empty($date) || empty($time) || $guest <= 0) {
        $_SESSION['reservation_error'] = "All fields are required and guests must be greater than 0";
    } else {
        $date = mysqli_real_escape_string($conn, $date);
        $time = mysqli_real_escape_string($conn, $time);
        
        if(mysqli_query($conn, "INSERT INTO reservations(user_id,reserve_date,reserve_time,guests) VALUES('$uid','$date','$time','$guest')")) {
            $_SESSION['reservation_success'] = "Reservation successful for $date at $time for $guest guests!";
            header("Location: index.php");
            exit();
        } else {
            $_SESSION['reservation_error'] = "Error: " . mysqli_error($conn);
        }
    }
    
    if(isset($_SESSION['reservation_error'])) {
        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Reserve Table</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-3"><a href="javascript:history.back()" class="btn btn-secondary btn-sm">← Back</a></div>

<div class="container mt-5">
<div class="card shadow" style="max-width: 500px; margin: 0 auto;">
<div class="card-body">
<h3 class="card-title">Reserve a Table</h3>

<?php if($msg): ?>
<div class="alert alert-success"><?= $msg ?></div>
<?php endif; ?>

<?php if($error): ?>
<div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form method="post">
<div class="mb-3">
<label class="form-label">Date</label>
<input type="date" name="date" class="form-control" required>
</div>
<div class="mb-3">
<label class="form-label">Time</label>
<input type="time" name="time" class="form-control" required>
</div>
<div class="mb-3">
<label class="form-label">Number of Guests</label>
<input type="number" name="guest" class="form-control" min="1" max="20" placeholder="2" required>
</div>
<button type="submit" class="btn btn-success w-100">Reserve</button>
</form>
</div>
</div>
</div>

</body>
</html>
