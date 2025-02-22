<?php
return [
    'base_path' => __DIR__ . '/../public/files',
    'log_path' => __DIR__ . '/../logs',
    'log_settings' => [
        'enabled' => true,
        'types' => [
            'access' => true,    // Log access attempts
            'error' => true,     // Log errors
            'security' => true,  // Log security events
            'download' => true   // Log downloads
        ]
    ],
    'protected_dirs' => [
        'private' => [
            'password' => password_hash('12345', PASSWORD_DEFAULT),  // Password is: 12345
            'description' => 'Private Directory'
        ],
        'secret' => [
            'password' => password_hash('secretpass', PASSWORD_DEFAULT),  // Password is: secretpass
            'description' => 'Secret Files'
        ]
    ],
    'security' => [
        'allowed_extensions' => [
            // Documents
            'pdf', 'txt', 'doc', 'docx', 'xls', 'xlsx',
            'csv', 'json', 'xml',
            
            // Images
            'jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg',
            
            // Archives
            'zip', 'rar', '7z', 'tar', 'gz',
            
            // Audio
            'mp3', 'wav', 'ogg', 'aac', 'm4a', 'flac',
            
            // Videos
            'mp4', 'mkv', 'avi', 'mov', 'wmv', 'flv', 
            'webm', '3gp', 'm4v', 'mpeg', 'mpg', 'ts',
            'vob', 'ogv', 'rm', 'rmvb', 'asf', 'divx'
        ],
        'max_failed_attempts' => 5,
        'lockout_time' => 900,     // 15 minutes
        'session_lifetime' => 3600, // 1 hour
    ]
];