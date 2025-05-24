<?php
// Database connection
$host = 'localhost';
$dbname = 'church_hr';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Language handling
$lang = $_GET['lang'] ?? 'en';
$translations = [
    'en' => [
        'title' => 'Member Registration System',
        'registered_members' => 'Registered Members',
        'order_no' => 'Order No',
        'member_id' => 'Member ID',
        'first_name' => 'First Name',
        'middle_name' => 'Middle Name',
        'last_name' => 'Last Name',
        'gender' => 'Gender',
        'email' => 'Email',
        'telephone' => 'Phone',
        'reg_date' => 'Registration Date',
        'photo' => 'Photo',
        'actions' => 'Actions',
        'view' => 'View',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'total_members' => 'Total members',
        'invalid_ids' => 'member(s) have invalid ID format (highlighted in pink). Valid IDs must be exactly 5 digits.',
        'no_members' => 'No members registered yet.',
        'register_new' => 'Register New Member',
        'switch_lang' => 'ቋንቋ ለውጥ'
    ],
    'ti' => [
        'title' => 'ስርዓት መዝግብ ኣባላት',
        'registered_members' => 'ዝተመዝገቦ ኣባላት',
        'order_no' => 'ትዕዛዝ ቁጽሪ',
        'member_id' => 'ኣባል ቁጽሪ',
        'first_name' => 'ስም',
        'middle_name' => 'ኣባ ',
        'last_name' => 'ኣባ ሓግ',
        'gender' => 'ሓግ',
        'email' => 'ኢመይል',
        'telephone' => 'ተሌፎን',
        'reg_date' => 'ናይ ምዝገባ ግዜ',
        'photo' => 'ፎቶ',
        'actions' => 'ተግባራት',
        'view' => 'ተመልከት',
        'edit' => 'ኣስተኻኽል',
        'delete' => 'ሰርዝ',
        'total_members' => 'ጠቕላላ ኣባላት',
        'invalid_ids' => 'ኣባላት ዘይቅኑዕ መለለዪ ኣለዎም (ብሕጽረት ቀለም ተራእዩ). ቅኑዕ መለለዪ 5 ቁጽሪ ክኸውን ኣለዎ።',
        'no_members' => 'ክሳብ እዚ ላዕለዋይ ኣባል ኣይተመዝገበን።',
        'register_new' => 'ሓድሽ ኣባል ኣመዝግብ',
        'switch_lang' => 'Change Language'
    ]
];

// Fetch members
try {
    $stmt = $pdo->query("SELECT * FROM members ORDER BY registration_date DESC");
    $members = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching members: " . $e->getMessage());
}
?>
<body>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $translations[$lang]['title'] ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Ethiopic&family=Roboto&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: <?= $lang === 'ti' ? "'Noto Sans Ethiopic', sans-serif" : "'Roboto', sans-serif" ?>;
            background: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 1300px;
            margin: 2rem auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
        }
        header {
            background: #004080;
            color: white;
            padding: 1rem;
            text-align: center;
            position: relative;
        }
        .language-switch {
            position: absolute;
            top: 10px;
            right: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: <?= $lang === 'ti' ? 'right' : 'left' ?>;
        }
        th {
            background: #004080;
            color: white;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        .invalid-id {
            background-color: #ffe5e5;
        }
        .btn {
            padding: 6px 12px;
            border-radius: 4px;
            color: white;
            text-decoration: none;
            margin-right: 5px;
            font-size: 0.8rem;
        }
        .btn-view { background: #5cb85c; }
        .btn-edit { background: #f0ad4e; }
        .btn-delete { background: #d9534f; }
        .btn:hover { opacity: 0.85; }
        img.member-photo {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
        }
        .stats {
            margin-top: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<header>
    <h1><?= $translations[$lang]['registered_members'] ?></h1>
    <div class="language-switch">
        <a href="?lang=<?= $lang === 'en' ? 'ti' : 'en' ?>" class="btn btn-view">
            <?= $translations[$lang]['switch_lang'] ?>
        </a>
    </div>
</header>
<!-- Buttons Side by Side -->
<div style="max-width: 900px; margin: 10px auto; display: flex; justify-content: space-between; gap: 10px; flex-wrap: wrap;">
  <!-- Add New Member Button -->
  <a href="add_member.php" class="btn btn-secondary" style="text-decoration:none; background:#6c757d; padding:10px 16px; color:white; border-radius:5px; display:inline-flex; align-items:center;">
    <i class="fas fa-user-plus" style="margin-right: 6px;"></i>
    <span class="dual-language">
        <span class="english">Add New Member</span>
        <span class="tigrinya"> / አባል ኣክል</span>
    </span>
  </a>

  <!-- Back Button -->
  <a href="index.php" class="btn btn-secondary" style="text-decoration:none; background:#6c757d; padding:10px 16px; color:white; border-radius:5px; display:inline-flex; align-items:center;">
    <i class="fas fa-home" style="margin-right: 6px;"></i>
    <span class="dual-language">
        <span class="english">Back to Home</span>
        <span class="tigrinya"> / ቤት ተመለስ</span>
    </span>
  </a>
</div>

<div class="container">
    <?php if (count($members) > 0): ?>
    <table>
        <thead>
            <tr>
                <th><?= $translations[$lang]['order_no'] ?></th>
                <th><?= $translations[$lang]['photo'] ?></th>
                <th><?= $translations[$lang]['member_id'] ?></th>
                <th><?= $translations[$lang]['last_name'] ?></th>
                <th><?= $translations[$lang]['middle_name'] ?></th>
                <th><?= $translations[$lang]['first_name'] ?></th>
                <th><?= $translations[$lang]['gender'] ?></th>
                <th><?= $translations[$lang]['email'] ?></th>
                <th><?= $translations[$lang]['telephone'] ?></th>
                <th><?= $translations[$lang]['reg_date'] ?></th>
                <th><?= $translations[$lang]['actions'] ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $orderNo = 1;
            foreach ($members as $member):
                $isValidId = preg_match('/^\d{5}$/', $member['member_id']);
                $imagePath = !empty($member['image']) && file_exists("uploads/" . $member['image'])
                    ? "uploads/" . htmlspecialchars($member['image'])
                    : "default_avatar.png";
            ?>
            <tr class="<?= !$isValidId ? 'invalid-id' : '' ?>">
                <td><?= $orderNo++ ?></td>
                <td><img src="<?= $imagePath ?>" class="member-photo" alt="Member photo"></td>
                <td><?= htmlspecialchars($member['id']) ?></td>
                <td><?= htmlspecialchars($member['last_name']) ?></td>
                <td><?= htmlspecialchars($member['middle_name']) ?></td>
                <td><?= htmlspecialchars($member['first_name']) ?></td>
                <td><?= htmlspecialchars($member['gender']) ?></td>
                <td><?= htmlspecialchars($member['email']) ?></td>
                <td><?= htmlspecialchars($member['telephone']) ?></td>
                <td><?= htmlspecialchars($member['registration_date']) ?></td>
                <td>
                    <a href="view_member.php?id=<?= $member['id'] ?>" class="btn btn-view"><?= $translations[$lang]['view'] ?></a>
                    <a href="edit_member-.php?id=<?= $member['id'] ?>" class="btn btn-edit"><?= $translations[$lang]['edit'] ?></a>
                    <a href="delete_member.php?id=<?= $member['id'] ?>" class="btn btn-delete" onclick="return confirm('Are you sure?')"><?= $translations[$lang]['delete'] ?></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="stats">
        <?= $translations[$lang]['total_members'] ?>: <?= count($members) ?><br>
        <?php 
            $invalidCount = count(array_filter($members, fn($m) => !preg_match('/^\d{5}$/', $m['member_id'])));
            if ($invalidCount > 0) {
                echo "<span style='color:red;'>$invalidCount {$translations[$lang]['invalid_ids']}</span>";
            }
        ?>
    </div>
    <?php else: ?>
        <p><?= $translations[$lang]['no_members'] ?></p>
    <?php endif; ?>

</body>
</html>
