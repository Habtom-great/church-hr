<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start secure session
session_start([
    'cookie_httponly' => true,
    'cookie_secure' => isset($_SERVER['HTTPS']),
    'use_strict_mode' => true
]);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'config/db.php';

// Validate database connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Get and validate member ID
$member_id = isset($_GET['id']) ? trim($_GET['id']) : '';



// Convert to integer for database query
$member_id_int = (int)$member_id;

// Language system
$availableLanguages = ['english', 'tigrinya'];
$currentLang = $_SESSION['user_lang'] ?? 'english';
$langFile = __DIR__ . '/languages/' . $currentLang . '.php';

if (!file_exists($langFile)) {
    // Fallback language data
    $lang = [
        'contact_title' => 'Contact Member',
        'member_not_found' => 'Member not found',
        'contact_form' => 'Contact Form',
        'message_label' => 'Your Message',
        'send_button' => 'Send Message',
        'back_to_profile' => 'Back to Profile',
        'error_message_empty' => 'Message is required'
    ];
} else {
    $lang = include $langFile;
    if (!is_array($lang)) {
        die("Invalid language file format");
    }
}

// Fetch member details including profile image
$stmt = $conn->prepare("SELECT id, name, email, image FROM users WHERE id = ?");
if (!$stmt) {
    die("Database error: " . $conn->error);
}

if (!$stmt->bind_param("i", $member_id_int)) {
    die("Database error: Failed to bind parameters");
}

if (!$stmt->execute()) {
    die("Database error: Failed to execute query");
}

$result = $stmt->get_result();
$member = $result->fetch_assoc();
$stmt->close();

if (!$member) {
    die($lang['member_not_found'] ?? 'Member not found');
}

// Set default image if not provided
$profile_image = !empty($member['profile_image']) ? $member['profile_image'] : 'assets/images/default-avatar.png';

// Handle form submission
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message'] ?? '');
    
    if (empty($message)) {
        $errors['message'] = $lang['error_message_empty'] ?? 'Message is required';
    } else {
        // Process the message (store in database, send email, etc.)
        // This is just a placeholder - implement your actual messaging logic
        
        // For now, we'll just show a success message
        $success = true;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="<?php echo $currentLang === 'tigrinya' ? 'ti' : 'en'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($lang['contact_title'] ?? 'Contact Member'); ?> | Church HR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .contact-card {
            max-width: 600px;
            margin: 2rem auto;
        }
        .member-id-display {
            font-family: monospace;
            font-size: 1.1rem;
            letter-spacing: 1px;
        }
        .profile-image {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .member-header {
            background: linear-gradient(to right, #3a5a78, #6c757d);
            color: white;
            padding: 2rem;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card contact-card">
            <div class="member-header">
                <img src="<?php echo htmlspecialchars($profile_image); ?>" 
                     alt="Profile Image" 
                     class="profile-image mb-3"
                     onerror="this.src='assets/images/default-avatar.png'">
                <h2><?php echo htmlspecialchars($member['name']); ?></h2>
                <p class="member-id-display mb-0">
                    ID: <?php echo str_pad($member['id'], 5, '0', STR_PAD_LEFT); ?>
                </p>
            </div>
            
            <div class="card-body">
                <?php if (isset($success)): ?>
                <div class="alert alert-success">
                    Your message has been sent successfully!
                </div>
                <?php endif; ?>
                
                <h4 class="mb-4"><?php echo htmlspecialchars($lang['contact_form'] ?? 'Contact Form'); ?></h4>
                
                <form method="POST">
                    <div class="mb-3">
                        <label for="message" class="form-label">
                            <?php echo htmlspecialchars($lang['message_label'] ?? 'Your Message'); ?> *
                        </label>
                        <textarea class="form-control <?php echo isset($errors['message']) ? 'is-invalid' : ''; ?>" 
                                  id="message" name="message" rows="5" required></textarea>
                        <?php if (isset($errors['message'])): ?>
                            <div class="invalid-feedback">
                                <?php echo htmlspecialchars($errors['message']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="member_profile.php?id=<?php echo $member_id; ?>" class="btn btn-secondary">
                            <?php echo htmlspecialchars($lang['back_to_profile'] ?? 'Back to Profile'); ?>
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <?php echo htmlspecialchars($lang['send_button'] ?? 'Send Message'); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fallback for broken images
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('img').forEach(img => {
                img.onerror = function() {
                    this.src = 'assets/images/default-avatar.png';
                };
            });
        });
    </script>
</body>
</html>