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
        // Block weekends: PHP date('w') returns 0 (Sun) .. 6 (Sat)
        $w = date('w', strtotime($date));
        if($w == 0 || $w == 6) {
            $_SESSION['reservation_error'] = "Operator is off on Saturday and Sunday. Please select a weekday.";
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
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/css/responsive.css" rel="stylesheet">
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
</div>
<script>
// Prevent selecting weekend dates client-side
document.addEventListener('DOMContentLoaded', function(){
    var dateInput = document.querySelector('input[name="date"]');
    if(!dateInput) return;
    dateInput.addEventListener('input', function(){
        var d = new Date(this.value + 'T00:00');
        if(isNaN(d)) return;
        var day = d.getDay(); // 0 Sun .. 6 Sat
        if(day === 0 || day === 6){
            alert('Operator is off on Saturday and Sunday. Please choose a weekday.');
            this.value = '';
        }
    });
});
</script>
</body>
</html>
