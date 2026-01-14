<?php
require_once 'security.php';
requireAuth();

$username = $_SESSION['username'];

// Sample trials data
$trials = [
    [
        'id' => 1,
        'title' => 'Cancer Immunotherapy Study',
        'description' => 'Phase III clinical trial for revolutionary immunotherapy treatment targeting solid tumors using CAR-T cell technology.',
        'category' => 'Oncology',
        'location' => 'Chennai, India',
        'duration' => '6 months',
        'spots' => 150,
        'status' => 'Recruiting',
        'compensation' => '₹25,000',
        'requirements' => ['Age 18-65', 'No prior immunotherapy', 'Good health']
    ],
    [
        'id' => 2,
        'title' => 'Diabetes Prevention Program',
        'description' => 'Lifestyle intervention study for pre-diabetic patients with personalized diet and exercise plans.',
        'category' => 'Endocrinology',
        'location' => 'Pondicherry, India',
        'duration' => '12 months',
        'spots' => 200,
        'status' => 'Open',
        'compensation' => '₹15,000',
        'requirements' => ['Pre-diabetic diagnosis', 'BMI > 25', 'Age 30-60']
    ],
    [
        'id' => 3,
        'title' => 'Cardiovascular Health Study',
        'description' => 'Wearable device monitoring for early detection of heart conditions using AI-powered analysis.',
        'category' => 'Cardiology',
        'location' => 'Multiple Locations',
        'duration' => '3 months',
        'spots' => 500,
        'status' => 'New',
        'compensation' => '₹10,000',
        'requirements' => ['Age 40+', 'History of heart disease in family', 'Non-smoker']
    ],
    [
        'id' => 4,
        'title' => 'Alzheimer\'s Early Detection',
        'description' => 'Breakthrough study using biomarkers and cognitive assessments for early Alzheimer\'s detection.',
        'category' => 'Neurology',
        'location' => 'Mumbai, India',
        'duration' => '18 months',
        'spots' => 100,
        'status' => 'Recruiting',
        'compensation' => '₹30,000',
        'requirements' => ['Age 55+', 'Memory concerns', 'No dementia diagnosis']
    ],
    [
        'id' => 5,
        'title' => 'Asthma Treatment Trial',
        'description' => 'Testing new biologic therapy for severe asthma patients unresponsive to standard treatments.',
        'category' => 'Pulmonology',
        'location' => 'Delhi, India',
        'duration' => '9 months',
        'spots' => 75,
        'status' => 'Open',
        'compensation' => '₹20,000',
        'requirements' => ['Severe asthma diagnosis', 'Age 18-55', 'Non-smoker']
    ],
    [
        'id' => 6,
        'title' => 'Depression Treatment Study',
        'description' => 'Novel rapid-acting antidepressant therapy for treatment-resistant depression.',
        'category' => 'Psychiatry',
        'location' => 'Bangalore, India',
        'duration' => '4 months',
        'spots' => 120,
        'status' => 'Recruiting',
        'compensation' => '₹18,000',
        'requirements' => ['Depression diagnosis', 'Age 21-60', 'Failed 2+ treatments']
    ]
];

$categories = ['All', 'Oncology', 'Endocrinology', 'Cardiology', 'Neurology', 'Pulmonology', 'Psychiatry'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinical Trials - ReSure</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/c1918dbe9d.js" crossorigin="anonymous"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f0f9ff;
            min-height: 100vh;
        }

        /* Brighter Color Palette */
        :root {
            --primary: #0077b6;
            --primary-light: #0096c7;
            --primary-dark: #005f89;
            --secondary: #023e8a;
            --accent: #caf0f8;
            --text-dark: #2d3436;
            --text-light: #636e72;
            --bg-light: #f0f9ff;
            --white: #ffffff;
            --success: #0077b6;
            --warning: #fdcb6e;
            --info: #74b9ff;
            --danger: #ff7675;
        }

        /* Navbar */
        .navbar {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            height: 70px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
            box-shadow: 0 4px 20px rgba(0, 119, 182, 0.3);
        }

        .nav-left {
            display: flex;
            align-items: center;
        }

        .logo {
            color: white;
            font-size: 24px;
            font-weight: 700;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 5px;
        }

        .nav-links a {
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            padding: 10px 16px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-links a:hover, .nav-links a.active {
            background: rgba(255,255,255,0.2);
            color: white;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-greeting {
            color: rgba(255,255,255,0.9);
            font-size: 14px;
        }

        .logout-btn {
            background: rgba(255,255,255,0.15);
            color: white;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .logout-btn:hover {
            background: rgba(255, 118, 117, 0.9);
        }

        /* Main Content */
        .main-content {
            margin-top: 70px;
            padding: 30px 40px;
            max-width: 1400px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 24px;
            padding: 50px;
            margin-bottom: 30px;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 60%);
            border-radius: 50%;
        }

        .page-header h1 {
            font-size: 2.5rem;
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 10px;
            position: relative;
        }

        .page-header p {
            opacity: 0.9;
            font-size: 1.1rem;
            max-width: 600px;
            position: relative;
        }

        /* Search & Filters */
        .filters-section {
            background: white;
            border-radius: 20px;
            padding: 25px 30px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            align-items: center;
        }

        .search-box {
            flex: 1;
            min-width: 300px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 14px 20px 14px 50px;
            border: 2px solid #e8ecef;
            border-radius: 12px;
            font-size: 15px;
            font-family: inherit;
            transition: all 0.3s;
            background: var(--bg-light);
        }

        .search-box input:focus {
            outline: none;
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 4px rgba(0, 119, 182, 0.1);
        }

        .search-box i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
        }

        .category-filters {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 10px 20px;
            border: 2px solid #e8ecef;
            background: white;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            font-family: inherit;
            color: var(--text-dark);
        }

        .filter-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .filter-btn.active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            border-color: var(--primary);
            color: white;
        }

        /* Trials Grid */
        .trials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 25px;
        }

        .trial-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: all 0.3s;
            border: 2px solid transparent;
        }

        .trial-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 119, 182, 0.15);
            border-color: var(--primary);
        }

        .trial-header {
            padding: 25px 25px 0;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .trial-category {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            background: var(--accent);
            color: var(--primary);
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }

        .trial-status {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .trial-status.recruiting {
            background: linear-gradient(135deg, var(--success) 0%, #0096c7 100%);
            color: white;
        }

        .trial-status.open {
            background: linear-gradient(135deg, var(--secondary) 0%, #48cae4 100%);
            color: white;
        }

        .trial-status.new {
            background: linear-gradient(135deg, var(--warning) 0%, #ffeaa7 100%);
            color: #2d3436;
        }

        .trial-body {
            padding: 20px 25px;
        }

        .trial-title {
            font-size: 1.25rem;
            color: var(--text-dark);
            margin-bottom: 10px;
            font-weight: 600;
        }

        .trial-description {
            color: var(--text-light);
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .trial-meta {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            color: var(--text-light);
        }

        .meta-item i {
            color: var(--primary);
            width: 16px;
        }

        .trial-requirements {
            background: var(--bg-light);
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .trial-requirements h4 {
            font-size: 0.85rem;
            color: var(--text-dark);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .trial-requirements h4 i {
            color: var(--primary);
        }

        .requirements-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .requirement-tag {
            background: white;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            color: var(--text-light);
            border: 1px solid #e8ecef;
        }

        .trial-footer {
            padding: 0 25px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .compensation {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary);
        }

        .compensation span {
            font-size: 0.85rem;
            color: var(--text-light);
            font-weight: 400;
        }

        .apply-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-family: inherit;
        }

        .apply-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 119, 182, 0.35);
        }

        /* Stats Bar */
        .stats-bar {
            display: flex;
            gap: 30px;
            margin-bottom: 25px;
            color: var(--text-light);
            font-size: 14px;
        }

        .stats-bar span {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .stats-bar strong {
            color: var(--primary);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .navbar {
                padding: 0 20px;
            }

            .nav-links {
                display: none;
            }

            .page-header {
                padding: 40px 30px;
            }

            .page-header h1 {
                font-size: 2rem;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 20px;
            }

            .filters-section {
                flex-direction: column;
            }

            .search-box {
                min-width: 100%;
            }

            .trials-grid {
                grid-template-columns: 1fr;
            }

            .user-greeting {
                display: none;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-left">
            <a href="index.php" class="logo"><i class="fa-solid fa-flask-vial"></i> ReSure</a>
        </div>
        <ul class="nav-links">
            <li><a href="dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a></li>
            <li><a href="about.html"><i class="fa-solid fa-circle-info"></i> About</a></li>
            <li><a href="trials.php" class="active"><i class="fa-solid fa-flask"></i> Trials</a></li>
            <li><a href="appointments.php"><i class="fa-solid fa-calendar"></i> Appointments</a></li>
        </ul>
        <div class="nav-right">
            <span class="user-greeting">Welcome, <strong><?php echo htmlspecialchars($username); ?></strong></span>
            <a href="logout.php" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </nav>

    <main class="main-content">
        <div class="page-header">
            <h1><i class="fa-solid fa-flask"></i> Clinical Trials</h1>
            <p>Discover groundbreaking medical research opportunities and contribute to the future of healthcare</p>
        </div>

        <div class="filters-section">
            <div class="search-box">
                <i class="fa-solid fa-search"></i>
                <input type="text" id="searchInput" placeholder="Search trials by name, location, or category...">
            </div>
            <div class="category-filters">
                <?php foreach ($categories as $cat): ?>
                    <button class="filter-btn <?php echo $cat === 'All' ? 'active' : ''; ?>" data-category="<?php echo $cat; ?>">
                        <?php echo $cat; ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="stats-bar">
            <span><i class="fa-solid fa-flask"></i> <strong><?php echo count($trials); ?></strong> Active Trials</span>
            <span><i class="fa-solid fa-users"></i> <strong>1,145</strong> Total Spots Available</span>
            <span><i class="fa-solid fa-location-dot"></i> <strong>6</strong> Locations</span>
        </div>

        <div class="trials-grid" id="trialsGrid">
            <?php foreach ($trials as $trial): ?>
                <div class="trial-card" data-category="<?php echo $trial['category']; ?>">
                    <div class="trial-header">
                        <span class="trial-category">
                            <i class="fa-solid fa-stethoscope"></i>
                            <?php echo $trial['category']; ?>
                        </span>
                        <span class="trial-status <?php echo strtolower($trial['status']); ?>">
                            <?php echo $trial['status']; ?>
                        </span>
                    </div>
                    <div class="trial-body">
                        <h3 class="trial-title"><?php echo $trial['title']; ?></h3>
                        <p class="trial-description"><?php echo $trial['description']; ?></p>
                        <div class="trial-meta">
                            <div class="meta-item">
                                <i class="fa-solid fa-location-dot"></i>
                                <?php echo $trial['location']; ?>
                            </div>
                            <div class="meta-item">
                                <i class="fa-solid fa-clock"></i>
                                <?php echo $trial['duration']; ?>
                            </div>
                            <div class="meta-item">
                                <i class="fa-solid fa-users"></i>
                                <?php echo $trial['spots']; ?> spots
                            </div>
                            <div class="meta-item">
                                <i class="fa-solid fa-indian-rupee-sign"></i>
                                <?php echo $trial['compensation']; ?>
                            </div>
                        </div>
                        <div class="trial-requirements">
                            <h4><i class="fa-solid fa-clipboard-check"></i> Requirements</h4>
                            <div class="requirements-list">
                                <?php foreach ($trial['requirements'] as $req): ?>
                                    <span class="requirement-tag"><?php echo $req; ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="trial-footer">
                        <div class="compensation">
                            <?php echo $trial['compensation']; ?>
                            <span>compensation</span>
                        </div>
                        <button class="apply-btn">
                            Apply Now <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const cards = document.querySelectorAll('.trial-card');
            
            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(searchTerm) ? 'block' : 'none';
            });
        });

        // Category filter
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                const category = this.dataset.category;
                const cards = document.querySelectorAll('.trial-card');
                
                cards.forEach(card => {
                    if (category === 'All' || card.dataset.category === category) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>

