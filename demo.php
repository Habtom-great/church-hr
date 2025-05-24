<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Church HR Management | �ምድብ ሰብ ምምሕዳር ቤተክርስቲያን</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Ethiopic:wght@400;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
  
  <!-- Custom CSS -->
  <style>
    :root {
      --primary-color: #3a5a78;
      --secondary-color: #6c757d;
      --accent-color: #ffc107;
      --light-color: #f8f9fa;
      --dark-color: #343a40;
    }
    
    body {
      font-family: 'Roboto', 'Noto Sans Ethiopic', sans-serif;
      background-color: #f4f6f9;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    
    .navbar {
      background: linear-gradient(to right, var(--primary-color), var(--dark-color));
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    .navbar-brand {
      font-weight: bold;
      color: var(--light-color) !important;
      font-size: 1.5rem;
      letter-spacing: 1px;
      display: flex;
      align-items: center;
    }
    
    .navbar-brand img {
      height: 40px;
      margin-right: 10px;
    }
    
    .navbar-nav .nav-link {
      color: var(--light-color) !important;
      margin-left: 15px;
      transition: all 0.3s ease;
      font-size: 0.95rem;
      padding: 8px 12px;
      border-radius: 4px;
    }
    
    .navbar-nav .nav-link:hover {
      color: var(--accent-color) !important;
      background-color: rgba(255, 255, 255, 0.1);
    }
    
    .navbar-nav .nav-link i {
      margin-right: 5px;
    }
    
    .language-switcher {
      margin-left: 15px;
    }
    
    .language-switcher .btn {
      color: var(--light-color);
      border: 1px solid rgba(255, 255, 255, 0.2);
      padding: 5px 10px;
      font-size: 0.8rem;
      margin-left: 5px;
    }
    
    .language-switcher .btn:hover {
      background-color: rgba(255, 255, 255, 0.1);
      color: var(--accent-color);
    }
    
    .language-switcher .btn.active {
      background-color: var(--accent-color);
      color: var(--dark-color);
      font-weight: bold;
    }
    
    .main-container {
      flex: 1;
      padding: 30px 0;
    }
    
    .welcome-section {
      background: linear-gradient(rgba(58, 90, 120, 0.8), rgba(58, 90, 120, 0.9)), url('images/church-bg.jpg');
      background-size: cover;
      background-position: center;
      color: white;
      padding: 60px 0;
      border-radius: 8px;
      margin-bottom: 30px;
      text-align: center;
    }
    
    .welcome-section h1 {
      font-weight: 700;
      margin-bottom: 20px;
      font-size: 2.5rem;
    }
    
    .welcome-section p {
      font-size: 1.2rem;
      max-width: 800px;
      margin: 0 auto 30px;
    }
    
    .feature-card {
      border: none;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      height: 100%;
      background-color: white;
    }
    
    .feature-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }
    
    .feature-card .card-body {
      padding: 25px;
    }
    
    .feature-card .card-icon {
      font-size: 2.5rem;
      color: var(--primary-color);
      margin-bottom: 20px;
    }
    
    .feature-card .card-title {
      font-weight: 600;
      margin-bottom: 15px;
      color: var(--primary-color);
    }
    
    footer {
      background-color: var(--dark-color);
      color: var(--light-color);
      padding: 30px 0;
      margin-top: 50px;
    }
    
    footer h5 {
      font-weight: 600;
      margin-bottom: 20px;
      color: var(--accent-color);
    }
    
    footer a {
      color: var(--light-color);
      text-decoration: none;
      transition: color 0.3s ease;
    }
    
    footer a:hover {
      color: var(--accent-color);
    }
    
    .social-icons a {
      display: inline-block;
      width: 40px;
      height: 40px;
      background-color: rgba(255, 255, 255, 0.1);
      border-radius: 50%;
      text-align: center;
      line-height: 40px;
      margin-right: 10px;
      transition: all 0.3s ease;
    }
    
    .social-icons a:hover {
      background-color: var(--accent-color);
      color: var(--dark-color);
    }
    
    /* Tigrinya specific styles */
    .tigrinya {
      font-family: 'Noto Sans Ethiopic', sans-serif;
      direction: ltr;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
      .welcome-section {
        padding: 40px 0;
      }
      
      .welcome-section h1 {
        font-size: 2rem;
      }
      
      .feature-card {
        margin-bottom: 20px;
      }
    }
  </style>
</head>
<body>
  <!-- Navigation Bar -->
  <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
      <a class="navbar-brand" href="index.php">
        <img src="images/church-logo.png" alt="Church Logo">
        <span class="english">Church HR</span>
        <span class="tigrinya d-none">ምድብ ሰብ ቤተክርስቲያን</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <a class="nav-link" href="index.php">
              <i class="fas fa-home"></i>
              <span class="english">Home</span>
              <span class="tigrinya d-none">መበገሲ</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="about.php">
              <i class="fas fa-info-circle"></i>
              <span class="english">About</span>
              <span class="tigrinya d-none">ብዛዕባ</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="members.php">
              <i class="fas fa-users"></i>
              <span class="english">Members</span>
              <span class="tigrinya d-none">ኣባላት</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="events.php">
              <i class="fas fa-calendar-alt"></i>
              <span class="english">Events</span>
              <span class="tigrinya d-none">ኣጋጣሚታት</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="contact.php">
              <i class="fas fa-envelope"></i>
              <span class="english">Contact</span>
              <span class="tigrinya d-none">ርክብ</span>
            </a>
          </li>
        </ul>
        
        <div class="language-switcher">
          <button class="btn btn-sm english active" onclick="switchLanguage('english')">English</button>
          <button class="btn btn-sm tigrinya" onclick="switchLanguage('tigrinya')">ትግርኛ</button>
        </div>
        
        <div class="ms-3">
          <a href="login.php" class="btn btn-warning btn-sm">
            <i class="fas fa-sign-in-alt"></i>
            <span class="english">Login</span>
            <span class="tigrinya d-none">እተ</span>
          </a>
        </div>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <main class="main-container">
    <div class="container">
      <!-- Welcome Section -->
      <section class="welcome-section">
        <h1 class="english">Welcome to Church HR Management</h1>
        <h1 class="tigrinya d-none">እንቋዕ ብደሓን መጻእኩም ናብ ምድብ ሰብ ቤተክርስቲያን</h1>
        
        <p class="english">
          A comprehensive solution for managing your church members, staff, volunteers, and events.
        </p>
        <p class="tigrinya d-none">
          �ምርዕዳእ ሓፈሻዊ መፍትሕ ንምድላው ኣባላት ቤተክርስቲያን፣ ሰራሕተኛታት፣ ፈቃድታት፣ ከምኡውን ኣጋጣሚታት።
        </p>
        
        <a href="register.php" class="btn btn-light btn-lg me-2 english">
          <i class="fas fa-user-plus"></i> Register Now
        </a>
        <a href="register.php" class="btn btn-light btn-lg me-2 tigrinya d-none">
          <i class="fas fa-user-plus"></i> ሕጂ ተመዝገብ
        </a>
        
        <a href="demo.php" class="btn btn-outline-light btn-lg english">
          <i class="fas fa-play-circle"></i> View Demo
        </a>
        <a href="demo.php" class="btn btn-outline-light btn-lg tigrinya d-none">
          <i class="fas fa-play-circle"></i> ምሳሌ ርአ
        </a>
      </section>
      
      <!-- Features Section -->
      <section>
        <h2 class="text-center mb-5 english">Our Features</h2>
        <h2 class="text-center mb-5 tigrinya d-none">ባህርያትና</h2>
        
        <div class="row g-4">
          <div class="col-md-4">
            <div class="feature-card">
              <div class="card-body text-center">
                <div class="card-icon">
                  <i class="fas fa-user-friends"></i>
                </div>
                <h3 class="card-title english">Member Management</h3>
                <h3 class="card-title tigrinya d-none">ምድላው ኣባላት</h3>
                <p class="english">
                  Easily track and manage all church members with detailed profiles, attendance, and contribution records.
                </p>
                <p class="tigrinya d-none">
                  ብቐሊሉ ኩሉ ኣባላት ቤተክርስቲያን ምድላው፣ ዝርዝር መግለጺታት፣ ምህላውን ኣበርክቶን ምዝገባ።
                </p>
              </div>
            </div>
          </div>
          
          <div class="col-md-4">
            <div class="feature-card">
              <div class="card-body text-center">
                <div class="card-icon">
                  <i class="fas fa-calendar-check"></i>
                </div>
                <h3 class="card-title english">Event Planning</h3>
                <h3 class="card-title tigrinya d-none">ምዕቃእ ኣጋጣሚ</h3>
                <p class="english">
                  Organize church events, manage volunteers, and track attendance for services and special programs.
                </p>
                <p class="tigrinya d-none">
                  ኣጋጣሚታት ቤተክርስቲያን ምድላው፣ ፈቃድታት ምምሕዳር፣ ከምኡውን ኣብ ኣገልግሎትን ፍሉይ መደባትን ምህላው ምዝገባ።
                </p>
              </div>
            </div>
          </div>
          
          <div class="col-md-4">
            <div class="feature-card">
              <div class="card-body text-center">
                <div class="card-icon">
                  <i class="fas fa-chart-pie"></i>
                </div>
                <h3 class="card-title english">Reporting</h3>
                <h3 class="card-title tigrinya d-none">ሪፖርት</h3>
                <p class="english">
                  Generate comprehensive reports on membership growth, attendance trends, and financial contributions.
                </p>
                <p class="tigrinya d-none">
                  ሓፈሻዊ ሪፖርትታት ብዛዕባ ዕብየት ኣባላት፣ ኣዝማሚዕ �ምህላው፣ ከምኡውን ፋይናንሳዊ ኣበርክቶታት ምፍራይ።
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </main>

  <!-- Footer -->
  <footer>
    <div class="container">
      <div class="row">
        <div class="col-md-4">
          <h5 class="english">Church HR Management</h5>
          <h5 class="tigrinya d-none">ምድብ ሰብ ቤተክርስቲያን</h5>
          <p class="english">
            Empowering churches with efficient member management solutions.
          </p>
          <p class="tigrinya d-none">
            ንቤተክርስቲያናት ብግቡእ ምድላው ኣባላት ምሕያል ምግባር።
          </p>
        </div>
        
        <div class="col-md-4">
          <h5 class="english">Quick Links</h5>
          <h5 class="tigrinya d-none">ቅልጡፍ ሊንክታት</h5>
          <ul class="list-unstyled">
            <li><a href="about.php" class="english">About Us</a><a href="about.php" class="tigrinya d-none">ብዛዕባና</a></li>
            <li><a href="contact.php" class="english">Contact Us</a><a href="contact.php" class="tigrinya d-none">ርከብ</a></li>
            <li><a href="privacy.php" class="english">Privacy Policy</a><a href="privacy.php" class="tigrinya d-none">ፖሊሲ ምስጢር</a></li>
            <li><a href="terms.php" class="english">Terms of Service</a><a href="terms.php" class="tigrinya d-none">ውዕል ኣገልግሎት</a></li>
          </ul>
        </div>
        
        <div class="col-md-4">
          <h5 class="english">Connect With Us</h5>
          <h5 class="tigrinya d-none">ምስናን ተራኸቡ</h5>
          <div class="social-icons">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-youtube"></i></a>
          </div>
          
          <div class="mt-3 english">
            <p><i class="fas fa-envelope me-2"></i> info@churchhr.com</p>
            <p><i class="fas fa-phone me-2"></i> +1 (123) 456-7890</p>
          </div>
          
          <div class="mt-3 tigrinya d-none">
            <p><i class="fas fa-envelope me-2"></i> info@churchhr.com</p>
            <p><i class="fas fa-phone me-2"></i> +1 (123) 456-7890</p>
          </div>
        </div>
      </div>
      
      <hr class="my-4 bg-secondary">
      
      <div class="text-center">
        <p class="mb-0 english">
          &copy; 2023 Church HR Management. All rights reserved.
        </p>
        <p class="mb-0 tigrinya d-none">
          &copy; 2023 ምድብ ሰብ ቤተክርስቲያን. ኩሉ መሰል ይሕብር።
        </p>
      </div>
    </div>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- Custom JS -->
  <script>
    // Language switching functionality
    function switchLanguage(lang) {
      // Hide all language elements
      document.querySelectorAll('.english, .tigrinya').forEach(el => {
        el.classList.add('d-none');
      });
      
      // Show selected language elements
      document.querySelectorAll('.' + lang).forEach(el => {
        el.classList.remove('d-none');
      });
      
      // Update active button
      document.querySelectorAll('.language-switcher .btn').forEach(btn => {
        btn.classList.remove('active');
      });
      document.querySelector(`.language-switcher .btn.${lang}`).classList.add('active');
      
      // Store preference in localStorage
      localStorage.setItem('preferredLanguage', lang);
    }
    
    // Check for preferred language on page load
    document.addEventListener('DOMContentLoaded', function() {
      const preferredLanguage = localStorage.getItem('preferredLanguage') || 'english';
      switchLanguage(preferredLanguage);
    });
  </script>
</body>
</html>