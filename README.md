# ReSure - Clinical Trials Platform

![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?style=flat&logo=mysql&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green.svg)

A modern web application for clinical trial management, allowing users to browse trials, book medical appointments, and access research information.

## 🌟 Features

- **User Authentication** - Secure signup/signin with password hashing
- **Dashboard** - Personalized overview with stats and featured trials
- **Clinical Trials** - Browse, search, and filter available trials
- **Appointments** - Book and manage medical appointments with calendar
- **About Page** - Learn about the platform and research studies
- **Security** - CSRF protection, rate limiting, input sanitization

## 📁 Project Structure

```
resure/
├── assets/
│   └── images/
│       ├── hero-bg.png          # Dashboard hero image
│       └── medical-bg.png       # Trial cards background
├── css/
│   └── dashboard.css            # Dashboard styles
├── includes/
│   └── security.php             # Security configuration
├── logs/
│   └── security.log             # Security event logs
├── index.php                    # Login/Signup page
├── dashboard.php                # Main dashboard
├── trials.php                   # Clinical trials listing
├── appointments.php             # Appointment booking
├── about.html                   # About page
├── logout.php                   # Session logout
└── README.md                    # This file
```

## 🚀 Installation

### Prerequisites

- XAMPP (or similar LAMP/WAMP stack)
- PHP 8.0+
- MySQL 5.7+

### Setup Steps

1. **Clone/Copy to htdocs**
   ```bash
   cd C:\xampp\htdocs
   git clone <repository-url> resure
   ```

2. **Start XAMPP**
   - Start Apache and MySQL from XAMPP Control Panel

3. **Create Database**
   - Open phpMyAdmin: http://localhost/phpmyadmin
   - Create database named `login page`
   - The `users` and `appointments` tables are created automatically

4. **Access the Application**
   - Open browser: http://localhost/resure

## 🔐 Security Features

| Feature | Description |
|---------|-------------|
| **CSRF Protection** | Token validation on all forms |
| **Rate Limiting** | 5 attempts per 5 minutes for auth |
| **Password Hashing** | bcrypt with PASSWORD_DEFAULT |
| **Input Sanitization** | XSS prevention on all inputs |
| **Secure Headers** | X-Frame-Options, CSP, XSS-Protection |
| **Session Security** | HTTPOnly cookies, regeneration on login |
| **Prepared Statements** | SQL injection prevention |

## 📊 Database Schema

### Users Table
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Appointments Table
```sql
CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    doctor_name VARCHAR(100) NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    reason VARCHAR(255),
    status VARCHAR(20) DEFAULT 'Scheduled',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## 🎨 Color Palette

The application uses a professional blue color scheme:

| Color | Hex | Usage |
|-------|-----|-------|
| Primary | `#0077b6` | Navigation, buttons, accents |
| Secondary | `#023e8a` | Gradients, dark elements |
| Light | `#0096c7` | Hover states, highlights |
| Accent | `#caf0f8` | Backgrounds, cards |

## 📱 Pages Overview

### Login Page (`index.php`)
- Tabbed signup/signin interface
- Form validation with error messages
- Secure authentication flow

### Dashboard (`dashboard.php`)
- Welcome message with stats cards
- Featured clinical trials
- Quick action buttons

### Trials (`trials.php`)
- Searchable trial listings
- Category filters
- Detailed trial cards with requirements

### Appointments (`appointments.php`)
- Mini calendar for date selection
- Doctor selection dropdown
- Appointment history with cancel/reschedule

### About (`about.html`)
- Mission statement
- Team section
- Research studies table

## 🛠️ Development

### Test Account
```
Email: test@example.com
Password: password123
```

### Adding New Features

1. Include security at top of PHP files:
   ```php
   require_once 'includes/security.php';
   requireAuth(); // If authentication required
   ```

2. Use CSRF tokens in forms:
   ```php
   <?php echo csrfField(); ?>
   ```

3. Sanitize all inputs:
   ```php
   $input = sanitizeInput($_POST['field']);
   ```

## 📝 License

This project is for educational purposes.

## 👥 Contributors

- Student Project - Web Programming Lab

---

Made with ❤️ for better healthcare
