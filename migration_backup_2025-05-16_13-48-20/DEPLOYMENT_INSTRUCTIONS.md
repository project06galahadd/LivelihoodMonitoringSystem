# Hostinger Deployment Instructions

## 1. Database Setup
1. Log in to your Hostinger control panel
2. Go to MySQL Databases
3. Create a new database
4. Create a new database user
5. Assign the user to the database with all privileges
6. Note down the database name, username, and password

## 2. File Upload
1. Log in to your Hostinger File Manager
2. Navigate to public_html
3. Upload all files from the migration_files directory
4. Set proper permissions:
   - Directories: 755
   - Files: 644
   - Upload directories: 777

## 3. Configuration
1. Update config/hostinger_config.php with your Hostinger credentials:
   - DB_HOST
   - DB_USER
   - DB_PASS
   - DB_NAME
   - BASE_URL
   - UPLOAD_PATH
   - VIDEO_PATH

## 4. Database Import
1. Go to phpMyAdmin in Hostinger
2. Select your database
3. Import the database_backup.sql file

## 5. Final Steps
1. Test the application
2. Check error logs
3. Verify all functionality
4. Remove migration files

## Troubleshooting
- Check error logs in /home/username/logs/php_errors.log
- Verify file permissions
- Ensure all required PHP extensions are enabled
- Check database connection settings
