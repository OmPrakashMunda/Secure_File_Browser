<?php
namespace SecureFileBrowser;

class Security {
    private $config;
    
    public function __construct(array $config) {
        $this->config = $config;
    }
    
    public function initializeSession(): void {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', 1);
        ini_set('session.cookie_samesite', 'Strict');
        ini_set('session.gc_maxlifetime', $this->config['security']['session_lifetime']);
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }
    
    public function validateCSRF(): bool {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return isset($_POST['csrf_token']) && 
                   hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
        }
        return true;
    }
    
    public function securePath(string $path): ?string {
        $real_path = realpath($path);
        if ($real_path === false) {
            return null;
        }
        
        $base_path = realpath($this->config['base_path']);
        if (strpos($real_path, $base_path) !== 0) {
            return null;
        }
        
        return $real_path;
    }
}
