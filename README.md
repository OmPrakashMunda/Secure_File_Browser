# 🚀 Secure File Browser

A modern, secure, and feature-rich PHP file browser with protected directories, image previews, and a sleek UI. This project provides a web-based file browser with password-protected directories, image thumbnails, and a modern dark theme interface.

![PHP Version](https://img.shields.io/badge/PHP->=7.4-blue.svg)
![License](https://img.shields.io/badge/license-MIT-green.svg)

## ✨ Features

### 🔐 Security
- Password-protected directories with inheritance
- Secure session handling
- CSRF protection
- Brute force protection
- Path traversal prevention
- Secure file downloads
- Protected subdirectories inheritance

### 📸 File Management
- Image previews with lazy loading
- Automatic thumbnail generation
- File type detection with icons
- Secure file downloads
- Multiple file format support
- Directory browsing
- File size display

### 🎨 Modern UI
- Responsive dark theme design
- Grid layout
- Image thumbnails with hover effects
- Loading animations
- File type icons
- Clean navigation breadcrumbs
- Mobile-friendly interface

## 📋 Requirements

- PHP 7.4 or higher
- Apache/Nginx web server
- mod_rewrite enabled
- fileinfo extension
- GD/Imagick for image handling
- Composer (PHP package manager)

## 🚀 Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/secure-file-browser.git
cd secure-file-browser
```

2. Install dependencies using Composer:
```bash
composer install
```

3. Set up directory structure:
```bash
mkdir -p public/files
mkdir -p logs
chmod 755 public/files logs vendor
```

4. Configure protected directories in config/config.php:
```php
return [
    'protected_dirs' => [
        'private' => [
            'password' => password_hash('your_password', PASSWORD_DEFAULT),
            'description' => 'Private Directory'
        ]
    ]
];
```

5. Configure your web server:
```apache
# Apache configuration example
<VirtualHost *:80>
    ServerName yourdomain.com
    DocumentRoot /path/to/secure-file-browser/public
    
    <Directory /path/to/secure-file-browser/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

## 📁 Directory Structure

```
secure-file-browser/
├── composer.json           # Composer configuration
├── composer.lock          # Composer dependencies lock file
├── config/
│   ├── config.php         # Main configuration
│   └── mime_types.php     # MIME type definitions
├── logs/                  # Log directory
├── public/
│   ├── index.php         # Entry point
│   ├── .htaccess        # Apache configuration
│   └── files/           # File storage
├── src/                  # Source files (PSR-4 autoloaded)
│   ├── Authentication.php    # Auth handling
│   ├── FileManager.php      # File operations
│   ├── Logger.php          # Logging system
│   └── Security.php        # Security functions
├── templates/
│   ├── browser.php        # Main file browser
│   └── login.php         # Login form
└── vendor/               # Composer dependencies & autoloader
```

## ⚙️ Configuration

### Protected Directories
Configure password-protected directories in config/config.php:
```php
'protected_dirs' => [
    'private' => [
        'password' => password_hash('your_password', PASSWORD_DEFAULT),
        'description' => 'Private Directory'
    ],
    'secret' => [
        'password' => password_hash('another_password', PASSWORD_DEFAULT),
        'description' => 'Secret Files'
    ]
]
```

### Security Settings
```php
'security' => [
    'allowed_extensions' => ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'doc', 'docx'],
    'max_failed_attempts' => 5,
    'lockout_time' => 900,  // 15 minutes
    'session_lifetime' => 3600  // 1 hour
]
```

### MIME Types
Configure allowed file types in config/mime_types.php:
```php
'allowed_mimes' => [
    'application/pdf' => ['pdf'],
    'image/jpeg' => ['jpg', 'jpeg'],
    'image/png' => ['png'],
    'image/gif' => ['gif']
]
```

## 🔧 Composer Configuration

The project uses Composer for autoloading classes:

```json
{
    "name": "your-vendor/secure-file-browser",
    "description": "A secure PHP file browser with protected directories",
    "type": "project",
    "require": {
        "php": ">=7.4",
        "ext-fileinfo": "*"
    },
    "autoload": {
        "psr-4": {
            "SecureFileBrowser\\": "src/"
        }
    }
}
```

## 📝 Logging

The system automatically logs various events:
- Access attempts to protected directories
- Failed login attempts
- File downloads
- Error occurrences
- Security-related events

Logs are stored in the logs/ directory with daily rotation.

## 🛡️ Security Features

### Protected Directories
- Password protection with secure hashing
- Subdirectory inheritance (subfolders inherit parent's protection)
- Brute force protection with configurable limits
- Session-based authentication

### File Access
- Path traversal prevention
- MIME type validation
- Secure file downloads
- Protected subdirectories

### Authentication
- Session-based secure authentication
- Configurable session timeout
- Failed attempt limiting
- Strong password hashing

## 📱 Mobile Support

The interface is fully responsive and supports:
- Touch-friendly controls
- Adaptive grid layout
- Optimized image loading
- Mobile-friendly navigation

## 🎨 Customization

### Theme Customization
Modify CSS variables in templates/browser.php:
```css
:root {
    --bg-primary: #0a0b0e;
    --bg-secondary: #12151a;
    --text-primary: #e2e8f0;
    --accent: #2563eb;
    /* Add more custom colors */
}
```

### Adding File Types
Add new file types in config/mime_types.php:
```php
'allowed_mimes' => [
    'application/zip' => ['zip'],
    'audio/mpeg' => ['mp3'],
    // Add more types
]
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🔍 Support

For issues and feature requests, please use the GitHub issue tracker.

## 🙏 Credits

- Font Awesome for icons
- Modern browser features (IntersectionObserver, etc.)
- PHP community