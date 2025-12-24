# ODTS Deployment Guide

## InfinityFree Hosting Deployment

### Step 1: Prepare Your Files
1. Download all project files from the repository
2. Make sure you have all the files listed below

### Step 2: Upload to InfinityFree
1. Login to your InfinityFree control panel (https://infinityfree.net)
2. Go to File Manager or use FTP (FileZilla recommended)
3. Navigate to `htdocs` folder
4. Upload all files maintaining the folder structure:
   ```
   htdocs/
   ├── config/
   ├── includes/
   ├── install/
   ├── assets/
   ├── All PHP files (.php)
   ├── .htaccess
   └── README.md
   ```

### Step 3: Create MySQL Database
1. In InfinityFree control panel, go to "MySQL Databases"
2. Create a new database
3. Note down:
   - Database name (e.g., epiz_12345678_odts)
   - Database username (e.g., epiz_12345678)
   - Database password
   - Database host (usually sql301.infinityfree.com or similar)

### Step 4: Run Installation
1. Open your browser and navigate to: `http://yoursite.infinityfreeapp.com/install/index.php`
2. Enter your database credentials:
   - Database Host: (from step 3)
   - Database User: (from step 3)
   - Database Password: (from step 3)
   - Database Name: (from step 3)
   - Site URL: http://yoursite.infinityfreeapp.com
3. Click "Start Installation"
4. Wait for tables to be created

### Step 5: Create Admin Account
1. After database setup, you'll be prompted to create an admin account
2. Fill in:
   - Username
   - Password (strong password recommended)
   - Full Name
   - Email
3. Click "Create Admin Account"

### Step 6: Login
1. You'll be redirected to login page
2. Login with your admin credentials
3. Start using ODTS!

## Important Notes for InfinityFree

### DO NOT USE session_start()
- This system uses COOKIE-based authentication
- session_start() causes 500 errors on InfinityFree
- All authentication is handled through cookies

### File Permissions
- Make sure config/config.php is writable (644 or 666)
- After installation, you can set it to 444 for security

### PHP Version
- InfinityFree supports PHP 7.4+
- This system is compatible with PHP 7.4 and above

### Database Charset
- System uses UTF-8 (utf8mb4) for Turkish character support
- Make sure your database supports utf8mb4

## Troubleshooting

### 500 Internal Server Error
1. Check .htaccess file
2. Make sure no session_start() is used
3. Check file permissions

### Database Connection Error
1. Verify database credentials
2. Check if database host is correct
3. Make sure database exists

### Turkish Characters Not Showing
1. Make sure database charset is utf8mb4
2. Check that files are saved as UTF-8
3. Verify Content-Type headers

### Cannot Login
1. Clear browser cookies
2. Check if cookies are enabled
3. Verify admin account was created successfully

## Post-Installation

### Security Recommendations
1. Change admin password after first login
2. Create additional users with appropriate roles
3. Regularly backup your database
4. Keep track of all admin activities

### Backup
1. Regularly backup your database from InfinityFree control panel
2. Download database exports periodically
3. Keep a local copy of your files

### Custom Domain
1. If using custom domain (gulayazim.gt.tc), update Site URL in settings
2. Update cookie_domain in config/config.php if needed

## Support

For issues or questions:
- Check the README.md file
- Review the troubleshooting section
- Check InfinityFree forums for hosting-specific issues
- Contact the developer

## Version
- ODTS Version: 1.0.0
- Release Date: 2025-12-24
- Compatible with: InfinityFree, PHP 7.4+, MySQL 5.7+
