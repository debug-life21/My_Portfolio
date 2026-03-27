# DKU Smart Café Management System

A modern, web-based system for managing university café meals using QR code verification. Built with PHP, MySQL, and modern web technologies.

## 🌟 Features

### Core Functionality
- **Student Registration & Authentication**: Secure user registration with Ethiopian phone validation
- **QR Code Generation**: Dynamic QR codes for meal access verification
- **Real-time Meal Verification**: Automated session-based meal validation
- **Admin Dashboard**: Comprehensive management interface
- **Public Kiosk Mode**: Camera-based QR scanning for meal entry
- **Bilingual Support**: English and Amharic language options

### New Features
- **Student Profile Dashboard**: Personal meal history and statistics
- **Feedback System**: Star ratings and meal experience feedback
- **Notification Center**: Real-time notifications for meal sessions
- **Advanced Search**: Admin search functionality for student management
- **Dark Mode**: Modern UI with theme switching
- **Responsive Design**: Mobile-friendly interface

## 🚀 Quick Start

### Prerequisites
- **PHP**: 8.0 or higher (recommended 8.2+)
- **MySQL**: 5.7 or higher (recommended 8.0+)
- **Web Server**: Apache/Nginx with mod_rewrite
- **Browser**: Modern browser with camera support for QR scanning

### Installation

1. **Download/Clone the Project**
   ```bash
   git clone <repository-url>
   cd dku_cafe/cafe
   ```

2. **Environment Setup**
   ```bash
   # Copy environment template
   cp .env.example .env

   # Edit .env with your database credentials
   nano .env
   ```

3. **Database Initialization**
   - Create MySQL database: `dku_cafe`
   - Open `setup.php` in your browser to initialize tables and default admin
   - Default admin credentials: `admin@dku.edu.et` / `admin123`

4. **Access the Application**
   - Open `index.php` in your browser
   - Register as a student or login as admin

## 📋 Usage Guide

### For Students
1. **Register**: Create account with valid Ethiopian phone number and national ID
2. **Login**: Access your dashboard
3. **Generate QR**: Get your personal QR code for meal access
4. **Scan at Gate**: Use kiosk to scan QR during meal sessions
5. **View History**: Check meal history and statistics in profile
6. **Provide Feedback**: Rate and review your meal experiences

### For Administrators
1. **Dashboard**: Overview of registered students
2. **Menu Management**: Set daily menus for each meal session
3. **Student Search**: Find students by name, email, or ID
4. **Add Admins**: Create additional administrator accounts
5. **Test Tools**: Use schedule testing tools for development

### Meal Sessions
- **Breakfast**: 7:00 AM - 8:00 AM (Local Ethiopian Time)
- **Lunch**: 11:30 AM - 1:00 PM
- **Dinner**: 5:00 PM - 6:30 PM

## 🏗️ Project Structure

```
dku_cafe/cafe/
├── 📄 index.php              # Landing page
├── 📄 login.php              # User authentication
├── 📄 register.php           # Student registration
├── 📄 dashboard.php          # Admin dashboard
├── 📄 profile.php            # Student profile & history
├── 📄 feedback.php           # Meal feedback system
├── 📄 my_qr.php              # QR code generation
├── 📄 gate.php               # Public kiosk interface
├── 📄 menu_display.php       # Meal display after scan
├── 📄 manage_menu.php        # Admin menu management
├── 📄 add_admin.php          # Admin user creation
├── 📄 test_schedule.php      # Development testing tool
├── 📄 check_meal.php         # API: Meal verification
├── 📄 setup.php              # Database initialization
├── 📄 config.php             # Configuration & translations
├── 📄 header.php             # Common header template
├── 📄 footer.php             # Common footer template
├── 📄 style.css              # Modern responsive styles
├── 📄 README.md              # This documentation
├── 📄 .htaccess              # Security & server config
├── 📄 .env.example           # Environment template
└── 📁 js/                    # JavaScript libraries
    ├── html5-qrcode.min.js   # QR scanning library
    └── qrcode.min.js         # QR generation library
```

## 🔧 Configuration

### Environment Variables (.env)
```env
# Database Configuration
DB_HOST=localhost
DB_NAME=dku_cafe
DB_USER=your_db_user
DB_PASS=your_db_password

# Application Settings
APP_ENV=production  # development or production
```

### Database Tables
- `users`: Student and admin accounts
- `meal_sessions`: Breakfast/Lunch/Dinner time slots
- `meal_logs`: Scan history and meal verification
- `daily_menu`: Menu items and images per session

## 🌐 API Endpoints

### Meal Verification
- **POST** `/check_meal.php`
  - Validates QR scans and logs meals
  - Parameters: `national_id`, `gate_mode`

### Authentication Flow
- **POST** `/login.php` - User login
- **POST** `/register.php` - User registration
- **GET** `/logout.php` - Session termination

## 🚀 Deployment

### Local Development (XAMPP)
```bash
# Place in htdocs/
# Start Apache & MySQL
# Access: http://localhost/dku_cafe/cafe/
```

### Production Server
1. **Upload files** to web root
2. **Configure HTTPS** (required for camera access)
3. **Set environment variables** via control panel
4. **Update file permissions** for security
5. **Configure cron jobs** for automated tasks (optional)

### Docker Deployment (Optional)
```dockerfile
FROM php:8.2-apache
COPY . /var/www/html/
RUN docker-php-ext-install pdo pdo_mysql
EXPOSE 80
```

## 🔒 Security Features

- **Password Hashing**: Bcrypt password storage
- **Prepared Statements**: SQL injection prevention
- **Session Management**: Secure PHP sessions
- **Input Validation**: Server-side validation
- **HTTPS Enforcement**: Camera access requires SSL
- **File Permissions**: Protected configuration files
- **CSRF Protection**: Form submission validation

## 🐛 Troubleshooting

### Common Issues

**QR Scanner not working:**
- Ensure HTTPS in production
- Check camera permissions
- Verify JavaScript console for errors

**Database connection failed:**
- Verify `.env` credentials
- Check MySQL server status
- Ensure database exists

**Meal sessions not active:**
- Check server timezone settings
- Verify meal session times
- Use admin test tools for debugging

### Debug Mode
Set `APP_ENV=development` in `.env` to enable error reporting.

## 🤝 Contributing

1. Fork the repository
2. Create feature branch: `git checkout -b feature-name`
3. Commit changes: `git commit -am 'Add feature'`
4. Push to branch: `git push origin feature-name`
5. Submit pull request

## 📄 License

This project is licensed under the MIT License - see LICENSE file for details.

## 📞 Support

For support or questions:
- Create an issue in the repository
- Contact: debuglife1221@gmail.com
- Documentation: This README

---

**Built with ❤️ for Debark University community**