<?php
require_once 'security.php';
requireAuth();

$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ReSure</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/c1918dbe9d.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-left">
            <span class="logo"><i class="fa-solid fa-flask-vial"></i> ReSure</span>
        </div>
        <ul class="nav-links">
            <li><a href="dashboard.php" class="active"><i class="fa-solid fa-house"></i> Dashboard</a></li>
            <li><a href="about.html"><i class="fa-solid fa-circle-info"></i> About</a></li>
            <li><a href="trials.php"><i class="fa-solid fa-flask"></i> Trials</a></li>
            <li><a href="appointments.php"><i class="fa-solid fa-calendar"></i> Appointments</a></li>
        </ul>
        <div class="nav-right">
            <span class="user-greeting">Welcome, <strong><?php echo htmlspecialchars($username); ?></strong></span>
            <a href="logout.php" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </nav>

    <main class="main-content">
        <!-- Hero Section with Image -->
        <section class="hero">
            <div class="hero-content">
                <h1>Welcome back, <?php echo htmlspecialchars($username); ?>! 👋</h1>
                <p>Your gateway to breakthrough clinical trials and cutting-edge medical research</p>
                <a href="#" class="hero-btn"><i class="fa-solid fa-search"></i> Explore Trials</a>
            </div>
            <div class="hero-image">
                <img src="hero-bg.png" alt="Medical Research">
            </div>
        </section>

        <!-- Stats Cards -->
        <section class="stats-section">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fa-solid fa-flask"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-number">12</span>
                    <span class="stat-label">Active Trials</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-number">3</span>
                    <span class="stat-label">Appointments</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="fa-solid fa-file-medical"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-number">8</span>
                    <span class="stat-label">Documents</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="fa-solid fa-bell"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-number">5</span>
                    <span class="stat-label">Notifications</span>
                </div>
            </div>
        </section>

        <!-- Featured Trials with Images -->
        <section class="trials-section">
            <div class="section-header">
                <h2><i class="fa-solid fa-star"></i> Featured Clinical Trials</h2>
                <a href="#" class="view-all">View All <i class="fa-solid fa-arrow-right"></i></a>
            </div>
            <div class="trials-grid">
                <div class="trial-card">
                    <div class="trial-image">
                        <img src="medical-bg.png" alt="Cancer Research">
                        <span class="trial-badge recruiting">Recruiting</span>
                    </div>
                    <div class="trial-content">
                        <h3>Cancer Immunotherapy Study</h3>
                        <p>Phase III clinical trial for revolutionary immunotherapy treatment targeting solid tumors.</p>
                        <div class="trial-meta">
                            <span><i class="fa-solid fa-location-dot"></i> Chennai</span>
                            <span><i class="fa-solid fa-clock"></i> 6 months</span>
                            <span><i class="fa-solid fa-users"></i> 150 spots</span>
                        </div>
                        <a href="#" class="trial-btn">Learn More <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>

                <div class="trial-card">
                    <div class="trial-image">
                        <img src="medical-bg.png" alt="Diabetes Study">
                        <span class="trial-badge open">Open</span>
                    </div>
                    <div class="trial-content">
                        <h3>Diabetes Prevention Program</h3>
                        <p>Lifestyle intervention study for pre-diabetic patients with personalized care plans.</p>
                        <div class="trial-meta">
                            <span><i class="fa-solid fa-location-dot"></i> Pondicherry</span>
                            <span><i class="fa-solid fa-clock"></i> 12 months</span>
                            <span><i class="fa-solid fa-users"></i> 200 spots</span>
                        </div>
                        <a href="#" class="trial-btn">Learn More <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>

                <div class="trial-card">
                    <div class="trial-image">
                        <img src="medical-bg.png" alt="Heart Health">
                        <span class="trial-badge new">New</span>
                    </div>
                    <div class="trial-content">
                        <h3>Cardiovascular Health Study</h3>
                        <p>Wearable device monitoring for early detection of heart conditions.</p>
                        <div class="trial-meta">
                            <span><i class="fa-solid fa-location-dot"></i> Multiple</span>
                            <span><i class="fa-solid fa-clock"></i> 3 months</span>
                            <span><i class="fa-solid fa-users"></i> 500 spots</span>
                        </div>
                        <a href="#" class="trial-btn">Learn More <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Quick Actions -->
        <section class="actions-section">
            <h2><i class="fa-solid fa-bolt"></i> Quick Actions</h2>
            <div class="actions-grid">
                <a href="#" class="action-card">
                    <i class="fa-solid fa-search"></i>
                    <span>Find Trials</span>
                </a>
                <a href="appointments.php" class="action-card">
                    <i class="fa-solid fa-user-doctor"></i>
                    <span>Book Consultation</span>
                </a>
                <a href="#" class="action-card">
                    <i class="fa-solid fa-file-lines"></i>
                    <span>View Reports</span>
                </a>
                <a href="about.html" class="action-card">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>Learn More</span>
                </a>
            </div>
        </section>
    </main>

    <!-- Beautiful Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-main">
                <div class="footer-brand">
                    <h3><i class="fa-solid fa-flask-vial"></i> ReSure</h3>
                    <p>Empowering patients to participate in groundbreaking clinical trials and medical research studies.</p>
                    <div class="social-links">
                        <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#"><i class="fa-brands fa-twitter"></i></a>
                        <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                        <a href="#"><i class="fa-brands fa-instagram"></i></a>
                    </div>
                </div>
                <div class="footer-links">
                    <div class="footer-col">
                        <h4>Platform</h4>
                        <a href="#">Find Trials</a>
                        <a href="#">How It Works</a>
                        <a href="#">For Researchers</a>
                        <a href="#">Success Stories</a>
                    </div>
                    <div class="footer-col">
                        <h4>Company</h4>
                        <a href="about.html">About Us</a>
                        <a href="#">Careers</a>
                        <a href="#">Press</a>
                        <a href="#">Contact</a>
                    </div>
                    <div class="footer-col">
                        <h4>Support</h4>
                        <a href="#">Help Center</a>
                        <a href="#">Privacy Policy</a>
                        <a href="#">Terms of Service</a>
                        <a href="#">FAQs</a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© 2026 ReSure Pvt. Ltd. All rights reserved.</p>
                <p>Made with <i class="fa-solid fa-heart"></i> for better healthcare</p>
            </div>
        </div>
    </footer>
</body>
</html>
