<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

use SecureFileBrowser\Security;
use SecureFileBrowser\FileManager;
use SecureFileBrowser\Authentication;
use SecureFileBrowser\Logger;

// Load configurations
$config = require __DIR__ . '/../config/config.php';
$mime_types = require __DIR__ . '/../config/mime_types.php';

// Initialize logger
$logger = new Logger($config['log_path']);

try {
    // Initialize core components
    $security = new Security($config);
    $security->initializeSession();

    if (!$security->validateCSRF()) {
        $logger->security('CSRF validation failed', ['ip' => $_SERVER['REMOTE_ADDR']]);
        header('HTTP/1.1 403 Forbidden');
        exit('CSRF validation failed');
    }

    $auth = new Authentication($config, $logger);
    $fileManager = new FileManager($config, $security, $mime_types, $logger);

    // Get current path
    $relative_path = isset($_GET['path']) ? trim($_GET['path'], '/') : '';
    $current_path = $config['base_path'] . ($relative_path ? '/' . $relative_path : '');
    
    // Add this check for root directory
    if (empty($relative_path)) {
        $old_csrf = $_SESSION['csrf_token'] ?? null;
        session_destroy();
        session_start();
        session_regenerate_id(true);
        $_SESSION['csrf_token'] = $old_csrf;
        $logger->security('Session destroyed on home return', [
            'ip' => $_SERVER['REMOTE_ADDR']
        ]);
    }
    // Handle download requests
    if (isset($_GET['download'])) {
        try {
            $download_file = basename($_GET['download']);
            $download_path = $current_path . '/' . $download_file;

            // Verify the file is within the allowed base path
            if (!realpath($download_path) || strpos(realpath($download_path), realpath($config['base_path'])) !== 0) {
                $logger->security('Path traversal attempt', [
                    'path' => $download_path,
                    'ip' => $_SERVER['REMOTE_ADDR']
                ]);
                throw new \Exception('Invalid file path');
            }

            // Check if file exists within the allowed directory
            if (!file_exists($download_path) || !is_file($download_path)) {
                throw new \Exception('File not found');
            }

            // Check directory authorization
            $current_dir = basename(dirname($download_path));
            if (!$auth->isAuthorized($current_dir)) {
                $logger->security('Unauthorized access attempt', [
                    'directory' => $current_dir,
                    'ip' => $_SERVER['REMOTE_ADDR']
                ]);
                header('HTTP/1.1 403 Forbidden');
                exit('Access denied');
            }

            $fileManager->downloadFile(dirname($download_path), $download_file);
            exit;
        } catch (\Exception $e) {
            $logger->error('Download error: ' . $e->getMessage(), [
                'file' => $download_file,
                'path' => $download_path
            ]);
            header('HTTP/1.1 400 Bad Request');
            exit($e->getMessage());
        }
    }

    // Handle login requests
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password']) && isset($_POST['dir'])) {
        try {
            if ($auth->authenticate($_POST['dir'], $_POST['password'])) {
                $logger->access('Successful login', ['directory' => $_POST['dir']]);
                header('Location: ?path=' . urlencode($_POST['dir']));
                exit;
            } else {
                $error = 'Invalid password! Please try again.';
                $logger->security('Failed login attempt', [
                    'directory' => $_POST['dir'],
                    'ip' => $_SERVER['REMOTE_ADDR']
                ]);
            }
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $logger->error('Login error', [
                'error' => $e->getMessage(),
                'directory' => $_POST['dir']
            ]);
        }
    }
 
    // Add this to your index.php where you handle requests
    if (isset($_GET['preview'])) {
        try {
            $file_path = $current_path . '/' . $_GET['preview'];
            if (!file_exists($file_path) || !is_file($file_path)) {
                throw new \Exception('File not found');
            }
    
            $mime_type = mime_content_type($file_path);
            if (strpos($mime_type, 'image/') !== 0) {
                throw new \Exception('Not an image file');
            }
    
            header('Content-Type: ' . $mime_type);
            readfile($file_path);
            exit;
        } catch (Exception $e) {
            header('HTTP/1.1 400 Bad Request');
            exit($e->getMessage());
        }
    }
      
    $relative_path = isset($_GET['path']) ? trim($_GET['path'], '/') : '';
    $current_path = $config['base_path'] . ($relative_path ? '/' . $relative_path : '');
    
    // Get the first directory from the path
    $path_parts = explode('/', $relative_path);
    $first_dir = $path_parts[0] ?? '';
    
    // Check if the first directory is protected
    if (!empty($first_dir) && isset($config['protected_dirs'][$first_dir]) && !$auth->isAuthorized($first_dir)) {
        $current_dir = $first_dir; // Use the parent protected directory
        require __DIR__ . '/../templates/login.php';
        exit;
    }
    
    // Continue with directory listing...
    try {
        $items = $fileManager->listDirectory($current_path);
        require __DIR__ . '/../templates/browser.php';
    } catch (Exception $e) {
        header('HTTP/1.1 400 Bad Request');
        exit($e->getMessage());
    }
    
} catch (\Exception $e) {
    $logger->error('Application error: ' . $e->getMessage(), [
        'exception' => get_class($e),
        'trace' => $e->getTraceAsString()
    ]);
    header('HTTP/1.1 500 Internal Server Error');
    exit('An error occurred');
}