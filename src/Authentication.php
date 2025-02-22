<?php
namespace SecureFileBrowser;

class Authentication {
    private $config;
    private $logger;
    
    public function __construct(array $config, Logger $logger) {
        $this->config = $config;
        $this->logger = $logger;
    }
    
    public function getProtectedParentDir(string $path): ?string {
        if (empty($path)) {
            return null;
        }

        $parts = explode('/', trim($path, '/'));
        
        // Check each directory starting from root
        foreach ($parts as $part) {
            if (isset($this->config['protected_dirs'][$part])) {
                return $part; // Return the first protected parent directory found
            }
        }
        
        return null;
    }
    
    public function authenticate(string $dir, string $password): bool {
        if (!isset($this->config['protected_dirs'][$dir])) {
            $this->logger->error('Invalid directory access attempt', ['dir' => $dir]);
            throw new \Exception('Invalid directory');
        }

        if (!$this->checkBruteForce($dir)) {
            $this->logger->security('Too many login attempts', ['dir' => $dir]);
            throw new \Exception('Too many failed attempts. Please try again later.');
        }

        if (!password_verify($password, $this->config['protected_dirs'][$dir]['password'])) {
            $this->incrementFailedAttempts($dir);
            $this->logger->security('Failed login attempt', ['dir' => $dir]);
            return false;
        }

        $this->resetFailedAttempts($dir);
        $_SESSION['auth_' . $dir] = true;
        $this->logger->access('Successful login', ['dir' => $dir]);
        return true;
    }

    public function isAuthorized(string $path): bool {
            // Check if there's a protected parent directory
            $protectedParent = $this->getProtectedParentDir($path);
            
            if ($protectedParent === null) {
                return true; // No protected parent found
            }
    
            // Check if authorized for the protected parent
            return isset($_SESSION['auth_' . $protectedParent]) && 
                   $_SESSION['auth_' . $protectedParent] === true;
    }

    public function isPathProtected(string $path): ?string {
        $parts = explode('/', trim($path, '/'));
        
        foreach ($parts as $part) {
            if (isset($this->config['protected_dirs'][$part])) {
                return $part;
            }
        }
        
        return null;
    }

    private function checkBruteForce(string $dir): bool {
        if (!isset($_SESSION['login_attempts'][$dir])) {
            $_SESSION['login_attempts'][$dir] = [
                'count' => 0,
                'timestamp' => time()
            ];
            return true;
        }
        
        $attempts = &$_SESSION['login_attempts'][$dir];
        
        if (time() - $attempts['timestamp'] > $this->config['security']['lockout_time']) {
            $attempts['count'] = 0;
            $attempts['timestamp'] = time();
            return true;
        }
        
        return $attempts['count'] < $this->config['security']['max_failed_attempts'];
    }

    private function incrementFailedAttempts(string $dir): void {
        if (!isset($_SESSION['login_attempts'][$dir])) {
            $_SESSION['login_attempts'][$dir] = [
                'count' => 0,
                'timestamp' => time()
            ];
        }
        $_SESSION['login_attempts'][$dir]['count']++;
    }

    private function resetFailedAttempts(string $dir): void {
        $_SESSION['login_attempts'][$dir] = [
            'count' => 0,
            'timestamp' => time()
        ];
    }
}