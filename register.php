<?php
include 'db.php';

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD']=='POST'){
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // Validation
    if(empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required";
    } elseif(strlen($password) < 6) {
        $error = "Password must be at least 6 characters";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0) {
            $error = "Email already registered";
        } else {
            $pass = password_hash($password, PASSWORD_DEFAULT);
            $name = $conn->real_escape_string($name);
            $email = $conn->real_escape_string($email);
            
            $stmt2 = $conn->prepare("INSERT INTO users(name,email,password) VALUES(?,?,?)");
            $stmt2->bind_param("sss", $name, $email, $pass);
            if($stmt2->execute()) {
                $success = "Registration successful! Redirecting to login...";
                header("refresh:2;url=login.php");
            } else {
                $error = "Registration failed: " . $stmt2->error;
            }
            $stmt2->close();
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Register</title>
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
<h3 class="text-center">Register</h3>

<?php if($error): ?>
<div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<?php if($success): ?>
<div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<form method="post">
<input class="form-control mb-3" name="name" placeholder="Name" required>
<input class="form-control mb-3" type="email" name="email" placeholder="Email" required>
<input class="form-control mb-3" type="password" name="password" placeholder="Password (min 6 characters)" required>
<button class="btn btn-success w-100">Register</button>
</form>

<p class="text-center mt-3">Already have an account? <a href="login.php">Login here</a></p>
</div>
</div>
</div>

</body>
</html>
