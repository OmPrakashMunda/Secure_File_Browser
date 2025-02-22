<?php
namespace SecureFileBrowser;

class FileManager {
    private $config;
    private $security;
    private $mime_types;
    private $logger;

    public function isImage(string $filename): bool {
        $image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($extension, $image_extensions);
    }
    
    public function __construct(array $config, Security $security, array $mime_types, Logger $logger) {
        $this->config = $config;
        $this->security = $security;
        $this->mime_types = $mime_types;
        $this->logger = $logger;
    }

    public function listDirectory(string $path): array {
        try {
            $secure_path = $this->security->securePath($path);
            if ($secure_path === null) {
                $this->logger->error('Invalid path access attempt', ['path' => $path]);
                throw new \Exception('Invalid path');
            }

            $items = scandir($secure_path);
            if ($items === false) {
                $this->logger->error('Unable to scan directory', ['path' => $secure_path]);
                throw new \Exception('Unable to scan directory');
            }

            $directories = [];
            $files = [];

            foreach ($items as $item) {
                // Skip . and ..
                if ($item === '.' || $item === '..') {
                    continue;
                }

                // Skip hidden files
                if (substr($item, 0, 1) === '.') {
                    continue;
                }

                $item_path = $secure_path . DIRECTORY_SEPARATOR . $item;

                // Check if item is accessible
                if (!is_readable($item_path)) {
                    continue;
                }

                if (is_dir($item_path)) {
                    $directories[] = [
                        'name' => $item,
                        'type' => 'directory',
                        'path' => $item_path
                    ];
                } else {
                    $files[] = [
                        'name' => $item,
                        'type' => 'file',
                        'size' => $this->formatFileSize(filesize($item_path)),
                        'extension' => pathinfo($item, PATHINFO_EXTENSION),
                        'path' => $item_path
                    ];
                }
            }

            // Sort arrays alphabetically
            usort($directories, function($a, $b) {
                return strcasecmp($a['name'], $b['name']);
            });
            
            usort($files, function($a, $b) {
                return strcasecmp($a['name'], $b['name']);
            });

            $this->logger->access('Directory listed successfully', ['path' => $path]);

            return [
                'directories' => $directories,
                'files' => $files
            ];

        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), [
                'path' => $path,
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function downloadFile(string $path, string $filename): void {
        try {
            $file_path = rtrim($path, '/') . '/' . $filename;
            $secure_path = $this->security->securePath($file_path);
            
            if ($secure_path === null || !is_file($secure_path)) {
                $this->logger->security('Invalid file download attempt', [
                    'path' => $file_path,
                    'filename' => $filename
                ]);
                throw new \Exception('Invalid file');
            }

            // Check if file exists and is readable
            if (!file_exists($secure_path) || !is_readable($secure_path)) {
                throw new \Exception('File not found or not readable');
            }

            // Get MIME type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo === false) {
                throw new \Exception('Unable to determine file type');
            }

            $mime_type = finfo_file($finfo, $secure_path);
            finfo_close($finfo);

            if ($mime_type === false) {
                throw new \Exception('Unable to determine file type');
            }

            // Set headers
            header('Content-Type: ' . $mime_type);
            header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
            header('Content-Length: ' . filesize($secure_path));
            header('X-Content-Type-Options: nosniff');
            header('Cache-Control: private, no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');

            // Log successful download
            $this->logger->download('File download started', [
                'filename' => $filename,
                'size' => filesize($secure_path),
                'mime_type' => $mime_type
            ]);

            // Read file in chunks to handle large files
            $handle = fopen($secure_path, 'rb');
            if ($handle === false) {
                throw new \Exception('Unable to open file');
            }

            while (!feof($handle)) {
                $buffer = fread($handle, 1024 * 1024); // Read 1MB at a time
                if ($buffer === false) {
                    fclose($handle);
                    throw new \Exception('Error reading file');
                }
                echo $buffer;
                flush();
            }

            fclose($handle);
            $this->logger->download('File download completed', ['filename' => $filename]);

        } catch (\Exception $e) {
            $this->logger->error('Download failed: ' . $e->getMessage(), [
                'path' => $path,
                'filename' => $filename,
                'exception' => get_class($e)
            ]);
            throw $e;
        }
    }

    public function getFileIcon(string $filename): string {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        $icons = [
            // Images
            'jpg' => 'fa-image',
            'jpeg' => 'fa-image',
            'png' => 'fa-image',
            'gif' => 'fa-image',
            'webp' => 'fa-image',
            'svg' => 'fa-image',
            'bmp' => 'fa-image',
            
            // Documents
            'pdf' => 'fa-file-pdf',
            'doc' => 'fa-file-word',
            'docx' => 'fa-file-word',
            'txt' => 'fa-file-lines',
            'rtf' => 'fa-file-lines',
            'md' => 'fa-file-lines',
            'csv' => 'fa-file-csv',
            'xls' => 'fa-file-excel',
            'xlsx' => 'fa-file-excel',
            
            // Archives
            'zip' => 'fa-file-archive',
            'rar' => 'fa-file-archive',
            '7z' => 'fa-file-archive',
            'tar' => 'fa-file-archive',
            'gz' => 'fa-file-archive',
            
            // Media
            'mp3' => 'fa-file-audio',
            'wav' => 'fa-file-audio',
            'ogg' => 'fa-file-audio',
            'mp4' => 'fa-file-video',
            'avi' => 'fa-file-video',
            'mkv' => 'fa-file-video',
            'mov' => 'fa-file-video',
            
            // Code
            'php' => 'fa-file-code',
            'js' => 'fa-file-code',
            'html' => 'fa-file-code',
            'css' => 'fa-file-code',
            'json' => 'fa-file-code',
            'xml' => 'fa-file-code'
        ];
        
        return isset($icons[$extension]) ? $icons[$extension] : 'fa-file';
    }

    private function formatFileSize(int $bytes): string {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}