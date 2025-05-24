<?php
define('BASE_URL', '/church-hr/');
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/auth_functions.php';

// Redirect if not logged in
redirectIfNotLoggedIn();
?>

<div class="container mt-4">
    <h2 class="<?php echo $_SESSION['lang'] === 'tigrinya' ? 'tigrinya' : ''; ?>">
        <?php echo $lang['dashboard_welcome']; ?>, <?php echo $_SESSION['user_name']; ?>
    </h2>
    
    <!-- Dashboard content -->
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>