<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start([
    'cookie_httponly' => true,
    'cookie_secure' => isset($_SERVER['HTTPS']),
    'use_strict_mode' => true
]);

require_once 'config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Determine which profile to show
$requestedId = isset($_GET['id']) ? intval($_GET['id']) : $_SESSION['user_id'];
$currentUserId = $_SESSION['user_id'];
$currentUserRole = $_SESSION['role'] ?? 'user';

// Only admins can view other users' profiles
if ($requestedId !== $currentUserId && $currentUserRole !== 'admin') {
    die("Access denied: You are not authorized to view this profile.");
}

// Language
$availableLanguages = ['english', 'tigrinya'];
$currentLang = in_array($_SESSION['user_lang'] ?? '', $availableLanguages) ? $_SESSION['user_lang'] : 'english';
$langFile = __DIR__ . '/languages/' . $currentLang . '.php';

if (!file_exists($langFile)) {
    die("Language file missing");
}
$lang = include $langFile;
if (!is_array($lang)) {
    die("Invalid language file format");
}

// Fetch user data
$query = $conn->prepare("SELECT name, email, phone, role, created_at FROM users WHERE id = ?");
$query->bind_param("i", $requestedId);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();
$query->close();

if (!$user) {
    die("User not found.");
}
?>
<!DOCTYPE html>
<html lang="<?= $currentLang === 'tigrinya' ? 'ti' : 'en'; ?>" dir="<?= $currentLang === 'tigrinya' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($lang['profile_title'] ?? 'My Profile'); ?> | Church HR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3a5a78;
            --secondary-color: #6c757d;
            --light-color: #f8f9fa;
        }
        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--light-color);
            padding-top: 2rem;
        }
        .profile-card {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .profile-header {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 1.5rem;
            text-align: center;
        }
        .profile-body {
            padding: 2rem;
        }
        .info-label {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        .info-value {
            margin-bottom: 1.5rem;
            padding-left: 1rem;
        }
        [dir="rtl"] .info-value {
            padding-left: 0;
            padding-right: 1rem;
        }
        .tigrinya {
            font-family: 'Noto Sans Ethiopic', sans-serif;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="profile-card">
            <div class="profile-header">
                <h2 class="<?= $currentLang === 'tigrinya' ? 'tigrinya' : ''; ?>">
                    <i class="fas fa-user-circle me-2"></i>
                    <?= htmlspecialchars($lang['profile_title'] ?? 'Member Profile'); ?>
                </h2>
            </div>
            <div class="profile-body">
                <div class="mb-4">
                    <div class="info-label <?= $currentLang === 'tigrinya' ? 'tigrinya' : ''; ?>">
                        <?= htmlspecialchars($lang['name_label'] ?? 'Name'); ?>
                    </div>
                    <div class="info-value">
                        <?= htmlspecialchars($user['name']); ?>
                    </div>
                </div>
                <div class="mb-4">
                    <div class="info-label <?= $currentLang === 'tigrinya' ? 'tigrinya' : ''; ?>">
                        <?= htmlspecialchars($lang['email_label'] ?? 'Email'); ?>
                    </div>
                    <div class="info-value">
                        <?= htmlspecialchars($user['email']); ?>
                    </div>
                </div>
                <div class="mb-4">
                    <div class="info-label <?= $currentLang === 'tigrinya' ? 'tigrinya' : ''; ?>">
                        <?= htmlspecialchars($lang['phone_label'] ?? 'Phone'); ?>
                    </div>
                    <div class="info-value">
                        <?= htmlspecialchars($user['phone'] ?: $lang['not_provided'] ?? 'Not provided'); ?>
                    </div>
                </div>
                <div class="mb-4">
                    <div class="info-label <?= $currentLang === 'tigrinya' ? 'tigrinya' : ''; ?>">
                        <?= htmlspecialchars($lang['role_label'] ?? 'Role'); ?>
                    </div>
                    <div class="info-value">
                        <?= htmlspecialchars(ucfirst($user['role'])); ?>
                    </div>
                </div>
                <div class="mb-4">
                    <div class="info-label <?= $currentLang === 'tigrinya' ? 'tigrinya' : ''; ?>">
                        <?= htmlspecialchars($lang['joined_label'] ?? 'Member Since'); ?>
                    </div>
                    <div class="info-value">
                        <?= htmlspecialchars(date("F j, Y", strtotime($user['created_at']))); ?>
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-4">
                    <?php if ($requestedId === $currentUserId): ?>
                        <a href="edit_profile.php" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>
                            <?= htmlspecialchars($lang['edit_profile'] ?? 'Edit Profile'); ?>
                        </a>
                    <?php endif; ?>
                    <a href="dashboard.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>
                        <?= htmlspecialchars($lang['back_to_dashboard'] ?? 'Back to Dashboard'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
