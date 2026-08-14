<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: list.php');
    exit;
}

include 'database.php';

$message = '';
$messageType = 'info';

if (isset($_GET['registered']) && $_GET['registered'] == '1') {
    $message = 'Registration successful. You can now login.';
    $messageType = 'success';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $message = 'Please enter both email and password.';
        $messageType = 'danger';
    } else {
        $stmt = mysqli_prepare($conn, 'SELECT id, username, email, full_name, password FROM users WHERE email = ?');
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['full_name'] = $user['full_name'];
            header('Location: list.php');
            exit;
        }

        $message = 'Invalid email or password.';
        $messageType = 'danger';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="stylee.css">
</head>
<body class="login-page">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card modern-card border-0">
                    <div class="card-body p-4">
                        <h3 class="mb-3"><i class="fa-solid fa-right-to-bracket me-2"></i>Login</h3>
                        <p class="text-muted">Access your user management dashboard.</p>
                        <?php if ($message !== '') { ?>
                            <div class="alert alert-<?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
                        <?php } ?>
                        <form method="POST" novalidate>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <button class="btn btn-primary w-100" type="submit"><i class="fa-solid fa-sign-in-alt me-1"></i>Login</button>
                        </form>
                        <div class="mt-3 text-center">
                            <a href="register.php">Create a new account</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
