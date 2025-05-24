
<?php
session_start();
require_once 'db_connect.php';

// Initialize variables
$success = $error = '';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_member'])) {
    try {
        // Validate required fields
        $required = ['first_name', 'last_name', 'dob', 'sex', 'marital_status', 'telephone'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("Please fill in all required fields");
            }
        }

        // Handle file uploads
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Process profile image
        $imageName = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $imageTmp = $_FILES['image']['tmp_name'];
            $imageName = uniqid() . '_' . basename($_FILES['image']['name']);
            $imagePath = $uploadDir . $imageName;
            
            // Validate image
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $fileType = mime_content_type($imageTmp);
            if (!in_array($fileType, $allowedTypes)) {
                throw new Exception("Only JPG, PNG, and GIF images are allowed");
            }
            
            if (!move_uploaded_file($imageTmp, $imagePath)) {
                throw new Exception("Failed to upload profile image");
            }
        } else {
            throw new Exception("Profile image is required");
        }

        // Process other file uploads
        $fileFields = [
            'baptism_certificate', 'marriage_certificate', 
            'education_certificates', 'training_certificates',
            'id_document', 'other_documents'
        ];
        
        $fileNames = [];
        foreach ($fileFields as $field) {
            if (isset($_FILES[$field]) && $_FILES[$field]['error'] == UPLOAD_ERR_OK) {
                $fileTmp = $_FILES[$field]['tmp_name'];
                $fileName = uniqid() . '_' . basename($_FILES[$field]['name']);
                $filePath = $uploadDir . $fileName;
                
                if (move_uploaded_file($fileTmp, $filePath)) {
                    $fileNames[$field] = $fileName;
                }
            }
        }

        // Prepare data for database insertion
        $data = [
            'first_name' => $_POST['first_name'],
            'middle_name' => $_POST['middle_name'] ?? '',
            'last_name' => $_POST['last_name'],
            'dob' => $_POST['dob'],
            'sex' => $_POST['sex'],
            'marital_status' => $_POST['marital_status'],
            'spouse_name' => $_POST['spouse_name'] ?? '',
            'no_family_members' => $_POST['no_family_members'] ?? 1,
            'telephone' => $_POST['telephone'],
            'email' => $_POST['email'] ?? '',
            'address' => $_POST['address'] ?? '',
            'education' => $_POST['education'] ?? '',
            'church_contribution' => $_POST['church_contribution'] ?? '',
            'performance' => $_POST['performance'] ?? '',
            'awards' => $_POST['awards'] ?? '',
            'trainings' => $_POST['trainings'] ?? '',
            'service_area' => $_POST['service_area'] ?? '',
            'experience_years' => $_POST['experience_years'] ?? 0,
            'image' => $imageName,
            'baptism_certificate' => $fileNames['baptism_certificate'] ?? '',
            'marriage_certificate' => $fileNames['marriage_certificate'] ?? '',
            'education_certificates' => $fileNames['education_certificates'] ?? '',
            'training_certificates' => $fileNames['training_certificates'] ?? '',
            'id_document' => $fileNames['id_document'] ?? '',
            'other_documents' => $fileNames['other_documents'] ?? ''
        ];

        // Insert into database
        $columns = implode(', ', array_keys($data));
        $values = ':' . implode(', :', array_keys($data));
        
        $stmt = $pdo->prepare("INSERT INTO members ($columns) VALUES ($values)");
        $stmt->execute($data);

        // Set success message
        $_SESSION['success'] = "Member added successfully!";
        header("Location: members.php");
        exit();
        
    } catch (Exception $e) {
        $error = $e->getMessage();
        $_SESSION['error'] = $error;
        header("Location: add-member.php");
        exit();
    }
} else {
    header("Location: add-member.php");
    exit();
}