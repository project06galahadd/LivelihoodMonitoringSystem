<?php
require_once __DIR__ . '/../config/sidebar_config.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $page_data = [
        'title' => $_POST['title'],
        'icon' => $_POST['icon'],
        'url' => $_POST['url'],
        'role' => $_POST['role'],
        'section' => $_POST['section'],
        'is_submenu' => isset($_POST['is_submenu']) ? true : false,
        'parent_section' => $_POST['parent_section'] ?? null
    ];

    // Generate PHP file
    $php_content = <<<EOT
<?php
\$page_title = '{$page_data['title']}';
include __DIR__ . '/../includes/base_template.php';
?>

<!-- Your page content here -->

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <?php echo \$page_title; ?>
        </h3>
    </div>
    <div class="card-body">
        <!-- Add your content here -->
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
EOT;

    // Create directory if it doesn't exist
    $dir = dirname($page_data['url']);
    if (!empty($dir) && !file_exists($dir)) {
        mkdir($dir, 0777, true);
    }

    // Write PHP file
    file_put_contents($page_data['url'], $php_content);

    // Update sidebar configuration
    $config = require __DIR__ . '/../config/sidebar_config.php';
    
    if ($page_data['is_submenu']) {
        // Add to parent section's submenu
        if (isset($config[$page_data['role']][$page_data['parent_section']]['submenu'])) {
            $config[$page_data['role']][$page_data['parent_section']]['submenu'][] = [
                'title' => $page_data['title'],
                'url' => basename($page_data['url']),
                'role' => [$page_data['role']]
            ];
        }
    } else {
        // Add as main menu item
        $config[$page_data['role']][$page_data['section']] = [
            'title' => $page_data['title'],
            'url' => basename($page_data['url']),
            'role' => [$page_data['role']],
            'icon' => $page_data['icon']
        ];
    }

    // Save updated configuration
    $config_content = "<?php\nreturn " . var_export($config, true) . ";";
    file_put_contents(__DIR__ . '/../config/sidebar_config.php', $config_content);

    echo "<div class='alert alert-success'>Page created successfully!</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Create New Page</h2>
        <form method="POST" class="mt-4">
            <div class="form-group">
                <label for="title">Page Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            
            <div class="form-group">
                <label for="icon">Font Awesome Icon Class</label>
                <input type="text" class="form-control" id="icon" name="icon" value="fas fa-file" required>
                <small class="form-text text-muted">Example: fas fa-file</small>
            </div>

            <div class="form-group">
                <label for="url">Page URL</label>
                <input type="text" class="form-control" id="url" name="url" required>
                <small class="form-text text-muted">Example: wp_admin/new_page.php or wp_admin/sections/new_page.php</small>
            </div>

            <div class="form-group">
                <label for="role">User Role</label>
                <select class="form-control" id="role" name="role" required>
                    <option value="ADMIN">Admin</option>
                    <option value="MEMBER">Member</option>
                </select>
            </div>

            <div class="form-group">
                <label for="section">Section</label>
                <input type="text" class="form-control" id="section" name="section" required>
                <small class="form-text text-muted">Name of the section in sidebar</small>
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="is_submenu" name="is_submenu">
                <label class="form-check-label" for="is_submenu">Is Submenu Item?</label>
            </div>

            <div class="form-group" id="parent_section_group" style="display: none;">
                <label for="parent_section">Parent Section</label>
                <input type="text" class="form-control" id="parent_section" name="parent_section">
                <small class="form-text text-muted">Only required if this is a submenu item</small>
            </div>

            <button type="submit" class="btn btn-primary">Create Page</button>
        </form>
    </div>

    <script>
    document.getElementById('is_submenu').addEventListener('change', function() {
        const parentSectionGroup = document.getElementById('parent_section_group');
        parentSectionGroup.style.display = this.checked ? 'block' : 'none';
    });
    </script>
</body>
</html>
