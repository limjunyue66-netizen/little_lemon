<?php
include 'db.php';

$error = '';

if($_SERVER['REQUEST_METHOD']=='POST'){
    $email = trim($_POST['email']);
    $pass = $_POST['password'];
    
    $email = $conn->real_escape_string($email);
    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    if($user && password_verify($pass, $user['password'])){
        $_SESSION['user'] = $user;
        // If there's a pending order from guest, attach user and continue to payment
        if(isset($_SESSION['pending_order']) && empty($_SESSION['pending_order']['user_id'])){
            $_SESSION['pending_order']['user_id'] = $user['id'];
            header("Location: payment.php");
            exit();
        }
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/css/responsive.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-3"><a href="javascript:history.back()" class="btn btn-secondary btn-sm">← Back</a></div>
<div class="container mt-2">
    <div class="alert alert-info small mb-0">Prefer to call? Book by phone: <a href="tel:<?= $contact_phone ?>"><?= $contact_phone ?></a></div>
</div>

<div class="container mt-5 col-md-4">
<div class="card shadow">
<div class="card-body">
<h3 class="text-center">Login</h3>

<?php if($error): ?>
<div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form method="post">
<input class="form-control mb-3" type="email" name="email" placeholder="Email" required>
<input class="form-control mb-3" type="password" name="password" placeholder="Password" required>
<button class="btn btn-success w-100">Login</button>
</form>

<p class="text-center mt-3">Don't have an account? <a href="register.php">Register here</a></p>
</div>
</div>
</div>

</body>
</html>
