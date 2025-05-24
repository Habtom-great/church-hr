<?php
// Language setup
$lang = $_GET['lang'] ?? 'en';
$translations = [
    'en' => [
        'title' => 'Member Registration System',
        'registered_members' => 'Registered Members',
        'id' => 'ID',
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
        'total_members' => 'Total Members',
        'invalid_ids' => 'member(s) have invalid ID format (highlighted in pink). Valid IDs must be exactly 5 digits.',
        'no_members' => 'No members registered yet.',
        'register_new' => 'Register New Member',
        'switch_lang' => 'Switch Language',
        'member_deleted' => 'Member deleted successfully.'
    ],
    'ti' => [
        'title' => 'ስርዓት መዝግብ ኣባላት',
        'registered_members' => 'ተመዝጊቦም ኣባላት',
        'id' => 'ተ.ቁ.',
        'member_id' => 'ኣባል ቁጽሪ',
        'first_name' => 'ስም',
        'middle_name' => 'ሽም ኣቦ',
        'last_name' => 'ሽም ኣቦ ኣቦ',
        'gender' => 'ፆታ',
        'email' => 'ኢመይል',
        'telephone' => 'ተሌፎን',
        'reg_date' => 'ዝመዝገበሉ ዕለት',
        'photo' => 'ፎቶ',
        'actions' => 'ተግባራት',
        'view' => 'ተመልከት',
        'edit' => 'ኣስተኻኽል',
        'delete' => 'ሰርዝ',
        'total_members' => 'ጠቕላላ ኣባላት',
        'invalid_ids' => 'ኣባላት ዘይቅኑዕ መለለዪ ኣለዎም። ቅኑዕ መለለዪ 5 ቁጽሪ ክኸውን ኣለዎ።',
        'no_members' => 'ክሳብ እሞ ኣባል ኣይተመዝገበን።',
        'register_new' => 'ሓድሽ ኣባል መዝግብ',
        'switch_lang' => 'ቋንቋ ለውጥ',
        'member_deleted' => 'ኣባል ብትኽክል ተሰርዖ።'
    ]
];
$t = $translations[$lang];

// Database connection
$pdo = new PDO("mysql:host=localhost;dbname=church_hr;charset=utf8", 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Fetch members
$members = $pdo->query("SELECT * FROM members")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $t['title'] ?></title>
    <style>
        body { font-family: Arial; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; }
        img { width: 50px; height: 50px; object-fit: cover; }
        .lang-switch { margin-bottom: 10px; }
        .success { color: green; font-weight: bold; }
    </style>
</head>
<body>

<h2><?= $t['registered_members'] ?></h2>

<div class="lang-switch">
    <a href="?lang=en">English</a> | <a href="?lang=ti">ትግርኛ</a>
</div>

<?php if (isset($_GET['deleted'])): ?>
    <div class="success"><?= $t['member_deleted'] ?></div>
<?php endif; ?>

<?php if (count($members) === 0): ?>
    <p><?= $t['no_members'] ?></p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th><?= $t['id'] ?></th>
                <th><?= $t['member_id'] ?></th>
                <th><?= $t['first_name'] ?></th>
                <th><?= $t['middle_name'] ?></th>
                <th><?= $t['last_name'] ?></th>
                <th><?= $t['gender'] ?></th>
                <th><?= $t['email'] ?></th>
                <th><?= $t['telephone'] ?></th>
                <th><?= $t['reg_date'] ?></th>
                <th><?= $t['photo'] ?></th>
                <th><?= $t['actions'] ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($members as $member): ?>
                <tr>
                    <td><?= $member['id'] ?></td>
                    <td><?= $member['member_id'] ?></td>
                    <td><?= $member['first_name'] ?></td>
                    <td><?= $member['middle_name'] ?></td>
                    <td><?= $member['last_name'] ?></td>
                    <td><?= $member['gender'] ?></td>
                    <td><?= $member['email'] ?></td>
                    <td><?= $member['telephone'] ?></td>
                    <td><?= $member['registration_date'] ?></td>
                    <td>
                        <?php if (!empty($member['photo'])): ?>
                            <img src="<?= $member['photo'] ?>" alt="Photo">
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="view_member.php?id=<?= $member['id'] ?>"><?= $t['view'] ?></a> |
                        <a href="edit_member.php?id=<?= $member['id'] ?>"><?= $t['edit'] ?></a> |
                        <a href="delete_member.php?id=<?= $member['id'] ?>&lang=<?= $lang ?>" onclick="return confirm('Are you sure you want to delete this member?')"><?= $t['delete'] ?></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>