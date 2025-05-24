

<?php
ini_set('log_errors', 1);
ini_set('error_log', __DIR__.'/logs/php-errors.log');
// Start session and include database connection
require_once 'config/db.php';
session_start();

// Set default language to English if not set
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'english';
}

// Language switching logic via GET parameter
if (isset($_GET['lang']) && in_array($_GET['lang'], ['english', 'tigrinya'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

// Load the language file
$lang_file = 'languages/' . $_SESSION['lang'] . '.php';
if (file_exists($lang_file)) {
    include $lang_file;
} else {
    include 'languages/english.php';
    $_SESSION['lang'] = 'english';
}

// Default error message
$error = '';

// Handle login submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = $lang['login_error_empty'] ?? 'Please fill in all fields.';
    } else {
        $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                header('Location: dashboard.php');
                exit();
            } else {
                $error = $lang['login_error_credentials'] ?? 'Invalid email or password.';
            }
        } else {
            $error = $lang['login_error_credentials'] ?? 'Invalid email or password.';
        }

        $stmt->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang'] === 'tigrinya' ? 'ti' : 'en'; ?>" dir="<?php echo $_SESSION['lang'] === 'tigrinya' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo $lang['login_title'] ?? 'Login'; ?> | Church HR</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap & Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Ethiopic:wght@400;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #3a5a78;
            --accent-color: #ffc107;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f6f9;
            height: 100vh;
            display: flex;
            align-items: center;
        }

        .tigrinya {
            font-family: 'Noto Sans Ethiopic', sans-serif;
            text-align: left; /* Force left alignment for Tigrinya text */
        }

        .login-container {
            max-width: 500px;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            text-align: left; /* Ensure form content aligns left */
        }

        .btn-login {
            background-color: var(--primary-color);
            color: white;
            font-weight: 500;
        }

        .language-switcher .btn {
            border: 1px solid var(--primary-color);
            margin: 0 5px;
        }

        .language-switcher .btn.active {
            background-color: var(--primary-color);
            color: white;
        }
        
        /* RTL adjustments only for script direction */
        [dir="rtl"] .form-control, 
        [dir="rtl"] .form-label {
            text-align: left; /* Keep text left-aligned */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center w-100">
            <div class="col-md-6">
                <div class="login-container <?php echo $_SESSION['lang'] === 'tigrinya' ? 'tigrinya' : ''; ?>">
                    <div class="text-center mb-4">
                        <img src="assets/image/2024-09-03 23.29.00.jpg" alt="Church Logo" height="80">
                        <h2><?php echo $lang['login_title'] ?? 'Login'; ?></h2>
                        <p><?php echo $lang['login_subtitle'] ?? 'Please sign in to continue'; ?></p>
                    </div>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form action="login.php" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <?php echo $lang['email_label'] ?? 'Email'; ?>
                            </label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <?php echo $lang['password_label'] ?? 'Password'; ?>
                            </label>
                            <input type="password" id="password" name="password" class="form-control" required>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-login">
                                <i class="fas fa-sign-in-alt"></i>
                                <?php echo $lang['login_button'] ?? 'Login'; ?>
                            </button>
                        </div>

                        <div class="text-center">
                            <a href="forgot-password.php?lang=<?php echo $_SESSION['lang']; ?>">
                                <?php echo $lang['forgot_password'] ?? 'Forgot Password?'; ?>
                            </a>
                        </div>
                    </form>

                    <div class="language-switcher text-center mt-4">
                        <a href="login.php?lang=english" class="btn btn-sm <?php echo $_SESSION['lang'] === 'english' ? 'active' : ''; ?>">English</a>
                        <a href="login.php?lang=tigrinya" class="btn btn-sm <?php echo $_SESSION['lang'] === 'tigrinya' ? 'active' : ''; ?>">ትግርኛ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>