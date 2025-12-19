# Brandwoods WordPress - Installation Guide

Quick deployment guide for Brandwoods WordPress site.

## Server Requirements

- **Web Server**: Apache 2.4+ or Nginx
- **PHP**: 7.4+ (8.0+ recommended)
- **MySQL/MariaDB**: 5.7+ or MariaDB 10.3+
- **PHP Extensions**: mysqli, curl, gd/imagick, mbstring, xml, zip, openssl
- **Access**: FTP/SFTP or cPanel File Manager

## Installation Steps

1. Upload source code to web root
2. Extract `uploads.zip` to `wp-content/uploads/`
3. Check your file permission.
4. Create database and user & 
5. import sample database Update `siteurl` and `home` in `wp_options` table
6. Update Security Keys
## Detailed Steps

### Step 1: Upload Files
Upload all WordPress files to web root (`public_html/` or `/var/www/html/`) via FTP/SFTP or cPanel File Manager.

### Step 2: Extract Media Files
Extract `uploads.zip` to `wp-content/uploads/` directory. Can be done via File Manager (Extract option) or extract locally and upload via FTP.

### Step 3: Check File Permissions
Standard permissions: Folders `755`, Files `644`. Most hosting handles this automatically.

## Database Configuration

### Step 4: Create Database
Create MySQL database and user via cPanel MySQL Databases or phpMyAdmin:
- Database name: `brandwoods_db`
Edit `wp-config.php` and update database credentials:

```php
define('DB_NAME', 'brandwoods_db');
define('DB_USER', 'brandwoods_user');
define('DB_PASSWORD', 'your_password');
define('DB_HOST', 'localhost');  // Usually 'localhost' or '127.0.0.1'
```
### Step 5: Import Database brandwood-db.sql

Go to the `wp_options` table, find the `siteurl` and `home` keys, and replace with `yourdomain.com`.

### Step 6: Update Security Keys

Generate new keys from https://api.wordpress.org/secret-key/1.1/salt/ and replace in `wp-config.php`

After completing all steps:

1. **Visit your website**: http://yourdomain.com or https://yourdomain.com
2. **Access WordPress Admin**: http://yourdomain.com/wp-admin
3. Log in with your existing WordPress credentials

If you don't know the admin credentials, you can reset them from phpMyAdmin or create a new admin user.

