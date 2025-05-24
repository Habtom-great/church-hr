


<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "church_hr";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get member ID
$memberId = $_GET['id'] ?? null;
if (!$memberId || !is_numeric($memberId)) {
    die("Invalid member ID.");
}

// Function to handle file uploads
function handleFileUpload($fileInput, $uploadDir, $currentFile = null) {
    if (!empty($fileInput['name'])) {
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $fileName = uniqid() . '_' . basename($fileInput['name']);
        $targetPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($fileInput['tmp_name'], $targetPath)) {
            // Delete old file if it exists
            if ($currentFile && file_exists($uploadDir . $currentFile)) {
                unlink($uploadDir . $currentFile);
            }
            return $fileName;
        }
    }
    return $currentFile; // Return existing file if no new upload
}

// Fetch member details
$stmt = $conn->prepare("SELECT * FROM members WHERE id = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

if (!$stmt->bind_param("i", $memberId)) {
    die("Bind failed: " . $stmt->error);
}

if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

$result = $stmt->get_result();
$member = $result->fetch_assoc();
$stmt->close();

if (!$member) {
    die("Member not found.");
}

function save_uploaded_file($file) {
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null; // or handle error as needed
    }
    
    // Define the upload directory
    $uploadDir = __DIR__ . '/uploads/'; // Make sure this folder exists and is writable
    
    // Create unique file name to avoid overwriting
    $fileName = uniqid() . '-' . basename($file['name']);
    
    // Full path for the uploaded file
    $uploadFilePath = $uploadDir . $fileName;
    
    // Move the uploaded file from temp location to uploads folder
    if (move_uploaded_file($file['tmp_name'], $uploadFilePath)) {
        return $fileName; // return the file name for saving to DB
    } else {
        return null; // file move failed
    }
}
if (isset($_FILES['marriage_certificate']) && $_FILES['marriage_certificate']['error'] === UPLOAD_ERR_OK) {
    $filename = save_uploaded_file($_FILES['marriage_certificate']);
    if ($filename) {
        // Update $member array and DB
        $member['marriage_certificate'] = $filename;
        // Run UPDATE query to save $filename in the database under marriage_certificate column for this member
    }
}

// Example for baptism certificate
if (isset($_FILES['baptism_certificate']) && $_FILES['baptism_certificate']['error'] == UPLOAD_ERR_OK) {
    // process baptism certificate upload
    $baptismFileName = save_uploaded_file($_FILES['baptism_certificate']);
}

// Similarly for marriage certificate
if (isset($_FILES['marriage_certificate']) && $_FILES['marriage_certificate']['error'] == UPLOAD_ERR_OK) {
    $marriageFileName = save_uploaded_file($_FILES['marriage_certificate']);
}

// Repeat for education_certificates, training_certificates, id_document, other_documents

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process form data
    $uploadDir = 'uploads/';
    
    // Handle file uploads
    $image = handleFileUpload($_FILES['image'] ?? [], $uploadDir, $member['image'] ?? null);
    $baptism_certificate = handleFileUpload($_FILES['baptism_certificate'] ?? [], $uploadDir, $member['baptism_certificate'] ?? null);
    // Add other file uploads similarly...
    
    // Prepare UPDATE statement
    $sql = "UPDATE members SET 
        first_name = ?,
        middle_name = ?,
        last_name = ?,
        dob = ?,
        gender = ?,
        marital_status = ?,
        spouse_name = ?,
        no_family_members = ?,
        telephone = ?,
        email = ?,
        address = ?,
        education = ?,
        church_contribution = ?,
        performance = ?,
        awards = ?,
        trainings = ?,
        service_area = ?,
        experience_years = ?,
        image = ?,
        baptism_certificate = ?,
        marriage_certificate = ?, 
        education_certificates = ?, 
        training_certificates = ?,
        id_document = ?, 
        other_documents = ?, 
        image_doc = ? 
        WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    
    // Bind parameters
    if (!$stmt->bind_param("sssssssissssssssssssssssssi",
        $_POST['first_name'],
        $_POST['middle_name'],
        $_POST['last_name'],
        $_POST['dob'],
        $_POST['sex'],
        $_POST['marital_status'],
        $_POST['spouse_name'],
        $_POST['no_family_members'],
        $_POST['telephone'],
        $_POST['email'],
        $_POST['address'],
        $_POST['education'],
        $_POST['church_contribution'],
        $_POST['performance'],
        $_POST['awards'],
        $_POST['trainings'],
        $_POST['service_area'],
        $_POST['experience_years'],
        $image,
        $baptism_certificate,
        $marriage_certificate,
        $education_certificates,
        $training_certificates,
        $id_document,  // fixed trailing space
        $other_documents,
        $image_doc,
        $memberId)) {
        die("Bind failed: " . $stmt->error);
    }
    
    // Execute update
    if ($stmt->execute()) {
        // Refresh member data
        $stmt = $conn->prepare("SELECT * FROM members WHERE id = ?");
        $stmt->bind_param("i", $memberId);
        $stmt->execute();
        $result = $stmt->get_result();
        $member = $result->fetch_assoc();
        $success = "Member updated successfully!";
    } else {
        $error = "Error updating member: " . $stmt->error;
    }
    $stmt->close();
}

// Calculate age
$dob = $member['dob'] ?? null;
$age = $dob ? date_diff(date_create($dob), date_create('today'))->y : '-';
$fullName = trim(($member['first_name'] ?? '') . ' ' . ($member['middle_name'] ?? '') . ' ' . ($member['last_name'] ?? ''));
?>


kkkk
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Member / ኣባል ኣርእስቲ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --font-xs: 0.75rem;
            --font-sm: 0.85rem;
            --font-md: 0.9rem;
            --text-indent: 1.2rem;
            --input-padding: 0.4rem 0.6rem;
        }
        
        body {
            font-family: 'Segoe UI', 'Noto Sans Ethiopic', sans-serif;
            background-color: #f5f5f5;
            color: #333;
            font-size: var(--font-md);
            line-height: 1.3;
            padding: 10px;
        }
        
        .edit-card {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        
        .edit-header, 
        .edit-body,
        .section-title,
        .form-group,
        .action-buttons {
            padding-left: var(--text-indent);
            padding-right: var(--text-indent);
        }
        
        .edit-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding-top: 0.8rem;
            padding-bottom: 0.8rem;
            text-align: center;
        }
        
        .edit-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin: 0;
        }
        
        .edit-body {
            padding-top: 0.8rem;
            padding-bottom: 0.8rem;
        }
        
        .profile-image {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            border: 2px solid white;
            object-fit: cover;
            margin: 0.3rem auto;
            display: block;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
        }
        
        .section-title {
            color: var(--primary-color);
            font-size: var(--font-md);
            font-weight: 600;
            margin: 0.6rem 0 0.4rem;
            padding-top: 0.3rem;
            padding-bottom: 0.3rem;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
        }
        
        .section-title i {
            margin-right: 0.4rem;
            color: var(--secondary-color);
            font-size: var(--font-md);
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 0.8rem;
            margin-bottom: 0.8rem;
        }
        
        .form-group {
            margin-bottom: 0.6rem;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.2rem;
            display: block;
            font-size: var(--font-sm);
        }
        
        .form-control, .form-select {
            padding: var(--input-padding);
            font-size: var(--font-sm);
            border-radius: 3px;
            border: 1px solid #ced4da;
            width: 100%;
        }
        
        .file-upload {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }
        
        .file-upload-input {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        
        .file-upload-label {
            display: block;
            padding: var(--input-padding);
            background-color: var(--light-color);
            border-radius: 3px;
            font-size: var(--font-sm);
            text-align: center;
            cursor: pointer;
        }
        
        .file-info {
            font-size: var(--font-xs);
            color: #6c757d;
            margin-top: 0.2rem;
        }
        
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1.2rem;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 0.4rem 0.8rem;
            border-radius: 3px;
            font-size: var(--font-sm);
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }
        
        @media (max-width: 768px) {
            :root {
                --text-indent: 0.8rem;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<div class="edit-card">
    <div class="edit-header">
        <h1 class="edit-title">
            <span class="dual-language">
                <span class="english">Edit Member</span>
                <span class="tigrinya">/ ኣባል ኣርእስቲ</span>
            </span>
        </h1>
    </div>

   <body>
<div class="edit-card">

    <div class="edit-body">
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php elseif (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
    </div>
</div>

<script>
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        const file = input.files[0];
        const reader = new FileReader();
        
        reader.onload = function(e) {
            if (preview.tagName === 'IMG') {
                preview.src = e.target.result;
            } else {
                // Replace the icon with an image
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'profile-image';
                preview.parentNode.replaceChild(img, preview);
                img.id = previewId;
            }
        }
        
        if (file) {
            reader.readAsDataURL(file);
        }
    }
</script>
   
    <div class="edit-body">
        <form method="POST" enctype="multipart/form-data">
            <!-- Profile Image -->
            <div class="text-center mb-3">
                <?php if (!empty($member['image'])): ?>
                    <img src="uploads/<?= htmlspecialchars($member['image']) ?>" class="profile-image" id="profile-preview">
                <?php else: ?>
                    <div class="profile-image bg-light d-flex align-items-center justify-content-center">
                        <i class="fas fa-user fa-3x text-muted" id="profile-preview"></i>
                    </div>
                <?php endif; ?>
                <div class="file-upload mt-2">
                    <label class="file-upload-label">
                        <i class="fas fa-camera me-1"></i>
                        <span class="english">Change Photo</span>
                        <span class="tigrinya">/ ስእሊ ቀይር</span>
                        <input type="file" class="file-upload-input" name="image" accept="image/*" onchange="previewImage(this, 'profile-preview')">
                    </label>
                </div>
            </div>
            
            <!-- Personal Information -->
            <div class="section-title">
                <i class="fas fa-user-circle"></i>
                <span class="dual-language">
                    <span class="english">Personal Information</span>
                    <span class="tigrinya">/ ግላዊ ሓበሬታ</span>
                </span>
            </div>
             <input type="hidden" name="id" value="<?= $memberId ?>">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-user me-1"></i>
                        <span class="english">First Name</span>
                        <span class="tigrinya">/ ናይ መጀመርታ ሽም</span>
                    </label>
                    <input type="text" class="form-control" name="first_name" value="<?= htmlspecialchars($member['first_name']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-user me-1"></i>
                        <span class="english">Middle Name</span>
                        <span class="tigrinya">/ ማእከላይ ሽም</span>
                    </label>
                    <input type="text" class="form-control" name="middle_name" value="<?= htmlspecialchars($member['middle_name']) ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-user me-1"></i>
                        <span class="english">Last Name</span>
                        <span class="tigrinya">/ ናይ መወዳእታ ሽም</span>
                    </label>
                    <input type="text" class="form-control" name="last_name" value="<?= htmlspecialchars($member['last_name']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-birthday-cake me-1"></i>
                        <span class="english">Date of Birth</span>
                        <span class="tigrinya">/ ዕለት ልደት</span>
                    </label>
                    <input type="date" class="form-control" name="dob" value="<?= htmlspecialchars($member['dob']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-venus-mars me-1"></i>
                        <span class="english">Gender</span>
                        <span class="tigrinya">/ ፆታ</span>
                    </label>
                    <select class="form-select" name="sex" required>
                        <option value="Male" <?= $member['sex'] === 'Male' ? 'selected' : '' ?>>Male</option>
                        <option value="Female" <?= $member['sex'] === 'Female' ? 'selected' : '' ?>>Female</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-heart me-1"></i>
                        <span class="english">Marital Status</span>
                        <span class="tigrinya">/ ዓዲ ዝነብር ሁነታ</span>
                    </label>
                    <select class="form-select" name="marital_status" required>
                        <option value="Single" <?= $member['marital_status'] === 'Single' ? 'selected' : '' ?>>Single</option>
                        <option value="Married" <?= $member['marital_status'] === 'Married' ? 'selected' : '' ?>>Married</option>
                        <option value="Divorced" <?= $member['marital_status'] === 'Divorced' ? 'selected' : '' ?>>Divorced</option>
                        <option value="Widowed" <?= $member['marital_status'] === 'Widowed' ? 'selected' : '' ?>>Widowed</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-user-friends me-1"></i>
                        <span class="english">Spouse Name</span>
                        <span class="tigrinya">/ ስም ባዕል</span>
                    </label>
                    <input type="text" class="form-control" name="spouse_name" value="<?= htmlspecialchars($member['spouse_name']) ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-users me-1"></i>
                        <span class="english">Family Members</span>
                        <span class="tigrinya">/ ኣባላት ስድራቤት</span>
                    </label>
                    <input type="number" class="form-control" name="no_family_members" value="<?= htmlspecialchars($member['no_family_members']) ?>" min="1">
                </div>
            </div>
            
            <!-- Contact Information -->
            <div class="section-title">
                <i class="fas fa-phone-alt"></i>
                <span class="dual-language">
                    <span class="english">Contact Information</span>
                    <span class="tigrinya">/ ምልክታ ርክብ</span>
                </span>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-mobile-alt me-1"></i>
                        <span class="english">Phone Number</span>
                        <span class="tigrinya">/ ቁጽሪ ስልኪ</span>
                    </label>
                    <input type="tel" class="form-control" name="telephone" value="<?= htmlspecialchars($member['telephone']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-envelope me-1"></i>
                        <span class="english">Email Address</span>
                        <span class="tigrinya">/ ኢመይል</span>
                    </label>
                    <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($member['email']) ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-home me-1"></i>
                        <span class="english">Address</span>
                        <span class="tigrinya">/ ኣድራሻ</span>
                    </label>
                    <textarea class="form-control" name="address" rows="2"><?= htmlspecialchars($member['address']) ?></textarea>
                </div>
            </div>
            
            <!-- Church Information -->
            <div class="section-title">
                <i class="fas fa-church"></i>
                <span class="dual-language">
                    <span class="english">Church Information</span>
                    <span class="tigrinya">/ ናይ ቤተክርስቲያን ሓበሬታ</span>
                </span>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-graduation-cap me-1"></i>
                        <span class="english">Education Level</span>
                        <span class="tigrinya">/ ደረጃ ትምህርቲ</span>
                    </label>
                    <select class="form-select" name="education">
                        <option value="Primary" <?= $member['education'] === 'Primary' ? 'selected' : '' ?>>Primary</option>
                        <option value="Secondary" <?= $member['education'] === 'Secondary' ? 'selected' : '' ?>>Secondary</option>
                        <option value="Diploma" <?= $member['education'] === 'Diploma' ? 'selected' : '' ?>>Diploma</option>
                        <option value="Bachelor" <?= $member['education'] === 'Bachelor' ? 'selected' : '' ?>>Bachelor</option>
                        <option value="Master" <?= $member['education'] === 'Master' ? 'selected' : '' ?>>Master</option>
                        <option value="PhD" <?= $member['education'] === 'PhD' ? 'selected' : '' ?>>PhD</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-hand-holding-heart me-1"></i>
                        <span class="english">Church Contribution</span>
                        <span class="tigrinya">/ ኣበርክቶ ቤተክርስቲያን</span>
                    </label>
                    <textarea class="form-control" name="church_contribution" rows="2"><?= htmlspecialchars($member['church_contribution']) ?></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-star me-1"></i>
                        <span class="english">Performance</span>
                        <span class="tigrinya">/ ኣፅድቓ</span>
                    </label>
                    <textarea class="form-control" name="performance" rows="2"><?= htmlspecialchars($member['performance']) ?></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-trophy me-1"></i>
                        <span class="english">Awards</span>
                        <span class="tigrinya">/ ሽልማት</span>
                    </label>
                    <textarea class="form-control" name="awards" rows="2"><?= htmlspecialchars($member['awards']) ?></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-certificate me-1"></i>
                        <span class="english">Trainings</span>
                        <span class="tigrinya">/ ስልጠናታት</span>
                    </label>
                    <textarea class="form-control" name="trainings" rows="2"><?= htmlspecialchars($member['trainings']) ?></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-hands-helping me-1"></i>
                        <span class="english">Service Area</span>
                        <span class="tigrinya">/ ዞባ ኣገልግሎት</span>
                    </label>
                    <input type="text" class="form-control" name="service_area" value="<?= htmlspecialchars($member['service_area']) ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-clock me-1"></i>
                        <span class="english">Experience (Years)</span>
                        <span class="tigrinya">/ ልምዲ (ዓመታት)</span>
                    </label>
                    <input type="number" class="form-control" name="experience_years" value="<?= htmlspecialchars($member['experience_years']) ?>" min="0">
                </div>
            </div>
            
            <!-- Documents Section -->
            <div class="section-title">
                <i class="fas fa-file-alt"></i>
                <span class="dual-language">
                    <span class="english">Documents</span>
                    <span class="tigrinya">/ ሰነዳት</span>
                </span>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-file-certificate me-1"></i>
                        <span class="english">Baptism Certificate</span>
                        <span class="tigrinya">/ ምስክር ጥምቀት</span>
                    </label>
                    <div class="file-upload">
                        <label class="file-upload-label">
                            <i class="fas fa-upload me-1"></i>
                            <span class="english">Upload File</span>
                            <span class="tigrinya">/ ፋይል ስደድ</span>
                            <input type="file" class="file-upload-input" name="baptism_certificate" accept=".pdf,.jpg,.jpeg,.png">
                        </label>
                    </div>
                    <?php if (!empty($member['baptism_certificate'])): ?>
                        <div class="file-info">
                            <i class="fas fa-check-circle text-success me-1"></i>
                            <span><?= htmlspecialchars($member['baptism_certificate']) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-file-contract me-1"></i>
                        <span class="english">Marriage Certificate</span>
                        <span class="tigrinya">/ ምስክር ሓዳር</span>
                    </label>
                    <div class="file-upload">
                        <label class="file-upload-label">
                            <i class="fas fa-upload me-1"></i>
                            <span class="english">Upload File</span>
                            <span class="tigrinya">/ ፋይል ስደድ</span>
                            <input type="file" class="file-upload-input" name="marriage_certificate" accept=".pdf,.jpg,.jpeg,.png">
                        </label>
                    </div>
                    <?php if (!empty($member['marriage_certificate'])): ?>
                        <div class="file-info">
                            <i class="fas fa-check-circle text-success me-1"></i>
                            <span><?= htmlspecialchars($member['marriage_certificate']) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-file-certificate me-1"></i>
                        <span class="english">Education Certificates</span>
                        <span class="tigrinya">/ ምስክር ትምህርቲ</span>
                    </label>
                    <div class="file-upload">
                        <label class="file-upload-label">
                            <i class="fas fa-upload me-1"></i>
                            <span class="english">Upload File</span>
                            <span class="tigrinya">/ ፋይል ስደድ</span>
                            <input type="file" class="file-upload-input" name="education_certificates" accept=".pdf,.jpg,.jpeg,.png">
                        </label>
                    </div>
                    <?php if (!empty($member['education_certificates'])): ?>
                        <div class="file-info">
                            <i class="fas fa-check-circle text-success me-1"></i>
                            <span><?= htmlspecialchars($member['education_certificates']) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-file-certificate me-1"></i>
                        <span class="english">Training Certificates</span>
                        <span class="tigrinya">/ ምስክር ስልጠና</span>
                    </label>
                    <div class="file-upload">
                        <label class="file-upload-label">
                            <i class="fas fa-upload me-1"></i>
                            <span class="english">Upload File</span>
                            <span class="tigrinya">/ ፋይል ስደድ</span>
                            <input type="file" class="file-upload-input" name="training_certificates" accept=".pdf,.jpg,.jpeg,.png">
                        </label>
                    </div>
                    <?php if (!empty($member['training_certificates'])): ?>
                        <div class="file-info">
                            <i class="fas fa-check-circle text-success me-1"></i>
                            <span><?= htmlspecialchars($member['training_certificates']) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-id-card me-1"></i>
                        <span class="english">ID Document</span>
                        <span class="tigrinya">/ ምስክር ምንዳይ</span>
                    </label>
                    <div class="file-upload">
                        <label class="file-upload-label">
                            <i class="fas fa-upload me-1"></i>
                            <span class="english">Upload File</span>
                            <span class="tigrinya">/ ፋይል ስደድ</span>
                            <input type="file" class="file-upload-input" name="id_document" accept=".pdf,.jpg,.jpeg,.png">
                        </label>
                    </div>
                    <?php if (!empty($member['id_document'])): ?>
                        <div class="file-info">
                            <i class="fas fa-check-circle text-success me-1"></i>
                            <span><?= htmlspecialchars($member['id_document']) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-file me-1"></i>
                        <span class="english">Other Documents</span>
                        <span class="tigrinya">/ ካልእ ሰነዳት</span>
                    </label>
                    <div class="file-upload">
                        <label class="file-upload-label">
                            <i class="fas fa-upload me-1"></i>
                            <span class="english">Upload File</span>
                            <span class="tigrinya">/ ፋይል ስደድ</span>
                            <input type="file" class="file-upload-input" name="other_documents" accept=".pdf,.jpg,.jpeg,.png">
                        </label>
                    </div>
                    <?php if (!empty($member['other_documents'])): ?>
                        <div class="file-info">
                            <i class="fas fa-check-circle text-success me-1"></i>
                            <span><?= htmlspecialchars($member['other_documents']) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="action-buttons">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    <span class="english">Save Changes</span>
                    <span class="tigrinya">/ ለውጢ ዓቅብ</span>
                </button>
                <a href="members.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    <span class="dual-language">
                        <span class="english">Back</span>
                        <span class="tigrinya">/ ተመለስ</span>
                    </span>
                </a>   
                <a href="view_member.php?id=<?= $memberId ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    <span class="english">Cancel</span>
                    <span class="tigrinya">/ ዕፀው</span>
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        const file = input.files[0];
        const reader = new FileReader();
        
        reader.onload = function(e) {
            if (preview.tagName === 'IMG') {
                preview.src = e.target.result;
            } else {
                // Replace the icon with an image
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'profile-image';
                preview.parentNode.replaceChild(img, preview);
                img.id = previewId;
            }
        }
        
        if (file) {
            reader.readAsDataURL(file);
        }
    }
</script>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<script>
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        const file = input.files[0];
        const reader = new FileReader();
        
        reader.onload = function(e) {
            if (preview.tagName === 'IMG') {
                preview.src = e.target.result;
            } else {
                // Replace the icon with an image
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'profile-image';
                preview.parentNode.replaceChild(img, preview);
                img.id = previewId;
            }
        }
        
        if (file) {
            reader.readAsDataURL(file);
        }
    }
</script>



<div class="form-group">
    <label>Current Image</label>
    <?php showFilePreview($uploadDir, $member['image']); ?>
    <label for="image">Upload New Image</label>
    <input type="file" id="image" name="image" accept="image/*" />
</div>

<div class="form-group">
    <label>Current Baptism Certificate</label>
    <?php showFilePreview($uploadDir, $member['baptism_certificate']); ?>
    <label for="baptism_certificate">Upload Baptism Certificate</label>
    <input type="file" id="baptism_certificate" name="baptism_certificate" accept=".pdf,image/*" />
</div>

<div class="form-group">
    <label>Current Marriage Certificate</label>
    <?php showFilePreview($uploadDir, $member['marriage_certificate']); ?>
    <label for="marriage_certificate">Upload Marriage Certificate</label>
    <input type="file" id="marriage_certificate" name="marriage_certificate" accept=".pdf,image/*" />
</div>

<div class="form-group">
    <label>Current Education Certificates</label>
    <?php showFilePreview($uploadDir, $member['education_certificates']); ?>
    <label for="education_certificates">Upload Education Certificates</label>
    <input type="file" id="education_certificates" name="education_certificates" accept=".pdf,image/*" />
</div>

<div class="form-group">
    <label>Current Training Certificates</label>
    <?php showFilePreview($uploadDir, $member['training_certificates']); ?>
    <label for="training_certificates">Upload Training Certificates</label>
    <input type="file" id="training_certificates" name="training_certificates" accept=".pdf,image/*" />
</div>

<div class="form-group">
    <label>Current ID Document</label>
    <?php showFilePreview($uploadDir, $member['id_document']); ?>
    <label for="id_document">Upload ID Document</label>
    <input type="file" id="id_document" name="id_document" accept=".pdf,image/*" />
</div>

<div class="form-group">
    <label>Current Other Documents</label>
    <?php showFilePreview($uploadDir, $member['other_documents']); ?>
    <label for="other_documents">Upload Other Documents</label>
    <input type="file" id="other_documents" name="other_documents" accept=".pdf,image/*" />
</div>

<div class="form-group">
    <label>Current Image Document</label>
    <?php showFilePreview($uploadDir, $member['image_doc']); ?>
    <label for="image_doc">Upload Image Document</label>
    <input type="file" id="image_doc" name="image_doc" accept="image/*" />
</div>

<button type="submit">Update Member</button>

kkkkk
// Prepare UPDATE statement (fixed commas)
$sql = "UPDATE members SET 
    first_name = ?,
    middle_name = ?,
    last_name = ?,
    dob = ?,
    gender = ?,
    marital_status = ?,
    spouse_name = ?,
    no_family_members = ?,
    telephone = ?,
    email = ?,
    address = ?,
    education = ?,
    church_contribution = ?,
    performance = ?,
    awards = ?,
    trainings = ?,
    service_area = ?,
    experience_years = ?,
    image = ?,
    baptism_certificate = ?,
    marriage_certificate = ?, 
    education_certificates = ?, 
    training_certificates = ?, 
    id_document = ?, 
    other_documents = ?, 
    image_doc = ? 
    WHERE id = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

// Bind parameters (use proper variable names from file uploads where applicable)
if (!$stmt->bind_param("sssssssissssssssssssssssi",
    $_POST['first_name'],
    $_POST['middle_name'],
    $_POST['last_name'],
    $_POST['dob'],
    $_POST['sex'],
    $_POST['marital_status'],
    $_POST['spouse_name'],
    $_POST['no_family_members'],
    $_POST['telephone'],
    $_POST['email'],
    $_POST['address'],
    $_POST['education'],
    $_POST['church_contribution'],
    $_POST['performance'],
    $_POST['awards'],
    $_POST['trainings'],
    $_POST['service_area'],
    $_POST['experience_years'],
    $image,                  // from file upload function, NOT $_POST
    $baptism_certificate,    // from file upload function
    $_POST['marriage_certificate'], // Assuming this is a form input, else use upload variable
    $_POST['education_certificates'], // ensure correct naming
    $_POST['training_certificates'],
    $_POST['id_document'],   // fixed trailing space
    $_POST['other_documents'],
    $_POST['image_doc'],
    $memberId)) {
    die("Bind failed: " . $stmt->error);
}
kkkkkkkkkkk

