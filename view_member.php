
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "church_hr";

// Database connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get member ID
$memberId = $_GET['id'] ?? null;
if (!$memberId || !is_numeric($memberId)) {
    die("Invalid member ID.");
}

// Fetch member details
$stmt = $conn->prepare("SELECT * FROM members WHERE id = ?");
$stmt->bind_param("i", $memberId);
$stmt->execute();
$result = $stmt->get_result();
$member = $result->fetch_assoc();
$stmt->close();

if (!$member) {
    die("Member not found.");
}

// Calculate age
$dob = $member['dob'];
$age = $dob ? date_diff(date_create($dob), date_create('today'))->y : '-';
$fullName = trim($member['first_name'] . ' ' . $member['middle_name'] . ' ' . $member['last_name']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Profile / መለኪዒ ኣባላት</title>
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
            --text-indent: 1.2rem; /* Equal left/right text indentation */
        }
        
        body {
            font-family: 'Segoe UI', 'Noto Sans Ethiopic', sans-serif;
            background-color: #f5f5f5;
            color: #333;
            font-size: var(--font-md);
            line-height: 1.3;
            padding: 10px;
        }
        
        .profile-card {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        
        /* Apply equal indentation to all text containers */
        .profile-header,
        .profile-body,
        .section-title,
        .info-item,
        .info-label,
        .info-value,
        .action-buttons,
        .signature-section,
        .alert {
            padding-left: var(--text-indent);
            padding-right: var(--text-indent);
        }
        
        /* Header styles */
        .profile-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding-top: 0.8rem;
            padding-bottom: 0.8rem;
            text-align: center;
        }
        
        .profile-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin: 0;
        }
        
        .profile-subtitle {
            font-size: var(--font-xs);
            opacity: 0.9;
            margin: 0.2rem 0 0;
        }
        
        /* Body styles */
        .profile-body {
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
        
        /* Grid layout */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 0.5rem;
            margin-bottom: 0.8rem;
        }
        
        .info-item {
            margin-bottom: 0.4rem;
        }
        
        .info-label {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.1rem;
            display: flex;
            align-items: center;
            font-size: var(--font-sm);
        }
        
        .info-label i {
            margin-right: 0.4rem;
            color: var(--secondary-color);
            width: 16px;
            text-align: center;
            font-size: var(--font-sm);
        }
        
        .info-value {
            padding: 0.3rem 0.5rem;
            background-color: var(--light-color);
            border-radius: 3px;
            font-size: var(--font-sm);
            min-height: 1.8rem;
        }
        
        /* Action buttons */
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 0.35rem 0.7rem;
            border-radius: 3px;
            font-size: var(--font-sm);
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }
        
        /* Print styles */
        .print-only {
            display: none;
        }
        
        @media print {
            .no-print {
                display: none !important;
            }
            
            .print-only {
                display: block;
            }
            
            body {
                padding: 0;
                font-size: 10px;
            }
            
            .profile-card {
                box-shadow: none;
                border: 1px solid #ddd;
                margin: 0;
            }
            
            :root {
                --text-indent: 0.8rem;
            }
        }
        
        @media (max-width: 768px) {
            :root {
                --text-indent: 0.8rem;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
 
    </style>
</head>
<body>
<div class="profile-card">
    <div class="profile-header">
        <h1 class="profile-title">
            <span class="dual-language">
                <span class="english">Member Profile</span>
                <span class="tigrinya">/ መለኪዒ ኣባላት</span>
            </span>
        </h1>
        <div class="profile-subtitle">
            <span class="dual-language">
                <span class="english">UGBC Member Information</span>
                <span class="tigrinya">/ ናይ UGBC ኣባል ሓበሬታ</span>
            </span>
        </div>
    </div>
    
    <div class="profile-body">
        <?php if ($member): ?>
            <div class="profile-image-container text-center no-print">
                <?php if (!empty($member['image'])): ?>
                    <img src="uploads/<?= htmlspecialchars($member['image']) ?>" class="profile-image" alt="Profile Image">
                <?php else: ?>
                    <div class="profile-image bg-light d-flex align-items-center justify-content-center">
                        <i class="fas fa-user fa-3x text-muted"></i>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="section-title">
                <i class="fas fa-user-circle"></i>
                <span class="dual-language">
                    <span class="english">Personal Information</span>
                    <span class="tigrinya">/ ግላዊ ሓበሬታ</span>
                </span>
            </div>
            
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-id-card"></i>
                        <span class="dual-language">
                            <span class="english">Member ID</span>
                            <span class="tigrinya">/ መለለዪ ኣባል</span>
                        </span>
                    </div>
                    <div class="info-value"><?= htmlspecialchars($member['id']) ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-user"></i>
                        <span class="dual-language">
                            <span class="english">Full Name</span>
                            <span class="tigrinya">/ ሙሉ ሽም</span>
                        </span>
                    </div>
                    <div class="info-value"><?= htmlspecialchars($fullName) ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-birthday-cake"></i>
                        <span class="dual-language">
                            <span class="english">Date of Birth</span>
                            <span class="tigrinya">/ ዕለት ልደት</span>
                        </span>
                    </div>
                    <div class="info-value">
                        <?= htmlspecialchars($member['dob']) ?> 
                        <span class="dual-language">
                            <span class="english">(Age: <?= $age ?>)</span>
                            <span class="tigrinya">/ (ዕድመ: <?= $age ?>)</span>
                        </span>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-venus-mars"></i>
                        <span class="dual-language">
                            <span class="english">Gender</span>
                            <span class="tigrinya">/ ፆታ</span>
                        </span>
                    </div>
                    <div class="info-value"><?= htmlspecialchars($member['sex']) ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-heart"></i>
                        <span class="dual-language">
                            <span class="english">Marital Status</span>
                            <span class="tigrinya">/ ዓዲ ዝነብር ሁነታ</span>
                        </span>
                    </div>
                    <div class="info-value"><?= htmlspecialchars($member['marital_status']) ?></div>
                </div>
                
                <?php if (!empty($member['spouse_name'])): ?>
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-user-friends"></i>
                        <span class="dual-language">
                            <span class="english">Spouse Name</span>
                            <span class="tigrinya">/ ስም ባዕል</span>
                        </span>
                    </div>
                    <div class="info-value"><?= htmlspecialchars($member['spouse_name']) ?></div>
                </div>
                <?php endif; ?>
                
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-users"></i>
                        <span class="dual-language">
                            <span class="english">Family Members</span>
                            <span class="tigrinya">/ ኣባላት ስድራቤት</span>
                        </span>
                    </div>
                    <div class="info-value"><?= htmlspecialchars($member['no_family_members']) ?></div>
                </div>
            </div>
            
            <div class="section-title">
                <i class="fas fa-phone-alt"></i>
                <span class="dual-language">
                    <span class="english">Contact Information</span>
                    <span class="tigrinya">/ ምልክታ ርክብ</span>
                </span>
            </div>
            
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-mobile-alt"></i>
                        <span class="dual-language">
                            <span class="english">Phone Number</span>
                            <span class="tigrinya">/ ቁጽሪ ስልኪ</span>
                        </span>
                    </div>
                    <div class="info-value"><?= htmlspecialchars($member['telephone']) ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-envelope"></i>
                        <span class="dual-language">
                            <span class="english">Email Address</span>
                            <span class="tigrinya">/ ኢመይል</span>
                        </span>
                    </div>
                    <div class="info-value"><?= htmlspecialchars($member['email']) ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-home"></i>
                        <span class="dual-language">
                            <span class="english">Address</span>
                            <span class="tigrinya">/ ኣድራሻ</span>
                        </span>
                    </div>
                    <div class="info-value"><?= htmlspecialchars($member['address']) ?></div>
                </div>
            </div>
            
            <div class="section-title">
                <i class="fas fa-church"></i>
                <span class="dual-language">
                    <span class="english">Church Information</span>
                    <span class="tigrinya">/ ናይ ቤተክርስቲያን ሓበሬታ</span>
                </span>
            </div>
            
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-graduation-cap"></i>
                        <span class="dual-language">
                            <span class="english">Education Level</span>
                            <span class="tigrinya">/ ደረጃ ትምህርቲ</span>
                        </span>
                    </div>
                    <div class="info-value"><?= htmlspecialchars($member['education']) ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-hand-holding-heart"></i>
                        <span class="dual-language">
                            <span class="english">Church Contribution</span>
                            <span class="tigrinya">/ ኣበርክቶ ቤተክርስቲያን</span>
                        </span>
                    </div>
                    <div class="info-value"><?= htmlspecialchars($member['church_contribution']) ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-star"></i>
                        <span class="dual-language">
                            <span class="english">Performance</span>
                            <span class="tigrinya">/ ኣፅድቓ</span>
                        </span>
                    </div>
                    <div class="info-value"><?= htmlspecialchars($member['performance']) ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-trophy"></i>
                        <span class="dual-language">
                            <span class="english">Awards</span>
                            <span class="tigrinya">/ ሽልማት</span>
                        </span>
                    </div>
                    <div class="info-value"><?= htmlspecialchars($member['awards']) ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-certificate"></i>
                        <span class="dual-language">
                            <span class="english">Trainings</span>
                            <span class="tigrinya">/ ስልጠናታት</span>
                        </span>
                    </div>
                    <div class="info-value"><?= htmlspecialchars($member['trainings']) ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-hands-helping"></i>
                        <span class="dual-language">
                            <span class="english">Service Area</span>
                            <span class="tigrinya">/ ዞባ ኣገልግሎት</span>
                        </span>
                    </div>
                    <div class="info-value"><?= htmlspecialchars($member['service_area']) ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-clock"></i>
                        <span class="dual-language">
                            <span class="english">Experience (Years)</span>
                            <span class="tigrinya">/ ልምዲ (ዓመታት)</span>
                        </span>
                    </div>
                    <div class="info-value"><?= htmlspecialchars($member['experience_years']) ?></div>
                </div>
            </div>
            
            <div class="section-title">
                <i class="fas fa-certificate"></i>
                <span class="dual-language">
                    <span class="english">Credentials & Certifications</span>
                    <span class="tigrinya">/ ምስክር ወረቐታት</span>
                </span>
            </div>
            
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-certificate"></i>
                        <span class="dual-language">
                            <span class="english">Baptism Certificate</span>
                            <span class="tigrinya">/ ምስክር ጥምቀት</span>
                        </span>
                    </div>
                    <div class="info-value">
                        <?php if (!empty($member['baptism_certificate'])): ?>
                            <a href="uploads/<?= htmlspecialchars($member['baptism_certificate']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> View
                            </a>
                        <?php else: ?>
                            <span class="text-muted">Not provided</span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-certificate"></i>
                        <span class="dual-language">
                            <span class="english">Marriage Certificate</span>
                            <span class="tigrinya">/ ምስክር ሓዳር</span>
                        </span>
                    </div>
                    <div class="info-value">
                        <?php if (!empty($member['marriage_certificate'])): ?>
                            <a href="uploads/<?= htmlspecialchars($member['marriage_certificate']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> View
                            </a>
                        <?php else: ?>
                            <span class="text-muted">Not provided</span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-certificate"></i>
                        <span class="dual-language">
                            <span class="english">Education Certificates</span>
                            <span class="tigrinya">/ ምስክር ትምህርቲ</span>
                        </span>
                    </div>
                    <div class="info-value">
                        <?php if (!empty($member['education_certificates'])): ?>
                            <a href="uploads/<?= htmlspecialchars($member['education_certificates']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> View
                            </a>
                        <?php else: ?>
                            <span class="text-muted">Not provided</span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-certificate"></i>
                        <span class="dual-language">
                            <span class="english">Training Certificates</span>
                            <span class="tigrinya">/ ምስክር ስልጠና</span>
                        </span>
                    </div>
                    <div class="info-value">
                        <?php if (!empty($member['training_certificates'])): ?>
                            <a href="uploads/<?= htmlspecialchars($member['training_certificates']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> View
                            </a>
                        <?php else: ?>
                            <span class="text-muted">Not provided</span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-id-card"></i>
                        <span class="dual-language">
                            <span class="english">ID Document</span>
                            <span class="tigrinya">/ ምስክር ምንዳይ</span>
                        </span>
                    </div>
                    <div class="info-value">
                        <?php if (!empty($member['id_document'])): ?>
                            <a href="uploads/<?= htmlspecialchars($member['id_document']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> View
                            </a>
                        <?php else: ?>
                            <span class="text-muted">Not provided</span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-file-alt"></i>
                        <span class="dual-language">
                            <span class="english">Other Documents</span>
                            <span class="tigrinya">/ ካልእ ሰነዳት</span>
                        </span>
                    </div>
                    <div class="info-value">
                        <?php if (!empty($member['other_documents'])): ?>
                            <a href="uploads/<?= htmlspecialchars($member['other_documents']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> View
                            </a>
                        <?php else: ?>
                            <span class="text-muted">Not provided</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="signature-section print-only">
                <div class="row mt-3">
                    <div class="col-md-6 text-center">
                        <div class="signature-line"></div>
                        <div>
                            <span class="dual-language">
                                <span class="english">Member Signature</span>
                                <span class="tigrinya">/ ፊርማ ኣባል</span>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6 text-center">
                        <div class="signature-line"></div>
                        <div>
                            <span class="dual-language">
                                <span class="english">Church Official</span>
                                <span class="tigrinya">/ ሓላፊ ቤተክርስቲያን</span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-2">
                    <small>
                        <span class="dual-language">
                            <span class="english">Date: <?= date('Y-m-d') ?></span>
                            <span class="tigrinya">/ ዕለት: <?= date('Y-m-d') ?></span>
                        </span>
                    </small>
                </div>
            </div>
            
            <div class="action-buttons no-print">
                <a href="edit_member-.php?id=<?= $memberId ?>" class="btn btn-primary">
                    <i class="fas fa-edit"></i>
                    <span class="dual-language">
                        <span class="english">Edit</span>
                        <span class="tigrinya">/ ምሕዳር</span>
                    </span>
                </a>
                
                <button onclick="window.print()" class="btn btn-secondary">
                    <i class="fas fa-print"></i>
                    <span class="dual-language">
                        <span class="english">Print</span>
                        <span class="tigrinya">/ ምሕታም</span>
                    </span>
                </button>
                
                <div class="btn-group">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-file-export"></i>
                        <span class="dual-language">
                            <span class="english">Export</span>
                            <span class="tigrinya">/ ምውጻእ</span>
                        </span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="exportToPDF()"><i class="fas fa-file-pdf me-2"></i>PDF</a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportToWord()"><i class="fas fa-file-word me-2"></i>Word</a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportToExcel()"><i class="fas fa-file-excel me-2"></i>Excel</a></li>
                    </ul>
                </div>

                <a href="members.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    <span class="dual-language">
                        <span class="english">Back</span>
                        <span class="tigrinya">/ ተመለስ</span>
                    </span>
                </a>
            </div>
        <?php else: ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <span class="dual-language">
                    <span class="english">Member not found</span>
                    <span class="tigrinya">/ ኣባል ኣይተረኽበን</span>
                </span>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Export functions remain the same as before
    function exportToPDF() {
        const { jsPDF } = window.jspdf;
        const element = document.querySelector('.profile-card');
        const title = 'Member_Profile_<?= $memberId ?>';
        
        const loading = document.createElement('div');
        loading.style.position = 'fixed';
        loading.style.top = '0';
        loading.style.left = '0';
        loading.style.width = '100%';
        loading.style.height = '100%';
        loading.style.backgroundColor = 'rgba(0,0,0,0.5)';
        loading.style.display = 'flex';
        loading.style.justifyContent = 'center';
        loading.style.alignItems = 'center';
        loading.style.zIndex = '9999';
        loading.innerHTML = '<div style="background: white; padding: 20px; border-radius: 5px;"><i class="fas fa-spinner fa-spin me-2"></i>Generating PDF...</div>';
        document.body.appendChild(loading);
        
        html2canvas(element, {
            scale: 2,
            logging: false,
            useCORS: true
        }).then(canvas => {
            const imgData = canvas.toDataURL('image/png', 1.0);
            const pdf = new jsPDF('p', 'mm', 'a4');
            const imgProps = pdf.getImageProperties(imgData);
            const pdfWidth = pdf.internal.pageSize.getWidth();
            const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;
            
            pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
            pdf.save(`${title}.pdf`);
            document.body.removeChild(loading);
        }).catch(err => {
            console.error('Error generating PDF:', err);
            document.body.removeChild(loading);
            alert('Error generating PDF. Please try again.');
        });
    }

    function exportToWord() {
        const element = document.querySelector('.profile-card').cloneNode(true);
        const buttons = element.querySelector('.action-buttons');
        if (buttons) buttons.remove();
        
        const images = element.querySelectorAll('img');
        images.forEach(img => {
            if (img.src.startsWith('http')) return;
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            canvas.width = img.width;
            canvas.height = img.height;
            ctx.drawImage(img, 0, 0);
            img.src = canvas.toDataURL('image/png');
        });
        
        const html = `
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <style>
                    body { font-family: Arial, sans-serif; margin: 15px; }
                    .profile-card { max-width: 800px; margin: 0 auto; }
                    .section-title { color: #2c3e50; font-weight: bold; border-bottom: 1px solid #eee; padding-bottom: 3px; margin: 10px 0 5px 0; }
                    .info-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 5px; margin-bottom: 10px; }
                    .info-item { margin: 2px 0; padding: 3px; border: 1px solid #f0f0f0; border-radius: 3px; }
                    .info-label { font-weight: bold; color: #555; margin-bottom: 1px; }
                    .profile-image { width: 100px; height: 100px; border-radius: 50%; border: 2px solid #eee; margin: 5px auto; }
                </style>
            </head>
            <body>
                ${element.outerHTML}
            </body>
            </html>
        `;
        
        const blob = new Blob(['\ufeff', html], { type: 'application/msword' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'Member_Profile_<?= $memberId ?>.doc';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }

    function exportToExcel() {
        const wb = XLSX.utils.book_new();
        const data = [];
        
        data.push(["UGBC Member Profile", "", "", ""]);
        data.push(["Name:", "<?= htmlspecialchars($fullName) ?>", "Member ID:", "<?= htmlspecialchars($member['id']) ?>"]);
        data.push(["Date of Birth:", "<?= htmlspecialchars($member['dob']) ?> (Age: <?= $age ?>)", "", ""]);
        data.push([]);
        
        const sections = [
            { 
                title: "Personal Information", 
                items: [
                    ["Gender", "<?= htmlspecialchars($member['sex']) ?>"],
                    ["Marital Status", "<?= htmlspecialchars($member['marital_status']) ?>"],
                    ["Spouse Name", "<?= !empty($member['spouse_name']) ? htmlspecialchars($member['spouse_name']) : 'N/A' ?>"],
                    ["Family Members", "<?= htmlspecialchars($member['no_family_members']) ?>"]
                ]
            },
            { 
                title: "Contact Information", 
                items: [
                    ["Phone Number", "<?= htmlspecialchars($member['telephone']) ?>"],
                    ["Email Address", "<?= htmlspecialchars($member['email']) ?>"],
                    ["Address", "<?= htmlspecialchars($member['address']) ?>"]
                ]
            },
            { 
                title: "Church Information", 
                items: [
                    ["Education Level", "<?= htmlspecialchars($member['education']) ?>"],
                    ["Church Contribution", "<?= htmlspecialchars($member['church_contribution']) ?>"],
                    ["Performance", "<?= htmlspecialchars($member['performance']) ?>"],
                    ["Awards", "<?= htmlspecialchars($member['awards']) ?>"],
                    ["Trainings", "<?= htmlspecialchars($member['trainings']) ?>"],
                    ["Service Area", "<?= htmlspecialchars($member['service_area']) ?>"],
                    ["Experience (Years)", "<?= htmlspecialchars($member['experience_years']) ?>"]
                ]
            },
            { 
                title: "Credentials & Certifications", 
                items: [
                    ["Baptism Certificate", "<?= !empty($member['baptism_certificate']) ? 'Available' : 'Not provided' ?>"],
                    ["Marriage Certificate", "<?= !empty($member['marriage_certificate']) ? 'Available' : 'Not provided' ?>"],
                    ["Education Certificates", "<?= !empty($member['education_certificates']) ? 'Available' : 'Not provided' ?>"],
                    ["Training Certificates", "<?= !empty($member['training_certificates']) ? 'Available' : 'Not provided' ?>"],
                    ["ID Document", "<?= !empty($member['id_document']) ? 'Available' : 'Not provided' ?>"],
                    ["Other Documents", "<?= !empty($member['other_documents']) ? 'Available' : 'Not provided' ?>"]
                ]
            }
        ];
        
        sections.forEach(section => {
            data.push([section.title, "", "", ""]);
            section.items.forEach(item => {
                data.push(item);
            });
            data.push([]);
        });
        
        data.push(["Exported on:", "<?= date('Y-m-d H:i:s') ?>", "", ""]);
        
        const ws = XLSX.utils.aoa_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, "Member Profile");
        XLSX.writeFile(wb, 'Member_Profile_<?= $memberId ?>.xlsx');
    }
</script>
</body>
</html>