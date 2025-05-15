# Livelihood Monitoring System

A web-based system for monitoring and managing livelihood programs and cases.

## Deployment Instructions for DigitalOcean

### Prerequisites
- A DigitalOcean account
- A domain name pointed to your DigitalOcean droplet
- SSH access to your droplet

### Deployment Steps

1. **Create a DigitalOcean Droplet**
   - Choose Ubuntu 20.04 LTS
   - Select a plan based on your needs (2GB RAM minimum recommended)
   - Choose a datacenter region closest to your users
   - Add your SSH key
   - Create the droplet

2. **Initial Server Setup**
   ```bash
   # Connect to your droplet
   ssh root@your-droplet-ip

   # Update system
   apt update && apt upgrade -y

   # Create a non-root user
   adduser deploy
   usermod -aG sudo deploy
   ```

3. **Deploy the Application**
   ```bash
   # Clone the repository
   git clone https://your-repository-url.git /var/www/html/LivelihoodMonitoringSystem

   # Make deploy script executable
   chmod +x deploy.sh

   # Run the deployment script
   ./deploy.sh
   ```

4. **Configure Environment Variables**
   - Create a `.env` file in the project root with the following variables:
     ```
     DB_HOST=localhost
     DB_USERNAME=your_db_user
     DB_PASSWORD=your_secure_password
     DB_NAME=livelihood_monitoring
     APP_ENV=production
     APP_DEBUG=false
     APP_URL=https://your-domain.com
     SESSION_SECURE=true
     COOKIE_SECURE=true
     ```

5. **Database Setup**
   ```bash
   # Access MySQL
   mysql -u root -p

   # Create database and user
   CREATE DATABASE livelihood_monitoring;
   CREATE USER 'your_db_user'@'localhost' IDENTIFIED BY 'your_secure_password';
   GRANT ALL PRIVILEGES ON livelihood_monitoring.* TO 'your_db_user'@'localhost';
   FLUSH PRIVILEGES;
   ```

6. **Import Database Schema**
   ```bash
   mysql -u your_db_user -p livelihood_monitoring < sql/schema.sql
   ```

7. **SSL Certificate**
   - The deployment script will automatically install and configure SSL using Let's Encrypt
   - Make sure your domain is properly pointed to your droplet's IP address

### Security Considerations

1. **File Permissions**
   - The deployment script sets appropriate permissions
   - Uploads directory is set to 775
   - Other directories are set to 755

2. **Security Headers**
   - X-Frame-Options
   - X-XSS-Protection
   - X-Content-Type-Options
   - Strict-Transport-Security

3. **Session Security**
   - HTTP-only cookies
   - Secure cookies
   - SameSite cookie policy

### Maintenance

1. **Backup Database**
   ```bash
   mysqldump -u your_db_user -p livelihood_monitoring > backup.sql
   ```

2. **Update Application**
   ```bash
   cd /var/www/html/LivelihoodMonitoringSystem
   git pull
   ```

3. **Monitor Logs**
   ```bash
   tail -f /var/log/nginx/error.log
   tail -f /var/log/php8.1-fpm.log
   ```

### Troubleshooting

1. **Check Service Status**
   ```bash
   systemctl status nginx
   systemctl status php8.1-fpm
   systemctl status mysql
   ```

2. **Check Error Logs**
   ```bash
   tail -f /var/log/nginx/error.log
   tail -f /var/log/php8.1-fpm.log
   ```

3. **Common Issues**
   - Permission issues: Check file ownership and permissions
   - Database connection: Verify database credentials and connection
   - SSL issues: Check certificate validity and configuration

For additional support or questions, please contact the system administrator. 