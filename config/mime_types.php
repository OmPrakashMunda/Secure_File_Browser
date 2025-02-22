<?php
// config/mime_types.php
return [
    'allowed_mimes' => [
        'text/plain' => ['txt', 'log', 'ini'],
        'text/html' => ['html', 'htm'],
        'text/css' => ['css'],
        'text/javascript' => ['js'],
        'text/csv' => ['csv'],
        'application/json' => ['json'],
        'application/pdf' => ['pdf'],
        'application/xml' => ['xml'],
        'application/zip' => ['zip'],
        'application/x-rar' => ['rar'],
        'application/x-7z-compressed' => ['7z'],
        'application/msword' => ['doc'],
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => ['docx'],
        'application/vnd.ms-excel' => ['xls'],
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => ['xlsx'],
        'image/jpeg' => ['jpg', 'jpeg'],
        'image/png' => ['png'],
        'image/gif' => ['gif'],
        'image/webp' => ['webp'],
        'image/svg+xml' => ['svg'],
        'audio/mpeg' => ['mp3'],
        'audio/wav' => ['wav'],
        'video/mp4' => ['mp4'],
        'video/webm' => ['webm'],
        'video/x-msvideo' => ['avi']
    ]
];