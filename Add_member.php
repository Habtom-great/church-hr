<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connect to your database
$conn = new mysqli("localhost", "root", "", "church_hr");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert
$stmt = $conn->prepare("INSERT INTO members (first_name, middle_name, last_name, dob, gender, marital_status, spouse_name, telephone, email, address, education, church_contribution, performance, awards, trainings, service_area) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("ssssssssssssssss", $first_name, $middle_name, $last_name, $dob, $gender, $marital_status, $spouse_name, $phone, $email, $address, $education, $contribution, $performance, $awards, $trainings, $service_area);
if ($stmt->execute()) {
    $success = "Member added successfully.";
} else {
    $error = "Error: " . $stmt->error;
}
// Initialize variables
$success = $error = '';
$member = null;
$isEdit = false;

// Check if editing
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $member_id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM members WHERE id = ?");
    $stmt->bind_param("i", $member_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $member = $result->fetch_assoc();
    $isEdit = true;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_member'])) {
    $first_name = $_POST['first_name'] ?? '';
    $middle_name = $_POST['middle_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $marital_status = $_POST['marital_status'] ?? '';
    $spouse_name = $_POST['spouse_name'] ?? '';
    $telephone = $_POST['telephone'] ?? '';
    $email = $_POST['email'] ?? '';
    $address = $_POST['address'] ?? '';
    $education = $_POST['education'] ?? '';
    $church_contribution = $_POST['church_contribution'] ?? '';
    $performance = $_POST['performance'] ?? '';
    $awards = $_POST['awards'] ?? '';
    $trainings = $_POST['trainings'] ?? '';
    $service_area = $_POST['service_area'] ?? '';

    if ($isEdit) {
        // Update
        $stmt = $conn->prepare("UPDATE members SET first_name=?, middle_name=?, last_name=?, dob=?, gender=?, marital_status=?, spouse_name=?, phone=?, email=?, address=?, education=?, contribution=?, performance=?, awards=?, trainings=?, service_area=? WHERE id=?");
        $stmt->bind_param("ssssssssssssssssi", $first_name, $middle_name, $last_name, $dob, $gender, $marital_status, $spouse_name, $phone, $email, $address, $education, $contribution, $performance, $awards, $trainings, $service_area, $member_id);
        if ($stmt->execute()) {
            $success = "Member updated successfully.";
        } else {
            $error = "Error: " . $stmt->error;
        }
    } else {
        // Insert
        $stmt = $conn->prepare("INSERT INTO members (first_name, middle_name, last_name, dob, gender, marital_status, spouse_name, telephone, email, address, education, church_contribution, performance, awards, trainings, service_area) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssssssssss", $first_name, $middle_name, $last_name, $dob, $gender, $marital_status, $spouse_name, $telephone, $email, $address, $education, $church_contribution, $performance, $awards, $trainings, $service_area);
        if ($stmt->execute()) {
            $success = "Member added successfully.";
        } else {
            $error = "Error: " . $stmt->error;
        }
    }
    $stmt->close();
}

$conn->close();
?>

  <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Member / ሓድሽ ኣባል ወስኽ</title>
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
                <span class="english">Add New Member</span>
                <span class="tigrinya">/ ሓድሽ ኣባል ወስኽ</span>
            </span>
        </h1>
    </div>

    <div class="edit-body">
        <?php if (isset($success)): ?>
            <div class="alert alert-success text-center"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger text-center"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <!-- Profile Image -->
            <div class="text-center mb-3">
                <div class="profile-image bg-light d-flex align-items-center justify-content-center">
                    <i class="fas fa-user fa-3x text-muted" id="profile-preview"></i>
                </div>
                <div class="file-upload mt-2">
                    <label class="file-upload-label">
                        <i class="fas fa-camera me-1"></i>
                        <span class="english">Upload Photo</span>
                        <span class="tigrinya">/ ስእሊ ስደድ</span>
                        <input type="file" class="file-upload-input" name="image" accept="image/*" onchange="previewImage(this, 'profile-preview')" required>
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
            
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-user me-1"></i>
                        <span class="english">First Name</span>
                        <span class="tigrinya">/ ናይ መጀመርታ ሽም</span>
                    </label>
                    <input type="text" class="form-control" name="first_name" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-user me-1"></i>
                        <span class="english">Middle Name</span>
                        <span class="tigrinya">/ ማእከላይ ሽም</span>
                    </label>
                    <input type="text" class="form-control" name="middle_name">
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-user me-1"></i>
                        <span class="english">Last Name</span>
                        <span class="tigrinya">/ ናይ መወዳእታ ሽም</span>
                    </label>
                    <input type="text" class="form-control" name="last_name" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-birthday-cake me-1"></i>
                        <span class="english">Date of Birth</span>
                        <span class="tigrinya">/ ዕለት ልደት</span>
                    </label>
                    <input type="date" class="form-control" name="dob" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-venus-mars me-1"></i>
                        <span class="english">Gender</span>
                        <span class="tigrinya">/ ፆታ</span>
                    </label>
                    <select class="form-select" name="sex" required>
                        <option value="">-- Select --</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-heart me-1"></i>
                        <span class="english">Marital Status</span>
                        <span class="tigrinya">/ ዓዲ ዝነብር ሁነታ</span>
                    </label>
                    <select class="form-select" name="marital_status" required>
                        <option value="">-- Select --</option>
                        <option value="Single">Single</option>
                        <option value="Married">Married</option>
                        <option value="Divorced">Divorced</option>
                        <option value="Widowed">Widowed</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-user-friends me-1"></i>
                        <span class="english">Spouse Name</span>
                        <span class="tigrinya">/ ስም ባዕል</span>
                    </label>
                    <input type="text" class="form-control" name="spouse_name">
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-users me-1"></i>
                        <span class="english">Family Members</span>
                        <span class="tigrinya">/ ኣባላት ስድራቤት</span>
                    </label>
                    <input type="number" class="form-control" name="no_family_members" value="1" min="1">
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
                    <input type="tel" class="form-control" name="telephone" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-envelope me-1"></i>
                        <span class="english">Email Address</span>
                        <span class="tigrinya">/ ኢመይል</span>
                    </label>
                    <input type="email" class="form-control" name="email">
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-home me-1"></i>
                        <span class="english">Address</span>
                        <span class="tigrinya">/ ኣድራሻ</span>
                    </label>
                    <textarea class="form-control" name="address" rows="2"></textarea>
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
                        <option value="">-- Select --</option>
                        <option value="Primary">Primary</option>
                        <option value="Secondary">Secondary</option>
                        <option value="Diploma">Diploma</option>
                        <option value="Bachelor">Bachelor</option>
                        <option value="Master">Master</option>
                        <option value="PhD">PhD</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-hand-holding-heart me-1"></i>
                        <span class="english">Church Contribution</span>
                        <span class="tigrinya">/ ኣበርክቶ ቤተክርስቲያን</span>
                    </label>
                    <textarea class="form-control" name="church_contribution" rows="2"></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-star me-1"></i>
                        <span class="english">Performance</span>
                        <span class="tigrinya">/ ኣፅድቓ</span>
                    </label>
                    <textarea class="form-control" name="performance" rows="2"></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-trophy me-1"></i>
                        <span class="english">Awards</span>
                        <span class="tigrinya">/ ሽልማት</span>
                    </label>
                    <textarea class="form-control" name="awards" rows="2"></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-certificate me-1"></i>
                        <span class="english">Trainings</span>
                        <span class="tigrinya">/ ስልጠናታት</span>
                    </label>
                    <textarea class="form-control" name="trainings" rows="2"></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-hands-helping me-1"></i>
                        <span class="english">Service Area</span>
                        <span class="tigrinya">/ ዞባ ኣገልግሎት</span>
                    </label>
                    <input type="text" class="form-control" name="service_area">
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-clock me-1"></i>
                        <span class="english">Experience (Years)</span>
                        <span class="tigrinya">/ ልምዲ (ዓመታት)</span>
                    </label>
                    <input type="number" class="form-control" name="experience_years" value="0" min="0">
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
                    <div class="file-info">
                        <i class="fas fa-times-circle text-danger me-1"></i>
                        <span class="english">No file uploaded</span>
                        <span class="tigrinya">/ ፋይል ኣይተሰደደን</span>
                    </div>
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
                    <div class="file-info">
                        <i class="fas fa-times-circle text-danger me-1"></i>
                        <span class="english">No file uploaded</span>
                        <span class="tigrinya">/ ፋይል ኣይተሰደደን</span>
                    </div>
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
                    <div class="file-info">
                        <i class="fas fa-times-circle text-danger me-1"></i>
                        <span class="english">No file uploaded</span>
                        <span class="tigrinya">/ ፋይል ኣይተሰደደን</span>
                    </div>
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
                    <div class="file-info">
                        <i class="fas fa-times-circle text-danger me-1"></i>
                        <span class="english">No file uploaded</span>
                        <span class="tigrinya">/ ፋይል ኣይተሰደደን</span>
                    </div>
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
                    <div class="file-info">
                        <i class="fas fa-times-circle text-danger me-1"></i>
                        <span class="english">No file uploaded</span>
                        <span class="tigrinya">/ ፋይል ኣይተሰደደን</span>
                    </div>
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
                    <div class="file-info">
                        <i class="fas fa-times-circle text-danger me-1"></i>
                        <span class="english">No file uploaded</span>
                        <span class="tigrinya">/ ፋይል ኣይተሰደደን</span>
                    </div>
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
                <button type="submit" class="btn btn-primary" name="add_member">
                    <i class="fas fa-user-plus"></i>
                    <span class="english">Add Member</span>
                    <span class="tigrinya">/ ኣባል ወስኽ</span>
                </button>
                <a href="members.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    <span class="english">Back to List</span>
                    <span class="tigrinya">/ ናብ ዝርዝር ተመለስ</span>
                </a>
                <button type="reset" class="btn btn-warning">
                    <i class="fas fa-undo"></i>
                    <span class="english">Reset Form</span>
                    <span class="tigrinya">/ ቅፅሊ ኣቕንዩ</span>
                </button>
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
