<?php
namespace SecureFileBrowser;

class Logger {
    private $logPath;
    private $dateFormat = 'Y-m-d H:i:s';
    private $logLevels = [
        'ERROR'    => 0,
        'SECURITY' => 1,
        'WARNING'  => 2,
        'INFO'     => 3,
        'DEBUG'    => 4,
        'ACCESS'   => 5,
        'DOWNLOAD' => 6
    ];
    private $maxLogSize = 10485760; // 10MB in bytes
    private $maxLogFiles = 30; // Maximum number of log files to keep

    public function __construct(string $logPath) {
        $this->logPath = rtrim($logPath, '/');
        $this->initializeLogDirectory();
    }

    private function initializeLogDirectory(): void {
        if (!file_exists($this->logPath)) {
            if (!mkdir($this->logPath, 0755, true)) {
                throw new \RuntimeException("Failed to create log directory: {$this->logPath}");
            }
        }

        if (!is_writable($this->logPath)) {
            throw new \RuntimeException("Log directory is not writable: {$this->logPath}");
        }
    }

    public function log(string $level, string $message, array $context = []): void {
        try {
            $date = date($this->dateFormat);
            $logFile = $this->getLogFilePath();

            // Prepare log entry components
            $ip = $this->getClientIP();
            $user = $this->getCurrentUser();
            $requestInfo = $this->getRequestInfo();

            // Format the log entry
            $logEntry = sprintf(
                "[%s] [%s] [%s] [%s] [%s] %s %s\n",
                $date,
                strtoupper($level),
                $ip,
                $user,
                $requestInfo,
                $this->sanitizeMessage($message),
                !empty($context) ? json_encode($context, JSON_UNESCAPED_SLASHES) : ''
            );

            // Check log rotation
            $this->rotateLogIfNeeded($logFile);

            // Write log entry with exclusive lock
            if (file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX) === false) {
                throw new \RuntimeException("Failed to write to log file: $logFile");
            }

        } catch (\Exception $e) {
            // If logging fails, try to write to PHP error log
            error_log("Logging failed: " . $e->getMessage());
        }
    }

    private function getLogFilePath(): string {
        return $this->logPath . '/filebrowser-' . date('Y-m-d') . '.log';
    }

    private function rotateLogIfNeeded(string $logFile): void {
        if (file_exists($logFile) && filesize($logFile) > $this->maxLogSize) {
            $rotatedFile = $logFile . '.' . time();
            rename($logFile, $rotatedFile);
            
            // Clean up old log files
            $this->cleanOldLogs();
        }
    }

    private function cleanOldLogs(): void {
        $files = glob($this->logPath . '/filebrowser-*.log*');
        if (count($files) > $this->maxLogFiles) {
            usort($files, function($a, $b) {
                return filemtime($a) - filemtime($b);
            });
            
            $filesToDelete = array_slice($files, 0, count($files) - $this->maxLogFiles);
            foreach ($filesToDelete as $file) {
                unlink($file);
            }
        }
    }

    private function getClientIP(): string {
        $ipKeys = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($ipKeys as $key) {
            if (isset($_SERVER[$key])) {
                $ip = filter_var($_SERVER[$key], FILTER_VALIDATE_IP);
                if ($ip !== false) {
                    return $ip;
                }
            }
        }

        return 'unknown';
    }

    private function getCurrentUser(): string {
        return isset($_SESSION['user']) ? $_SESSION['user'] : 'anonymous';
    }

    private function getRequestInfo(): string {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN';
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        return $method . ' ' . $uri;
    }

    private function sanitizeMessage(string $message): string {
        // Remove any control characters
        $message = preg_replace('/[\x00-\x1F\x7F]/u', '', $message);
        // Limit length
        return substr($message, 0, 1024);
    }

    // Public logging methods for different levels
    public function error(string $message, array $context = []): void {
        $this->log('ERROR', $message, $context);
    }

    public function security(string $message, array $context = []): void {
        $this->log('SECURITY', $message, $context);
    }

    public function warning(string $message, array $context = []): void {
        $this->log('WARNING', $message, $context);
    }

    public function info(string $message, array $context = []): void {
        $this->log('INFO', $message, $context);
    }

    public function debug(string $message, array $context = []): void {
        $this->log('DEBUG', $message, $context);
    }

    public function access(string $message, array $context = []): void {
        $this->log('ACCESS', $message, $context);
    }

    public function download(string $message, array $context = []): void {
        $this->log('DOWNLOAD', $message, $context);
    }

    // Utility methods
    public function getLogContents(string $date = null, string $level = null): array {
        $date = $date ?? date('Y-m-d');
        $logFile = $this->logPath . '/filebrowser-' . $date . '.log';
        
        if (!file_exists($logFile)) {
            return [];
        }

        $logs = [];
        $handle = fopen($logFile, 'r');
        
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if ($level && !strpos($line, "[$level]")) {
                    continue;
                }
                $logs[] = $line;
            }
            fclose($handle);
        }

        return $logs;
    }

    public function clearLogs(): void {
        $files = glob($this->logPath . '/filebrowser-*.log*');
        foreach ($files as $file) {
            unlink($file);
        }
    }

    public function setMaxLogSize(int $bytes): void {
        $this->maxLogSize = $bytes;
    }

    public function setMaxLogFiles(int $count): void {
        $this->maxLogFiles = $count;
    }
}