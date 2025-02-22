<?php
// Ensure $current_dir is defined
$current_dir = isset($_GET['path']) ? trim($_GET['path'], '/') : '';
$path_parts = explode('/', $current_dir);
$parent_dir = $path_parts[0];
// Check if the directory exists in protected_dirs
$dir_config = $config['protected_dirs'][$current_dir] ?? null;

// If no valid directory config, set a default description
$description = $dir_config['description'] ?? 'Protected Directory';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Protected Directory</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --bg-primary: #0a0b0e;
            --bg-secondary: #12151a;
            --text-primary: #e2e8f0;
            --text-secondary: #94a3b8;
            --accent: #2563eb;
            --accent-dark: #1d4ed8;
            --hover: #1e2837;
            --border: #1e293b;
            --error: #ef4444;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --neon-shadow: 0 0 10px rgba(37, 99, 235, 0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
            padding: 2rem;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(37, 99, 235, 0.05) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(37, 99, 235, 0.05) 0%, transparent 40%);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-form {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            background-color: var(--bg-secondary);
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            border: 1px solid var(--border);
            position: relative;
            overflow: hidden;
        }

        .login-form::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(to right, var(--accent), var(--accent-dark));
        }

        .login-form h2 {
            margin-bottom: 1.5rem;
            color: var(--text-primary);
            text-align: center;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border);
            background-color: var(--bg-primary);
            color: var(--text-primary);
            border-radius: 8px;
            transition: all 0.2s;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: var(--neon-shadow);
        }

        .submit-btn {
            width: 100%;
            padding: 0.75rem;
            background-color: var(--accent);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.2s;
display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .submit-btn:hover {
            background-color: var(--accent-dark);
            transform: translateY(-1px);
            box-shadow: var(--neon-shadow);
        }

        .error-message {
            background-color: rgba(239, 68, 68, 0.1);
            border-left: 4px solid var(--error);
            color: var(--error);
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
        }

        .error-message i {
            font-size: 1.2rem;
        }

        .back-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
            color: var(--text-secondary);
            text-decoration: none;
            padding: 0.75rem;
            border-radius: 8px;
            transition: all 0.2s;
            background-color: var(--bg-primary);
            border: 1px solid var(--border);
        }

        .back-link:hover {
            color: var(--text-primary);
            background-color: var(--hover);
            border-color: var(--accent);
            transform: translateY(-1px);
            box-shadow: var(--neon-shadow);
        }

        /* Loading animation */
        @keyframes glowing {
            0% { box-shadow: 0 0 5px var(--accent); }
            50% { box-shadow: 0 0 20px var(--accent); }
            100% { box-shadow: 0 0 5px var(--accent); }
        }

        .submit-btn.loading {
            animation: glowing 1.5s infinite;
            opacity: 0.8;
            cursor: wait;
        }

        /* Password input wrapper */
        .password-wrapper {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            cursor: pointer;
            transition: color 0.2s;
        }

        .toggle-password:hover {
            color: var(--text-primary);
        }
    </style>
</head>
<body>
    <div class="login-form">
        <h2>
            <i class="fas fa-lock" style="color: var(--accent); margin-right: 0.5rem;"></i>
            <?php echo htmlspecialchars($description); ?>
        </h2>
        
        <?php if (isset($error)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" autocomplete="off" id="loginForm">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            
            <div class="form-group">
                <label for="password">
                    <i class="fas fa-key" style="color: var(--accent); margin-right: 0.5rem;"></i>
                    Password
                </label>
                <div class="password-wrapper">
                    <input type="password" 
                           id="password" 
                           name="password" 
                           required 
                           autofocus
                           placeholder="Enter password"
                           class="<?php echo isset($error) ? 'error' : ''; ?>">
                    <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                </div>
            </div>

            <input type="hidden" name="dir" value="<?php echo htmlspecialchars($parent_dir); ?>">
            
            <button type="submit" class="submit-btn" id="submitBtn">
                <i class="fas fa-unlock"></i>
                <span>Access Directory</span>
            </button>
        </form>

        <a href="?path=" class="back-link">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Home</span>
        </a>
    </div>

    <script>
        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });

        // Add loading state to form submission
        const form = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');
        
        form.addEventListener('submit', function() {
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
            
            // Change button text
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Verifying...</span>';
        });
    </script>
</body>
</html>