<?php
require_once 'security.php';

// Database connection
$conn = getSecureConnection();

$error = "";
$success = "";

// Handle Sign Up
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'signup') {
    // Validate CSRF token
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = "Security validation failed. Please try again.";
        logSecurityEvent('CSRF_FAILURE', 'Signup attempt');
    } 
    // Check rate limit
    elseif (!checkRateLimit('signup', 5, 300)) {
        $error = "Too many signup attempts. Please try again in 5 minutes.";
        logSecurityEvent('RATE_LIMIT', 'Signup blocked');
    } else {
        $username = sanitizeInput(trim($_POST["username"]));
        $email = sanitizeEmail($_POST["email"]);
        $password = $_POST["password"];

        if (empty($username) || empty($email) || empty($password)) {
            $error = "All fields are required.";
        } elseif (!$email) {
            $error = "Please enter a valid email address.";
        } else {
            $passValidation = validatePassword($password);
            if (!$passValidation['valid']) {
                $error = $passValidation['message'];
            } else {
                // Check if email already exists
                $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
                $check->bind_param("s", $email);
                $check->execute();
                if ($check->get_result()->num_rows > 0) {
                    $error = "Email already registered. Please sign in.";
                } else {
                    // Insert new user
                    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt->bind_param("sss", $username, $email, $hashed_password);
                    
                    if ($stmt->execute()) {
                        $success = "Account created! Please sign in.";
                        logSecurityEvent('SIGNUP_SUCCESS', "User: $email");
                        resetRateLimit('signup');
                    } else {
                        $error = "Error creating account. Please try again.";
                        logSecurityEvent('SIGNUP_FAILURE', "Database error for: $email");
                    }
                }
            }
        }
    }
}

// Handle Sign In
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'signin') {
    // Validate CSRF token
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = "Security validation failed. Please try again.";
        logSecurityEvent('CSRF_FAILURE', 'Signin attempt');
    }
    // Check rate limit
    elseif (!checkRateLimit('signin', 5, 300)) {
        $error = "Too many login attempts. Please try again in 5 minutes.";
        logSecurityEvent('RATE_LIMIT', 'Signin blocked');
    } else {
        $email = sanitizeEmail($_POST["email"]);
        $password = $_POST["password"];

        if (empty($email) || empty($password)) {
            $error = "Email and password are required.";
        } else {
            $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['password'])) {
                    // Regenerate session ID on login
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['login_time'] = time();
                    
                    logSecurityEvent('LOGIN_SUCCESS', "User: $email");
                    resetRateLimit('signin');
                    
                    header("Location: dashboard.php");
                    exit;
                }
            }
            $error = "Invalid email or password.";
            logSecurityEvent('LOGIN_FAILURE', "Failed attempt for: $email");
        }
    }
}

$conn->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReSure - Login</title>
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
            min-height: 100vh;
            background: linear-gradient(135deg, #0077b6 0%, #023e8a 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 420px;
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo h1 {
            color: white;
            font-size: 2.5rem;
            font-weight: 700;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        .logo p {
            color: rgba(255,255,255,0.8);
            margin-top: 8px;
        }

        .card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.25);
            overflow: hidden;
        }

        .tabs {
            display: flex;
            border-bottom: 1px solid #eee;
        }

        .tab {
            flex: 1;
            padding: 18px;
            text-align: center;
            font-weight: 600;
            font-size: 15px;
            color: #888;
            cursor: pointer;
            transition: all 0.3s;
            border-bottom: 3px solid transparent;
            background: #f8f9fa;
        }

        .tab.active {
            color: #0077b6;
            background: white;
            border-bottom-color: #0077b6;
        }

        .tab:hover:not(.active) {
            background: #f0f0f0;
        }

        .form-container {
            padding: 35px 30px;
        }

        .form {
            display: none;
        }

        .form.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 500;
            color: #333;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
            font-size: 16px;
        }

        .input-wrapper input {
            width: 100%;
            padding: 14px 16px 14px 48px;
            border: 2px solid #e8e8e8;
            border-radius: 12px;
            font-size: 15px;
            font-family: inherit;
            transition: all 0.3s;
            background: #f8f9fa;
        }

        .input-wrapper input:focus {
            outline: none;
            border-color: #0077b6;
            background: white;
            box-shadow: 0 0 0 4px rgba(0, 119, 182, 0.1);
        }

        .submit-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #0077b6 0%, #023e8a 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-family: inherit;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 119, 182, 0.4);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .error-msg {
            background: #fee;
            color: #c00;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .success-msg {
            background: #efe;
            color: #080;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .forgot-link {
            text-align: center;
            margin-top: 20px;
        }

        .forgot-link a {
            color: #0077b6;
            text-decoration: none;
            font-size: 14px;
        }

        .forgot-link a:hover {
            text-decoration: underline;
        }

        .footer-text {
            text-align: center;
            margin-top: 25px;
            color: rgba(255,255,255,0.7);
            font-size: 13px;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .logo h1 {
                font-size: 2rem;
            }

            .form-container {
                padding: 25px 20px;
            }

            .tab {
                padding: 15px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <h1><i class="fa-solid fa-flask-vial"></i> ReSure</h1>
            <p>Clinical Trials Platform</p>
        </div>

        <div class="card">
            <div class="tabs">
                <div class="tab active" data-tab="signin">Sign In</div>
                <div class="tab" data-tab="signup">Create Account</div>
            </div>

            <div class="form-container">
                <?php if ($error): ?>
                    <div class="error-msg">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="success-msg">
                        <i class="fa-solid fa-circle-check"></i>
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <!-- Sign In Form -->
                <form class="form active" id="signin-form" method="POST">
                    <input type="hidden" name="action" value="signin">
                    <?php echo csrfField(); ?>
                    
                    <div class="input-group">
                        <label>Email Address</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-envelope"></i>
                            <input type="email" name="email" placeholder="Enter your email" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <label>Password</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" name="password" placeholder="Enter your password" required>
                        </div>
                    </div>

                    <button type="submit" class="submit-btn">
                        <i class="fa-solid fa-right-to-bracket"></i> Sign In
                    </button>

                    <div class="forgot-link">
                        <a href="#">Forgot your password?</a>
                    </div>
                </form>

                <!-- Sign Up Form -->
                <form class="form" id="signup-form" method="POST">
                    <input type="hidden" name="action" value="signup">
                    <?php echo csrfField(); ?>
                    
                    <div class="input-group">
                        <label>Full Name</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-user"></i>
                            <input type="text" name="username" placeholder="Enter your name" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <label>Email Address</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-envelope"></i>
                            <input type="email" name="email" placeholder="Enter your email" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <label>Password</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" name="password" placeholder="Create a password" required minlength="6">
                        </div>
                    </div>

                    <button type="submit" class="submit-btn">
                        <i class="fa-solid fa-user-plus"></i> Create Account
                    </button>
                </form>
            </div>
        </div>

        <p class="footer-text">© 2026 ReSure Pvt. Ltd. All rights reserved.</p>
    </div>

    <script>
        // Tab switching
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', () => {
                // Update active tab
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                tab.classList.add('active');

                // Show corresponding form
                const tabName = tab.dataset.tab;
                document.querySelectorAll('.form').forEach(f => f.classList.remove('active'));
                document.getElementById(tabName + '-form').classList.add('active');
            });
        });

        // If there was a signup success, switch to signin tab
        <?php if ($success): ?>
        document.querySelector('[data-tab="signin"]').click();
        <?php endif; ?>
    </script>
</body>
</html>


