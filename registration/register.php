<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: list.php');
    exit;
}

include 'database.php';

$message = '';
$messageType = 'info';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    if ($username === '' || $email === '' || $full_name === '' || $password === '' || $confirm_password === '') {
        $message = 'All fields are required.';
        $messageType = 'danger';
    } elseif ($password !== $confirm_password) {
        $message = 'Passwords do not match.';
        $messageType = 'danger';
    } else {
        $checkStmt = mysqli_prepare($conn, 'SELECT id FROM users WHERE username = ? OR email = ?');
        mysqli_stmt_bind_param($checkStmt, 'ss', $username, $email);
        mysqli_stmt_execute($checkStmt);
        mysqli_stmt_store_result($checkStmt);

        if (mysqli_stmt_num_rows($checkStmt) > 0) {
            $message = 'This username or email already exists. Please choose another username or email.';
            $messageType = 'danger';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, 'INSERT INTO users (username, email, password, full_name) VALUES (?, ?, ?, ?)');
            mysqli_stmt_bind_param($stmt, 'ssss', $username, $email, $hashed, $full_name);
            if (mysqli_stmt_execute($stmt)) {
                header('Location: login.php?registered=1');
                exit;
            } else {
                $message = 'Registration failed. Please try again.';
                $messageType = 'danger';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="stylee.css">
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-6">
                <div class="card modern-card border-0">
                    <div class="card-body p-4">
                        <h3 class="mb-3"><i class="fa-solid fa-user-plus me-2"></i>Create Account</h3>
                        <?php if ($message !== '') { ?>
                            <div class="alert alert-<?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
                        <?php } ?>
                        <form method="POST" novalidate>
                            <div class="mb-3"><label class="form-label">Username</label><input type="text" name="username" class="form-control" required></div>
                            <div class="mb-3"><label class="form-label">Full Name</label><input type="text" name="full_name" class="form-control" required></div>
                            <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
                            <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
                            <div class="mb-3"><label class="form-label">Confirm Password</label><input type="password" name="confirm_password" class="form-control" required></div>
                            <button class="btn btn-success w-100" type="submit"><i class="fa-solid fa-user-check me-1"></i>Register</button>
                        </form>
                        <div class="mt-3 text-center"><a href="login.php">Already have an account?</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
