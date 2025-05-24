<?php
// Initialize with default values
$input = [
    'name' => $_POST['name'] ?? '',
    'email' => $_POST['email'] ?? '',
    'phone' => $_POST['phone'] ?? '',
    'role' => $_POST['role'] ?? 'member',
    'password' => '',
    'confirm_password' => '',
    'terms' => false
];

// Language setup
$currentLang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'en';
$_SESSION['lang'] = $currentLang;
$isTigrinya = ($currentLang === 'ti');

// Translations
$lang = [
    'en' => [
        'register_title' => 'Create Account',
        'register_subtitle' => 'Join our community',
        'name_label' => 'Full Name',
        'email_label' => 'Email',
        'phone_label' => 'Phone',
        'role_label' => 'Role',
        'role_member' => 'Member',
        'role_volunteer' => 'Volunteer',
        'role_staff' => 'Staff',
        'password_label' => 'Password',
        'confirm_password_label' => 'Confirm Password',
        'password_hint' => 'At least 8 characters',
        'agree_terms' => 'I agree to terms',
        'register_button' => 'Register',
        'already_have_account' => 'Have an account?',
        'login_link' => 'Login',
        'error_password_mismatch' => 'Passwords don\'t match',
        'error_terms' => 'You must accept terms'
    ],
    'ti' => [
        'register_title' => 'ኣካውንት ምፍጣር',
        'register_subtitle' => 'ኣብ ማሕበርና ተጸንበሩ',
        'name_label' => 'ምሉእ ስም',
        'email_label' => 'ኢመይል',
        'phone_label' => 'ቁጽሪ ስልኪ',
        'role_label' => 'ሚና',
        'role_member' => 'ኣባል',
        'role_volunteer' => 'በጻሕቲ',
        'role_staff' => 'ሰራሕተኛ',
        'password_label' => 'መሕለፊ ቃል',
        'confirm_password_label' => 'መሕለፊ ቃል ኣረጋግጽ',
        'password_hint' => 'ውሕድ 8 ፊደላት',
        'agree_terms' => 'ምስ ውዕል ተሰማምዕኩም',
        'register_button' => 'ተመዝገቡ',
        'already_have_account' => 'ኣካውንት ኣለኩም?',
        'login_link' => 'እቶ',
        'error_password_mismatch' => 'መሕለፊ ቃላት ኣይመሳሰሉን',
        'error_terms' => 'ነቲ ውዕል ክትቅበል ኣለካ'
    ]
];
?>

<!DOCTYPE html>
<html lang="<?= $currentLang ?>" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($lang[$currentLang]['register_title']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Ethiopic:wght@400;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    :root {
        --primary: #4361ee;
        --light: #f8f9fa;
        --dark: #212529;
        --border: #dee2e6;
        --error: #dc3545;
    }
    
    body {
        font-family: <?= $isTigrinya ? "'Noto Sans Ethiopic', sans-serif" : "'Roboto', sans-serif" ?>;
        background: var(--light);
        color: var(--dark);
        padding: 1rem;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        direction: ltr;
        text-align: left;
    }
    
    .card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        width: 100%;
        max-width: 500px;
        padding: 2rem;
        position: relative;
    }
    
    .lang-switcher {
        position: absolute;
        top: 1rem;
        right: 1rem;
    }
    
    .lang-btn {
        background: none;
        border: 1px solid var(--border);
        border-radius: 4px;
        padding: 0.25rem 0.5rem;
        font-size: 0.8rem;
        cursor: pointer;
        margin-left: 0.25rem;
    }
    
    .lang-btn.active {
        background: var(--primary);
        color: white;
    }
    
    .header {
        text-align: center;
        margin-bottom: 1.5rem;
    }
    
    .header h2 {
        color: var(--primary);
        margin-bottom: 0.5rem;
        font-size: 1.5rem;
    }
    
    .form-group {
        margin-bottom: 1rem;
    }
    
    label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
    }
    
    input, select {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid var(--border);
        border-radius: 6px;
        font-size: 1rem;
    }
    
    input:focus, select:focus {
        border-color: var(--primary);
        outline: none;
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
    }
    
    .is-invalid {
        border-color: var(--error) !important;
    }
    
    .error-msg {
        color: var(--error);
        font-size: 0.8rem;
        margin-top: 0.25rem;
    }
    
    .btn {
        width: 100%;
        padding: 0.75rem;
        background: var(--primary);
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 1rem;
        margin-top: 1rem;
        cursor: pointer;
    }
    
    .btn:hover {
        background: #3a56d4;
    }
    
    .login-link {
        text-align: center;
        margin-top: 1rem;
    }
    
    .row {
        display: flex;
        gap: 1rem;
    }
    
    .col {
        flex: 1;
    }
    
    @media (max-width: 576px) {
        .row {
            flex-direction: column;
            gap: 0;
        }
    }
    </style>
</head>
<body>
    <div class="card">
        <div class="lang-switcher">
            <button class="lang-btn <?= $currentLang === 'en' ? 'active' : '' ?>" 
                    onclick="window.location.href='?lang=en'">EN</button>
            <button class="lang-btn <?= $currentLang === 'ti' ? 'active' : '' ?>" 
                    onclick="window.location.href='?lang=ti'">ትግ</button>
        </div>
        
        <div class="header">
            <h2><?= htmlspecialchars($lang[$currentLang]['register_title']) ?></h2>
            <p><?= htmlspecialchars($lang[$currentLang]['register_subtitle']) ?></p>
        </div>
        
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label><?= htmlspecialchars($lang[$currentLang]['name_label']) ?> *</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($input['name']) ?>" required>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label><?= htmlspecialchars($lang[$currentLang]['email_label']) ?> *</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($input['email']) ?>" required>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label><?= htmlspecialchars($lang[$currentLang]['phone_label']) ?></label>
                        <input type="tel" name="phone" value="<?= htmlspecialchars($input['phone']) ?>">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label><?= htmlspecialchars($lang[$currentLang]['role_label']) ?> *</label>
                        <select name="role" required>
                            <option value="member"><?= htmlspecialchars($lang[$currentLang]['role_member']) ?></option>
                            <option value="volunteer"><?= htmlspecialchars($lang[$currentLang]['role_volunteer']) ?></option>
                            <option value="staff"><?= htmlspecialchars($lang[$currentLang]['role_staff']) ?></option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label><?= htmlspecialchars($lang[$currentLang]['password_label']) ?> *</label>
                <input type="password" name="password" required>
                <small><?= htmlspecialchars($lang[$currentLang]['password_hint']) ?></small>
            </div>
            
            <div class="form-group">
                <label><?= htmlspecialchars($lang[$currentLang]['confirm_password_label']) ?> *</label>
                <input type="password" name="confirm_password" required>
            </div>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="terms" required>
                    <?= htmlspecialchars($lang[$currentLang]['agree_terms']) ?>
                </label>
            </div>
            
            <button type="submit" class="btn">
                <i class="fas fa-user-plus"></i> <?= htmlspecialchars($lang[$currentLang]['register_button']) ?>
            </button>
            
            <p class="login-link">
                <?= htmlspecialchars($lang[$currentLang]['already_have_account']) ?> 
                <a href="login.php?lang=<?= $currentLang ?>"><?= htmlspecialchars($lang[$currentLang]['login_link']) ?></a>
            </p>
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelector('form').addEventListener('submit', function(e) {
            const pwd1 = document.querySelector('input[name="password"]').value;
            const pwd2 = document.querySelector('input[name="confirm_password"]').value;
            const terms = document.querySelector('input[name="terms"]').checked;
            
            if (pwd1 !== pwd2) {
                e.preventDefault();
                alert('<?= addslashes($lang[$currentLang]['error_password_mismatch']) ?>');
            }
            
            if (!terms) {
                e.preventDefault();
                alert('<?= addslashes($lang[$currentLang]['error_terms']) ?>');
            }
        });
    });
    </script>
</body>
</html>