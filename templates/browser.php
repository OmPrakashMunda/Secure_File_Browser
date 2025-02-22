<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Browser</title>
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
            --success: #10b981;
            --warning: #f59e0b;
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
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: var(--bg-secondary);
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            border: 1px solid var(--border);
        }

        .header {
            padding: 1.5rem;
            background-color: var(--bg-primary);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .header h1 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .breadcrumb {
            padding: 1rem 1.5rem;
            background-color: var(--bg-secondary);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .breadcrumb a {
            color: var(--text-secondary);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            transition: all 0.2s;
            font-size: 0.9rem;
        }

        .breadcrumb a:hover {
            color: var(--text-primary);
            background-color: var(--hover);
            box-shadow: var(--neon-shadow);
        }

        .items {
            padding: 1rem;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .item {
            background-color: var(--bg-primary);
            border: 1px solid var(--border);
            border-radius: 12px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .item:hover {
            transform: translateY(-4px);
            box-shadow: var(--neon-shadow);
            border-color: var(--accent);
        }

        .item.directory {
            cursor: pointer;
        }

        .item.directory .item-link {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1;
        }

.item.image-file {
    aspect-ratio: 1;
    position: relative;
    padding: 0;
    background-color: var(--bg-primary);
    overflow: hidden;
}

.item.image-file img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.item.image-file:hover img {
    transform: scale(1.05);
}

.image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    padding: 1rem;
    background: linear-gradient(to bottom, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.4) 50%, transparent 100%);
    color: white;
    z-index: 2;
}

.image-file .item-name {
    font-size: 0.9rem;
    font-weight: 500;
    color: white;
    margin-right: 2rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    text-shadow: 0 1px 2px rgba(0,0,0,0.5);
}

.image-file .item-meta {
    font-size: 0.8rem;
    opacity: 0.8;
    margin-top: 0.25rem;
}

.image-download-btn {
    position: absolute;
    top: 0.75rem;
    right: 0.75rem;
    z-index: 3;
    width: 32px;
    height: 32px;
    background-color: rgba(37, 99, 235, 0.9);
    color: white;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    text-decoration: none;
}

.image-download-btn:hover {
    background-color: var(--accent);
    transform: translateY(-2px);
    box-shadow: var(--neon-shadow);
}

        .item-inner {
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .item.has-thumbnail .item-inner {
            border-top: 1px solid var(--border);
            padding: 1rem;
        }

        .item-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--hover);
            border-radius: 10px;
            font-size: 1.2rem;
        }

        .folder-icon {
            color: var(--warning);
        }

        .file-icon {
            color: var(--accent);
        }

        .item-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .item-name {
            color: var(--text-primary);
            font-weight: 500;
            word-break: break-word;
            font-size: 0.95rem;
        }

        .item-meta {
            display: flex;
            align-items: center;
            gap: 1rem;
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .download-btn {
            background-color: var(--accent);
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            justify-content: center;
            position: relative;
            z-index: 2;
            margin-top: 0.5rem;
        }

        .download-btn:hover {
            background-color: var(--accent-dark);
            transform: translateY(-1px);
            box-shadow: var(--neon-shadow);
        }

        .empty-folder {
            grid-column: 1 / -1;
            padding: 4rem 2rem;
            text-align: center;
            color: var(--text-secondary);
            background-color: var(--bg-primary);
            border-radius: 12px;
            border: 2px dashed var(--border);
        }

        .empty-folder-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }

        .empty-folder i {
            font-size: 3rem;
            color: var(--text-secondary);
        }

        .empty-folder p {
            font-size: 1.1rem;
            font-weight: 500;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .items {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <i class="fas fa-folder-tree" style="color: var(--accent); font-size: 1.5rem;"></i>
            <h1>File Browser</h1>
        </div>

        <div class="breadcrumb">
            <a href="?path=">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>
            <?php
            if ($relative_path) {
                $parts = explode('/', $relative_path);
                $built_path = '';
                foreach ($parts as $part) {
                    $built_path .= ($built_path ? '/' : '') . $part;
                    echo ' / ';
                    echo '<a href="?path=' . htmlspecialchars(urlencode($built_path)) . '">';
                    echo '<i class="fas fa-folder"></i> ';
                    echo htmlspecialchars($part);
                    echo '</a>';
                }
            }
            ?>
        </div>

        <div class="items">
            <?php if (empty($items['directories']) && empty($items['files'])): ?>
                <div class="empty-folder">
                    <div class="empty-folder-content">
                        <i class="fas fa-folder-open"></i>
                        <p>This folder is empty</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($items['directories'] as $directory): ?>
                <div class="item directory">
                    <a class="item-link" href="?path=<?php echo htmlspecialchars(urlencode(($relative_path ? $relative_path . '/' : '') . $directory['name'])); ?>"></a>
                    <div class="item-inner">
                        <div class="item-icon">
                            <i class="fas fa-folder folder-icon"></i>
                        </div>
                        <div class="item-content">
                            <span class="item-name" title="<?php echo htmlspecialchars($directory['name']); ?>">
                                <?php echo htmlspecialchars($directory['name']); ?>
                            </span>
                            <div class="item-meta">
                                <span>Directory</span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <?php foreach ($items['files'] as $file): ?>
                    <?php $isImage = $fileManager->isImage($file['name']); ?>
                    <?php if ($isImage): ?>
                        <div class="item image-file">
                            <img data-src="?path=<?php echo htmlspecialchars(urlencode($relative_path)); ?>&preview=<?php echo htmlspecialchars(urlencode($file['name'])); ?>" 
                                 alt="<?php echo htmlspecialchars($file['name']); ?>"
                                 loading="lazy">
                            <div class="preloader">
                                <div class="spinner"></div>
                            </div>
                            <a href="?path=<?php echo htmlspecialchars(urlencode($relative_path)); ?>&download=<?php echo htmlspecialchars(urlencode($file['name'])); ?>" 
                               class="image-download-btn" title="Download <?php echo htmlspecialchars($file['name']); ?>">
                                <i class="fas fa-download"></i>
                            </a>
                            <div class="image-overlay">
                                <div class="item-name" title="<?php echo htmlspecialchars($file['name']); ?>">
                                    <?php echo htmlspecialchars($file['name']); ?>
                                </div>
                                <div class="item-meta">
                                    <?php echo htmlspecialchars($file['size']); ?>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="item regular-file">
                            <div class="item-inner">
                                <div class="item-icon">
                                    <i class="fas <?php echo $fileManager->getFileIcon($file['name']); ?> file-icon"></i></div>
                                <div class="item-content">
                                    <span class="item-name" title="<?php echo htmlspecialchars($file['name']); ?>">
                                        <?php echo htmlspecialchars($file['name']); ?>
                                    </span>
                                    <div class="item-meta">
                                        <span><?php echo htmlspecialchars($file['size']); ?></span>
                                    </div>
                                    <a href="?path=<?php echo htmlspecialchars(urlencode($relative_path)); ?>&download=<?php echo htmlspecialchars(urlencode($file['name'])); ?>" 
                                       class="download-btn">
                                        <i class="fas fa-download"></i>
                                        <span>Download</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // Intersection Observer for lazy loading
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        loadImage(img);
                        observer.unobserve(img);
                    }
                }
            });
        }, {
            rootMargin: '50px 0px',
            threshold: 0.1
        });

        // Function to load image
        function loadImage(img) {
            const container = img.closest('.image-file');
            const preloader = container.querySelector('.preloader');

            // Start loading the image
            img.src = img.dataset.src;
            
            img.onload = () => {
                img.classList.add('loaded');
                preloader.style.opacity = '0';
                setTimeout(() => {
                    preloader.style.display = 'none';
                }, 300);
            };

            img.onerror = () => {
                preloader.style.display = 'none';
                container.style.backgroundColor = 'var(--bg-primary)';
                // Add an error icon or message if needed
                const errorIcon = document.createElement('div');
                errorIcon.className = 'error-icon';
                errorIcon.innerHTML = '<i class="fas fa-image" style="color: var(--text-secondary); font-size: 2rem;"></i>';
                errorIcon.style.position = 'absolute';
                errorIcon.style.top = '50%';
                errorIcon.style.left = '50%';
                errorIcon.style.transform = 'translate(-50%, -50%)';
                container.appendChild(errorIcon);
            };
        }

        // Initialize lazy loading for all images
        const images = document.querySelectorAll('img[data-src]');
        images.forEach(img => imageObserver.observe(img));
    });
    </script>
</body>
</html>