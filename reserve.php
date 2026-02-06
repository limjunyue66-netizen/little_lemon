<?php 
include 'db.php';

if(!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$msg = '';
$error = '';

// Define available tables and capacities (table_number => seats)
$tables = [
    1 => 2,
    2 => 2,
    3 => 4,
    4 => 4,
    5 => 6,
    6 => 6,
    7 => 8,
    8 => 8,
    9 => 10,
    10 => 12
];

if($_SERVER['REQUEST_METHOD']=='POST'){
    $uid = $_SESSION['user']['id'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $guest = intval($_POST['guest']);
    $table_id = intval($_POST['table_id'] ?? 0);
    
    // Validation
    if(empty($date) || empty($time) || $guest <= 0) {
        $_SESSION['reservation_error'] = "All fields are required and guests must be greater than 0";
    } else {
        // Block weekends: PHP date('w') returns 0 (Sun) .. 6 (Sat)
        $w = date('w', strtotime($date));
        if($w == 0 || $w == 6) {
            $_SESSION['reservation_error'] = "Operator is off on Saturday and Sunday. Please select a weekday.";
        } else {
            // validate table selection
            if(!isset($tables[$table_id])) {
                $_SESSION['reservation_error'] = "Please select a valid table.";
            } else {
                $capacity = $tables[$table_id];
                if($guest > $capacity) {
                    $_SESSION['reservation_error'] = "Selected table $table_id only has $capacity seats. Please choose a larger table or reduce guests.";
                } else {
                    $date = $conn->real_escape_string($date);
                    $time = $conn->real_escape_string($time);
                    $table_safe = $conn->real_escape_string((string)$table_id);

                    // Check if reservations.table_number column exists
                    $result = $conn->query("SHOW COLUMNS FROM reservations LIKE 'table_number'");
                    $has_table_col = $result && $result->num_rows > 0;

                    if($has_table_col) {
                        $stmt = $conn->prepare("INSERT INTO reservations(user_id,reserve_date,reserve_time,guests,table_number) VALUES(?,?,?,?,?)");
                        $stmt->bind_param("isssi", $uid, $date, $time, $guest, $table_safe);
                    } else {
                        // fallback if DB not updated yet
                        $stmt = $conn->prepare("INSERT INTO reservations(user_id,reserve_date,reserve_time,guests) VALUES(?,?,?,?)");
                        $stmt->bind_param("isss", $uid, $date, $time, $guest);
                    }

                    if($stmt->execute()) {
                        $note = $has_table_col ? '' : ' (table not saved — run ALTER TABLE to add table_number column)';
                        $_SESSION['reservation_success'] = "Reservation successful for $date at $time for $guest guests at Table $table_id!" . $note;
                        header("Location: index.php");
                        exit();
                    } else {
                        $_SESSION['reservation_error'] = "Error: " . $stmt->error;
                    }
                    $stmt->close();
                }
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
<input type="number" name="guest" class="form-control" min="1" max="20" required>
</div>
<div class="mb-3">
    <label class="form-label">Select Table</label>
    <select name="table_id" class="form-select" required>
        <option value="">-- Select Table --</option>
        <?php foreach($tables as $tn => $seats): ?>
            <option value="<?= $tn ?>">Table <?= $tn ?> (<?= $seats ?> seats)</option>
        <?php endforeach; ?>
    </select>
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
