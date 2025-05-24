<?php
// Enable full error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Database connection config
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "church_hr";

// Connect to DB
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function handleFileUpload($fileInput, $uploadDir, $currentFile = null) {
    if (
        isset($fileInput) &&
        is_array($fileInput) &&
        isset($fileInput['error']) &&
        $fileInput['error'] === UPLOAD_ERR_OK
    ) {
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = uniqid() . '_' . basename($fileInput['name']);
        $targetPath = rtrim($uploadDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $fileName;

        if (move_uploaded_file($fileInput['tmp_name'], $targetPath)) {
            if ($currentFile && file_exists($uploadDir . DIRECTORY_SEPARATOR . $currentFile)) {
                unlink($uploadDir . DIRECTORY_SEPARATOR . $currentFile);
            }
            return $fileName;
        }
    }

    return $currentFile;
}

// Usage example:
$uploadDir = __DIR__ . '/uploads/'; // Make sure this folder exists and is writable
$currentFile = 'previous_file.pdf'; // from your database or form

if (isset($_FILES['member_file'])) {
    $uploadedFileName = handleFileUpload($_FILES['member_file'], $uploadDir, $currentFile);
    // Now you can store $uploadedFileName in your DB or process further
} else {
    $uploadedFileName = $currentFile;
}


// Get member ID from GET
$memberId = $_GET['id'] ?? null;
if (!$memberId || !is_numeric($memberId)) {
    die("Invalid member ID.");
}

// Fetch current member data for form prefill
$stmt = $conn->prepare("SELECT * FROM members WHERE id = ?");
$stmt->bind_param("i", $memberId);
$stmt->execute();
$result = $stmt->get_result();
$member = $result->fetch_assoc();
$stmt->close();

if (!$member) {
    die("Member not found.");
}

$success = $error = null;
$experience_years = isset($_POST['experience_years']) && is_numeric($_POST['experience_years']) ? intval($_POST['experience_years']) : null;


// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle uploads, keeping old file if none uploaded
    $image = handleFileUpload($_FILES['image'] ?? [], $uploadDir, $member['image'] ?? null);
    $baptism_certificate = handleFileUpload($_FILES['baptism_certificate'] ?? [], $uploadDir, $member['baptism_certificate'] ?? null);
    $marriage_certificate = handleFileUpload($_FILES['marriage_certificate'] ?? [], $uploadDir, $member['marriage_certificate'] ?? null);
    $education_certificates = handleFileUpload($_FILES['education_certificates'] ?? [], $uploadDir, $member['education_certificates'] ?? null);
    $training_certificates = handleFileUpload($_FILES['training_certificates'] ?? [], $uploadDir, $member['training_certificates'] ?? null);
    $id_document = handleFileUpload($_FILES['id_document'] ?? [], $uploadDir, $member['id_document'] ?? null);
    $other_documents = handleFileUpload($_FILES['other_documents'] ?? [], $uploadDir, $member['other_documents'] ?? null);
    $image_doc = handleFileUpload($_FILES['image_doc'] ?? [], $uploadDir, $member['image_doc'] ?? null);

    // Prepare update statement
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

    /*
     * Bind parameters types:
     * s = string
     * i = integer
     * Fields:
     * first_name (s), middle_name (s), last_name (s), dob (s), gender (s), marital_status (s), spouse_name (s),
     * no_family_members (i), telephone (s), email (s), address (s), education (s), church_contribution (s), performance (s),
     * awards (s), trainings (s), service_area (s), experience_years (s),
     * image (s), baptism_certificate (s), marriage_certificate (s), education_certificates (s),
     * training_certificates (s), id_document (s), other_documents (s), image_doc (s),
     * id (i)
     */

      // Bind parameters
    if (!$stmt->bind_param("sssssssissssssssssssssssssi",
    $_POST['first_name'],
    $_POST['middle_name'],
    $_POST['last_name'],
    $_POST['dob'],
    $_POST['gender'],
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

 
    if ($stmt->execute()) {
        $success = "Member updated successfully.";

        // Refresh member data for showing updated values
        $stmt->close();
        $stmt = $conn->prepare("SELECT * FROM members WHERE id = ?");
        $stmt->bind_param("i", $memberId);
        $stmt->execute();
        $result = $stmt->get_result();
        $member = $result->fetch_assoc();
        $stmt->close();
    } else {
        $error = "Error updating member: " . $stmt->error;
    }
}

// Calculate age from dob
$dob = $member['dob'] ?? null;
$age = $dob ? date_diff(date_create($dob), date_create('today'))->y : '-';

?>


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
            width: 100%;
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
        
        .document-preview {
            max-width: 100%;
            max-height: 150px;
            margin-top: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .document-container {
            margin-bottom: 1rem;
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
           
                <h1 class="edit-title">Edit Member: <?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></h1>
               <span class="tigrinya">/ ኣባል ኣርእስቲ</span>
            </div>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success text-center"><?php echo $success; ?></div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger text-center"><?php echo $error; ?></div>
    <?php endif; ?>

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
            
            <input type="hidden" name="id" value="<?= $memberId ?>">
            
            <!-- Personal Information -->
            <div class="section-title">
                <i class="fas fa-user-circle"></i>
                <span class="dual-language">
                    <span class="english">Personal Information</span>
                    <span class="tigrinya">/ ግላዊ ሓበሬታ</span>
                </span>
            </div>
            
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
                        <span class="tigrinya">/ ናይ መወዳእታ �ም</span>
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
                <!-- Baptism Certificate -->
                <div class="form-group document-container">
                    <label class="form-label">
                        <i class="fas fa-file-certificate me-1"></i>
                        <span class="english">Baptism Certificate</span>
                        <span class="tigrinya">/ ምስክር ጥምቀት</span>
                    </label>
                    <?php if (!empty($member['baptism_certificate'])): ?>
                        <div class="file-info">
                            <i class="fas fa-check-circle text-success me-1"></i>
                            <span><?= htmlspecialchars($member['baptism_certificate']) ?></span>
                        </div>
                        <img src="uploads/<?= htmlspecialchars($member['baptism_certificate']) ?>" class="document-preview" id="baptism-preview">
                    <?php else: ?>
                        <div class="file-info">
                            <i class="fas fa-times-circle text-danger me-1"></i>
                            <span class="english">No file uploaded</span>
                            <span class="tigrinya">/ ፋይል ኣይተሰደደን</span>
                        </div>
                    <?php endif; ?>
                    <div class="file-upload mt-2">
                        <label class="file-upload-label">
                            <i class="fas fa-upload me-1"></i>
                            <span class="english">Upload File</span>
                            <span class="tigrinya">/ ፋይል ስደድ</span>
                            <input type="file" class="file-upload-input" name="baptism_certificate" accept=".pdf,.jpg,.jpeg,.png" onchange="previewDocument(this, 'baptism-preview')">
                        </label>
                    </div>
                </div>
                
                <!-- Marriage Certificate -->
                <div class="form-group document-container">
                    <label class="form-label">
                        <i class="fas fa-file-contract me-1"></i>
                        <span class="english">Marriage Certificate</span>
                        <span class="tigrinya">/ ምስክር ሓዳር</span>
                    </label>
                    <?php if (!empty($member['marriage_certificate'])): ?>
                        <div class="file-info">
                            <i class="fas fa-check-circle text-success me-1"></i>
                            <span><?= htmlspecialchars($member['marriage_certificate']) ?></span>
                        </div>
                        <img src="uploads/<?= htmlspecialchars($member['marriage_certificate']) ?>" class="document-preview" id="marriage-preview">
                    <?php else: ?>
                        <div class="file-info">
                            <i class="fas fa-times-circle text-danger me-1"></i>
                            <span class="english">No file uploaded</span>
                            <span class="tigrinya">/ ፋይል ኣይተሰደደን</span>
                        </div>
                    <?php endif; ?>
                    <div class="file-upload mt-2">
                        <label class="file-upload-label">
                            <i class="fas fa-upload me-1"></i>
                            <span class="english">Upload File</span>
                            <span class="tigrinya">/ ፋይል ስደድ</span>
                            <input type="file" class="file-upload-input" name="marriage_certificate" accept=".pdf,.jpg,.jpeg,.png" onchange="previewDocument(this, 'marriage-preview')">
                        </label>
                    </div>
                </div>
                
                <!-- Education Certificates -->
                <div class="form-group document-container">
                    <label class="form-label">
                        <i class="fas fa-file-certificate me-1"></i>
                        <span class="english">Education Certificates</span>
                        <span class="tigrinya">/ ምስክር ትምህርቲ</span>
                    </label>
                    <?php if (!empty($member['education_certificates'])): ?>
                        <div class="file-info">
                            <i class="fas fa-check-circle text-success me-1"></i>
                            <span><?= htmlspecialchars($member['education_certificates']) ?></span>
                        </div>
                        <img src="uploads/<?= htmlspecialchars($member['education_certificates']) ?>" class="document-preview" id="education-preview">
                    <?php else: ?>
                        <div class="file-info">
                            <i class="fas fa-times-circle text-danger me-1"></i>
                            <span class="english">No file uploaded</span>
                            <span class="tigrinya">/ ፋይል ኣይተሰደደን</span>
                        </div>
                    <?php endif; ?>
                    <div class="file-upload mt-2">
                        <label class="file-upload-label">
                            <i class="fas fa-upload me-1"></i>
                            <span class="english">Upload File</span>
                            <span class="tigrinya">/ ፋይል ስደድ</span>
                            <input type="file" class="file-upload-input" name="education_certificates" accept=".pdf,.jpg,.jpeg,.png" onchange="previewDocument(this, 'education-preview')">
                        </label>
                    </div>
                </div>
                
                <!-- Training Certificates -->
                <div class="form-group document-container">
                    <label class="form-label">
                        <i class="fas fa-file-certificate me-1"></i>
                        <span class="english">Training Certificates</span>
                        <span class="tigrinya">/ ምስክር ስልጠና</span>
                    </label>
                    <?php if (!empty($member['training_certificates'])): ?>
                        <div class="file-info">
                            <i class="fas fa-check-circle text-success me-1"></i>
                            <span><?= htmlspecialchars($member['training_certificates']) ?></span>
                        </div>
                        <img src="uploads/<?= htmlspecialchars($member['training_certificates']) ?>" class="document-preview" id="training-preview">
                    <?php else: ?>
                        <div class="file-info">
                            <i class="fas fa-times-circle text-danger me-1"></i>
                            <span class="english">No file uploaded</span>
                            <span class="tigrinya">/ ፋይል ኣይተሰደደን</span>
                        </div>
                    <?php endif; ?>
                    <div class="file-upload mt-2">
                        <label class="file-upload-label">
                            <i class="fas fa-upload me-1"></i>
                            <span class="english">Upload File</span>
                            <span class="tigrinya">/ ፋይል ስደድ</span>
                            <input type="file" class="file-upload-input" name="training_certificates" accept=".pdf,.jpg,.jpeg,.png" onchange="previewDocument(this, 'training-preview')">
                        </label>
                    </div>
                </div>
                
                <!-- ID Document -->
                <div class="form-group document-container">
                    <label class="form-label">
                        <i class="fas fa-id-card me-1"></i>
                        <span class="english">ID Document</span>
                        <span class="tigrinya">/ ምስክር ምንዳይ</span>
                    </label>
                    <?php if (!empty($member['id_document'])): ?>
                        <div class="file-info">
                            <i class="fas fa-check-circle text-success me-1"></i>
                            <span><?= htmlspecialchars($member['id_document']) ?></span>
                        </div>
                        <img src="uploads/<?= htmlspecialchars($member['id_document']) ?>" class="document-preview" id="id-preview">
                    <?php else: ?>
                        <div class="file-info">
                            <i class="fas fa-times-circle text-danger me-1"></i>
                            <span class="english">No file uploaded</span>
                            <span class="tigrinya">/ ፋይል ኣይተሰደደን</span>
                        </div>
                    <?php endif; ?>
                    <div class="file-upload mt-2">
                        <label class="file-upload-label">
                            <i class="fas fa-upload me-1"></i>
                            <span class="english">Upload File</span>
                            <span class="tigrinya">/ ፋይል ስደድ</span>
                            <input type="file" class="file-upload-input" name="id_document" accept=".pdf,.jpg,.jpeg,.png" onchange="previewDocument(this, 'id-preview')">
                        </label>
                    </div>
                </div>
                
                <!-- Other Documents -->
                <div class="form-group document-container">
                    <label class="form-label">
                        <i class="fas fa-file me-1"></i>
                        <span class="english">Other Documents</span>
                        <span class="tigrinya">/ ካልእ ሰነዳት</span>
                    </label>
                    <?php if (!empty($member['other_documents'])): ?>
                        <div class="file-info">
                            <i class="fas fa-check-circle text-success me-1"></i>
                            <span><?= htmlspecialchars($member['other_documents']) ?></span>
                        </div>
                        <img src="uploads/<?= htmlspecialchars($member['other_documents']) ?>" class="document-preview" id="other-preview">
                    <?php else: ?>
                        <div class="file-info">
                            <i class="fas fa-times-circle text-danger me-1"></i>
                            <span class="english">No file uploaded</span>
                            <span class="tigrinya">/ ፋይል ኣይተሰደደን</span>
                        </div>
                    <?php endif; ?>
                    <div class="file-upload mt-2">
                        <label class="file-upload-label">
                            <i class="fas fa-upload me-1"></i>
                            <span class="english">Upload File</span>
                            <span class="tigrinya">/ ፋይል ስደድ</span>
                            <input type="file" class="file-upload-input" name="other_documents" accept=".pdf,.jpg,.jpeg,.png" onchange="previewDocument(this, 'other-preview')">
                        </label>
                    </div>
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
                    <span class="english">Back to List</span>
                    <span class="tigrinya">/ ናብ ዝርዝር ተመለስ</span>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
    
    function previewDocument(input, previewId) {
        const previewContainer = input.closest('.document-container');
        const file = input.files[0];
        
        if (!file) return;
        
        // Check if file is PDF
        if (file.type === 'application/pdf') {
            // For PDFs, we'll just show the filename
            const previewElement = document.getElementById(previewId);
            if (previewElement) {
                previewElement.src = '';
                previewElement.style.display = 'none';
            }
            
            // Update the file info display
            const fileInfo = previewContainer.querySelector('.file-info');
            if (fileInfo) {
                fileInfo.innerHTML = `
                    <i class="fas fa-file-pdf text-danger me-1"></i>
                    <span>${file.name}</span>
                `;
            }
        } else {
            // For images, show preview
            const reader = new FileReader();
            
            reader.onload = function(e) {
                let previewElement = document.getElementById(previewId);
                
                if (!previewElement) {
                    previewElement = document.createElement('img');
                    previewElement.className = 'document-preview';
                    previewElement.id = previewId;
                    input.closest('.form-group').insertBefore(previewElement, input.closest('.file-upload'));
                }
                
                previewElement.src = e.target.result;
                previewElement.style.display = 'block';
                
                // Update the file info display
                const fileInfo = previewContainer.querySelector('.file-info');
                if (fileInfo) {
                    fileInfo.innerHTML = `
                        <i class="fas fa-check-circle text-success me-1"></i>
                        <span>${file.name}</span>
                    `;
                }
            }
            
            reader.readAsDataURL(file);
        }
    }
</script>
</body>
</html>

