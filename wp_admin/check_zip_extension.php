<?php
// Check if ZipArchive is available
if (!extension_loaded('zip')) {
    echo "PHP ZipArchive extension is not enabled.\n";
    echo "To enable it, you need to:\n\n";
    echo "1. Open your php.ini file\n";
    echo "2. Find the line with ';extension=zip'\n";
    echo "3. Remove the semicolon to uncomment it: 'extension=zip'\n";
    echo "4. Save the file and restart your web server\n\n";
    
    // Try to locate php.ini
    $php_ini_path = php_ini_loaded_file();
    if ($php_ini_path) {
        echo "Your php.ini file is located at: " . $php_ini_path . "\n";
    } else {
        echo "Could not locate your php.ini file. Common locations are:\n";
        echo "- /etc/php.ini\n";
        echo "- /etc/php/[version]/apache2/php.ini\n";
        echo "- /etc/php/[version]/fpm/php.ini\n";
        echo "- /usr/local/etc/php.ini\n";
    }
    
    exit(1);
}

echo "PHP ZipArchive extension is enabled.\n";
exit(0); 