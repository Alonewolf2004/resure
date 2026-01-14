<?php
require_once 'includes/security.php';
requireAuth();

$username = $_SESSION['username'];

// Database connection
$conn = getSecureConnection();

// Create appointments table if it doesn't exist
$conn->query("CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    doctor_name VARCHAR(100) NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    reason VARCHAR(255),
    status VARCHAR(20) DEFAULT 'Scheduled',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$success = "";
$error = "";

// Handle new appointment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['book_appointment'])) {
    // Validate CSRF token
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = "Security validation failed. Please try again.";
        logSecurityEvent('CSRF_FAILURE', 'Appointment booking');
    } else {
        $doctor = sanitizeInput($_POST['doctor']);
        $date = sanitizeInput($_POST['date']);
        $time = sanitizeInput($_POST['time']);
        $reason = sanitizeInput($_POST['reason']);
        $user_id = $_SESSION['user_id'];

        $stmt = $conn->prepare("INSERT INTO appointments (user_id, doctor_name, appointment_date, appointment_time, reason) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $user_id, $doctor, $date, $time, $reason);
        
        if ($stmt->execute()) {
            $success = "Appointment booked successfully!";
            logSecurityEvent('APPOINTMENT_BOOKED', "Doctor: $doctor, Date: $date");
        } else {
            $error = "Failed to book appointment. Please try again.";
        }
    }
}

// Handle cancel appointment (with CSRF via token in URL)
if (isset($_GET['cancel']) && isset($_GET['token'])) {
    if (!validateCSRFToken($_GET['token'])) {
        $error = "Security validation failed.";
    } else {
        $apt_id = intval($_GET['cancel']);
        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("UPDATE appointments SET status = 'Cancelled' WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $apt_id, $user_id);
        $stmt->execute();
        $success = "Appointment cancelled.";
        logSecurityEvent('APPOINTMENT_CANCELLED', "Appointment ID: $apt_id");
    }
}

// Get user's appointments using prepared statement
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM appointments WHERE user_id = ? ORDER BY appointment_date DESC, appointment_time DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$appointments = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments - ReSure</title>
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
            background: #f8fafb;
            min-height: 100vh;
        }

        /* Calm Color Palette */
        :root {
            --primary: #0077b6;
            --primary-light: #0096c7;
            --primary-dark: #005f89;
            --secondary: #023e8a;
            --accent: #e8f4f2;
            --text-dark: #2c3e50;
            --text-light: #6b7c8a;
            --bg-light: #f8fafb;
            --white: #ffffff;
            --success: #4caf50;
            --warning: #ff9800;
            --danger: #ef5350;
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
            box-shadow: 0 4px 20px rgba(0, 119, 182, 0.2);
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
            color: rgba(255,255,255,0.85);
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
            background: rgba(239, 83, 80, 0.9);
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
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 30px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-header h1 {
            font-size: 2rem;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .page-header p {
            opacity: 0.9;
            margin-top: 8px;
        }

        .book-btn {
            background: white;
            color: var(--primary);
            padding: 14px 28px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
            cursor: pointer;
            border: none;
            font-size: 15px;
            font-family: inherit;
        }

        .book-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        /* Layout Grid */
        .appointments-layout {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 30px;
        }

        /* Appointments List */
        .appointments-section {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        .section-title {
            font-size: 1.3rem;
            color: var(--text-dark);
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: var(--primary);
        }

        /* Appointment Card */
        .appointment-card {
            background: var(--accent);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid var(--primary);
            transition: all 0.3s;
        }

        .appointment-card:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        .appointment-card.cancelled {
            border-left-color: var(--danger);
            opacity: 0.7;
        }

        .apt-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .apt-doctor {
            font-weight: 600;
            color: var(--text-dark);
            font-size: 1.05rem;
        }

        .apt-status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .apt-status.scheduled {
            background: rgba(76, 175, 80, 0.15);
            color: var(--success);
        }

        .apt-status.cancelled {
            background: rgba(239, 83, 80, 0.15);
            color: var(--danger);
        }

        .apt-datetime {
            display: flex;
            gap: 20px;
            margin-bottom: 10px;
            color: var(--text-light);
            font-size: 0.9rem;
        }

        .apt-datetime i {
            color: var(--primary);
            margin-right: 6px;
        }

        .apt-reason {
            color: var(--text-light);
            font-size: 0.9rem;
            font-style: italic;
        }

        .apt-actions {
            margin-top: 15px;
            display: flex;
            gap: 10px;
        }

        .apt-actions a {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s;
        }

        .cancel-btn {
            background: rgba(239, 83, 80, 0.1);
            color: var(--danger);
        }

        .cancel-btn:hover {
            background: var(--danger);
            color: white;
        }

        .reschedule-btn {
            background: rgba(0, 119, 182, 0.1);
            color: var(--primary);
        }

        .reschedule-btn:hover {
            background: var(--primary);
            color: white;
        }

        .no-appointments {
            text-align: center;
            padding: 50px 20px;
            color: var(--text-light);
        }

        .no-appointments i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }

        /* Booking Form */
        .booking-section {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            height: fit-content;
            position: sticky;
            top: 100px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-dark);
            font-size: 14px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e8ecef;
            border-radius: 12px;
            font-size: 15px;
            font-family: inherit;
            transition: all 0.3s;
            background: var(--bg-light);
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 4px rgba(0, 119, 182, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .submit-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-family: inherit;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 119, 182, 0.3);
        }

        /* Calendar Mini */
        .calendar-mini {
            background: var(--accent);
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .calendar-header h4 {
            color: var(--text-dark);
            font-size: 1rem;
        }

        .calendar-nav {
            display: flex;
            gap: 5px;
        }

        .calendar-nav button {
            width: 30px;
            height: 30px;
            border: none;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            color: var(--primary);
            transition: all 0.3s;
        }

        .calendar-nav button:hover {
            background: var(--primary);
            color: white;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
            text-align: center;
        }

        .calendar-day-name {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-light);
            padding: 5px;
        }

        .calendar-day {
            padding: 8px;
            font-size: 13px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            color: var(--text-dark);
        }

        .calendar-day:hover {
            background: var(--primary);
            color: white;
        }

        .calendar-day.today {
            background: var(--primary);
            color: white;
            font-weight: 600;
        }

        .calendar-day.other-month {
            color: #ccc;
        }

        /* Messages */
        .message {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .message.success {
            background: rgba(76, 175, 80, 0.1);
            color: var(--success);
        }

        .message.error {
            background: rgba(239, 83, 80, 0.1);
            color: var(--danger);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .appointments-layout {
                grid-template-columns: 1fr;
            }

            .booking-section {
                position: static;
            }
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 0 20px;
            }

            .nav-links {
                display: none;
            }

            .main-content {
                padding: 20px;
            }

            .page-header {
                flex-direction: column;
                text-align: center;
                gap: 20px;
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
            <li><a href="#"><i class="fa-solid fa-flask"></i> Trials</a></li>
            <li><a href="appointments.php" class="active"><i class="fa-solid fa-calendar"></i> Appointments</a></li>
        </ul>
        <div class="nav-right">
            <span class="user-greeting">Welcome, <strong><?php echo htmlspecialchars($username); ?></strong></span>
            <a href="logout.php" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </nav>

    <main class="main-content">
        <div class="page-header">
            <div>
                <h1><i class="fa-solid fa-calendar-check"></i> My Appointments</h1>
                <p>Schedule and manage your medical appointments</p>
            </div>
        </div>

        <?php if ($success): ?>
            <div class="message success">
                <i class="fa-solid fa-circle-check"></i>
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="message error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="appointments-layout">
            <!-- Appointments List -->
            <div class="appointments-section">
                <h2 class="section-title"><i class="fa-solid fa-list"></i> Upcoming Appointments</h2>
                
                <?php if ($appointments->num_rows > 0): ?>
                    <?php while ($apt = $appointments->fetch_assoc()): ?>
                        <div class="appointment-card <?php echo strtolower($apt['status']); ?>">
                            <div class="apt-header">
                                <span class="apt-doctor"><i class="fa-solid fa-user-doctor"></i> <?php echo htmlspecialchars($apt['doctor_name']); ?></span>
                                <span class="apt-status <?php echo strtolower($apt['status']); ?>"><?php echo $apt['status']; ?></span>
                            </div>
                            <div class="apt-datetime">
                                <span><i class="fa-solid fa-calendar"></i> <?php echo date('F j, Y', strtotime($apt['appointment_date'])); ?></span>
                                <span><i class="fa-solid fa-clock"></i> <?php echo date('g:i A', strtotime($apt['appointment_time'])); ?></span>
                            </div>
                            <?php if ($apt['reason']): ?>
                                <p class="apt-reason">"<?php echo htmlspecialchars($apt['reason']); ?>"</p>
                            <?php endif; ?>
                            <?php if ($apt['status'] === 'Scheduled'): ?>
                                <div class="apt-actions">
                                    <a href="appointments.php?cancel=<?php echo $apt['id']; ?>&token=<?php echo generateCSRFToken(); ?>" class="cancel-btn" onclick="return confirm('Cancel this appointment?')">
                                        <i class="fa-solid fa-xmark"></i> Cancel
                                    </a>
                                    <a href="#booking-form" class="reschedule-btn">
                                        <i class="fa-solid fa-calendar-pen"></i> Reschedule
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-appointments">
                        <i class="fa-solid fa-calendar-xmark"></i>
                        <h3>No appointments yet</h3>
                        <p>Book your first appointment using the form on the right.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Booking Form -->
            <div class="booking-section" id="booking-form">
                <h2 class="section-title"><i class="fa-solid fa-calendar-plus"></i> Book Appointment</h2>
                
                <!-- Mini Calendar -->
                <div class="calendar-mini">
                    <div class="calendar-header">
                        <h4 id="calendarMonth">January 2026</h4>
                        <div class="calendar-nav">
                            <button onclick="changeMonth(-1)"><i class="fa-solid fa-chevron-left"></i></button>
                            <button onclick="changeMonth(1)"><i class="fa-solid fa-chevron-right"></i></button>
                        </div>
                    </div>
                    <div class="calendar-grid" id="calendarGrid">
                        <div class="calendar-day-name">Su</div>
                        <div class="calendar-day-name">Mo</div>
                        <div class="calendar-day-name">Tu</div>
                        <div class="calendar-day-name">We</div>
                        <div class="calendar-day-name">Th</div>
                        <div class="calendar-day-name">Fr</div>
                        <div class="calendar-day-name">Sa</div>
                    </div>
                </div>

                <form method="POST">
                    <input type="hidden" name="book_appointment" value="1">
                    <?php echo csrfField(); ?>
                    
                    <div class="form-group">
                        <label><i class="fa-solid fa-user-doctor"></i> Select Doctor</label>
                        <select name="doctor" required>
                            <option value="">Choose a doctor...</option>
                            <option value="Dr. Sarah Johnson - Oncology">Dr. Sarah Johnson - Oncology</option>
                            <option value="Dr. Michael Chen - Cardiology">Dr. Michael Chen - Cardiology</option>
                            <option value="Dr. Emily Rodriguez - Neurology">Dr. Emily Rodriguez - Neurology</option>
                            <option value="Dr. James Wilson - General Medicine">Dr. James Wilson - General Medicine</option>
                            <option value="Dr. Priya Sharma - Endocrinology">Dr. Priya Sharma - Endocrinology</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><i class="fa-solid fa-calendar"></i> Select Date</label>
                        <input type="date" name="date" id="appointmentDate" required min="<?php echo date('Y-m-d'); ?>">
                    </div>

                    <div class="form-group">
                        <label><i class="fa-solid fa-clock"></i> Select Time</label>
                        <select name="time" required>
                            <option value="">Choose a time slot...</option>
                            <option value="09:00">9:00 AM</option>
                            <option value="09:30">9:30 AM</option>
                            <option value="10:00">10:00 AM</option>
                            <option value="10:30">10:30 AM</option>
                            <option value="11:00">11:00 AM</option>
                            <option value="11:30">11:30 AM</option>
                            <option value="14:00">2:00 PM</option>
                            <option value="14:30">2:30 PM</option>
                            <option value="15:00">3:00 PM</option>
                            <option value="15:30">3:30 PM</option>
                            <option value="16:00">4:00 PM</option>
                            <option value="16:30">4:30 PM</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><i class="fa-solid fa-comment-medical"></i> Reason for Visit</label>
                        <textarea name="reason" placeholder="Describe your symptoms or reason for the appointment..."></textarea>
                    </div>

                    <button type="submit" class="submit-btn">
                        <i class="fa-solid fa-calendar-check"></i> Book Appointment
                    </button>
                </form>
            </div>
        </div>
    </main>

    <script>
        let currentDate = new Date();
        let currentMonth = currentDate.getMonth();
        let currentYear = currentDate.getFullYear();

        function renderCalendar() {
            const months = ['January', 'February', 'March', 'April', 'May', 'June', 
                          'July', 'August', 'September', 'October', 'November', 'December'];
            
            document.getElementById('calendarMonth').textContent = months[currentMonth] + ' ' + currentYear;
            
            const grid = document.getElementById('calendarGrid');
            // Keep day names
            grid.innerHTML = `
                <div class="calendar-day-name">Su</div>
                <div class="calendar-day-name">Mo</div>
                <div class="calendar-day-name">Tu</div>
                <div class="calendar-day-name">We</div>
                <div class="calendar-day-name">Th</div>
                <div class="calendar-day-name">Fr</div>
                <div class="calendar-day-name">Sa</div>
            `;

            const firstDay = new Date(currentYear, currentMonth, 1).getDay();
            const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
            const today = new Date();

            // Empty cells for days before first day
            for (let i = 0; i < firstDay; i++) {
                grid.innerHTML += '<div class="calendar-day other-month"></div>';
            }

            // Days of month
            for (let day = 1; day <= daysInMonth; day++) {
                const isToday = day === today.getDate() && 
                               currentMonth === today.getMonth() && 
                               currentYear === today.getFullYear();
                
                grid.innerHTML += `<div class="calendar-day ${isToday ? 'today' : ''}" onclick="selectDate(${day})">${day}</div>`;
            }
        }

        function changeMonth(delta) {
            currentMonth += delta;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            } else if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            renderCalendar();
        }

        function selectDate(day) {
            const dateStr = currentYear + '-' + 
                          String(currentMonth + 1).padStart(2, '0') + '-' + 
                          String(day).padStart(2, '0');
            document.getElementById('appointmentDate').value = dateStr;
        }

        // Initialize calendar
        renderCalendar();
    </script>
</body>
</html>


