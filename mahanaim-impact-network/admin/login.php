<?php
// === admin/login.php ===
session_start();
require_once '../config/db.php';

// Redirect if already logged in
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit();
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
            $stmt->execute([$username]);
            $admin = $stmt->fetch();
            
            if ($admin && password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                header('Location: dashboard.php');
                exit();
            } else {
                $error = "Invalid username or password";
            }
        } catch (PDOException $e) {
            $error = "Login error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Mahanaim Impact Network</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #4B0082 0%, #6A0DAD 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-card {
            background: white;
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 450px;
            width: 100%;
        }
        .logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        .logo i {
            font-size: 4rem;
            color: #D4AF37;
        }
        .logo h3 {
            color: #4B0082;
            font-weight: 700;
            margin-top: 1rem;
        }
        .btn-login {
            background: #4B0082;
            color: white;
            padding: 0.75rem;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-login:hover {
            background: #6A0DAD;
            color: white;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo">
            <i class="fas fa-shield-alt"></i>
            <h3>Admin Portal</h3>
            <p class="text-muted mb-0">Mahanaim Impact Network</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label fw-bold">Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" name="username" class="form-control" required autofocus>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="form-label fw-bold">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" class="form-control" required>
                </div>
            </div>
            
            <button type="submit" class="btn btn-login w-100">
                <i class="fas fa-sign-in-alt me-2"></i>Login
            </button>
        </form>
        
        <div class="text-center mt-4">
            <a href="../index.php" class="text-muted text-decoration-none">
                <i class="fas fa-arrow-left me-1"></i>Back to Website
            </a>
        </div>
    </div>
</body>
</html>
