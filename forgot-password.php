<?php
require_once 'config/db.php';
session_start();

// Error reporting (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Language setup
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'english';
}

if (isset($_GET['lang']) && in_array($_GET['lang'], ['english', 'tigrinya'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

$lang_file = 'languages/' . $_SESSION['lang'] . '.php';
if (file_exists($lang_file)) {
    include $lang_file;
} else {
    include 'languages/english.php';
}

// Initialize variables
$email = '';
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    
    if (empty($email)) {
        $error = $lang['forgot_password_error_empty'] ?? 'Please enter your email address.';
    } else {
        try {
            $stmt = $conn->prepare("SELECT id, name FROM users WHERE email = ?");
            if (!$stmt) throw new Exception($conn->error);
            
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                $token = bin2hex(random_bytes(32));
                $expires = date("Y-m-d H:i:s", time() + 3600);
                
                // Check if columns exist
                $check = $conn->query("SHOW COLUMNS FROM users LIKE 'reset_token'");
                if ($check->num_rows == 0) {
                    throw new Exception("Password reset columns not found in database");
                }
                
                $update = $conn->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?");
                if (!$update) throw new Exception($conn->error);
                
                $update->bind_param("ssi", $token, $expires, $user['id']);
                $update->execute();
                $update->close();
                
                // In real app, send email here
                $reset_link = "http://".$_SERVER['HTTP_HOST']."/reset-password.php?token=$token";
                $message = sprintf($lang['forgot_password_success'] ?? 'Password reset link has been sent to %s', $email);
                $message .= "<br><small>DEMO: <a href='$reset_link'>Reset Link</a></small>";
            } else {
                $error = $lang['forgot_password_error_not_found'] ?? 'Email not found.';
            }
            
            $stmt->close();
        } catch (Exception $e) {
            $error = "System error. Please try later.";
            // Log this error: error_log($e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang'] === 'tigrinya' ? 'ti' : 'en'; ?>" dir="<?php echo $_SESSION['lang'] === 'tigrinya' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo $lang['forgot_password_title'] ?? 'Forgot Password'; ?> | Church HR</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap & Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Ethiopic:wght@400;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary-color: #3a5a78; --accent-color: #ffc107; }
        body { font-family: 'Roboto', sans-serif; background-color: #f4f6f9; height: 100vh; display: flex; align-items: center; }
        .tigrinya { font-family: 'Noto Sans Ethiopic', sans-serif; text-align: left; }
        .login-container { max-width: 500px; background-color: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1); }
        .btn-primary { background-color: var(--primary-color); border: none; }
        .btn-primary:hover { background-color: #2c4761; }
        [dir="rtl"] .form-control, [dir="rtl"] .form-label { text-align: left; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="login-container <?php echo $_SESSION['lang'] === 'tigrinya' ? 'tigrinya' : ''; ?>">
                    <div class="text-center mb-4">
                        <img src="assets/image/2024-09-03 23.29.00.jpg" alt="Church Logo" height="80">
                        <h2><?php echo $lang['forgot_password_title'] ?? 'Forgot Password'; ?></h2>
                        <p><?php echo $lang['forgot_password_subtitle'] ?? 'Enter your email to reset your password'; ?></p>
                    </div>

                    <?php if (!empty($message)): ?>
                        <div class="alert alert-success"><?php echo $message; ?></div>
                    <?php endif; ?>
                    
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form action="forgot-password.php" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label"><?php echo $lang['email_label'] ?? 'Email'; ?></label>
                            <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-key"></i> <?php echo $lang['reset_password_button'] ?? 'Reset Password'; ?>
                            </button>
                        </div>

                        <div class="text-center">
                            <a href="login.php?lang=<?php echo $_SESSION['lang']; ?>">
                                <i class="fas fa-arrow-left"></i> <?php echo $lang['back_to_login'] ?? 'Back to Login'; ?>
                            </a>
                        </div>
                    </form>

                    <div class="language-switcher text-center mt-4">
                        <a href="forgot-password.php?lang=english" class="btn btn-sm <?php echo $_SESSION['lang'] === 'english' ? 'active' : ''; ?>">English</a>
                        <a href="forgot-password.php?lang=tigrinya" class="btn btn-sm <?php echo $_SESSION['lang'] === 'tigrinya' ? 'active' : ''; ?>">ትግርኛ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>