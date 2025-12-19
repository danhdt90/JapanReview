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
3. Check file permissions (755 for folders, 644 for files)
4. Create database and user
5. Import sample database and update `siteurl` and `home` in `wp_options` table
6. Update security keys in `wp-config.php`
7. Save permalinks to regenerate `.htaccess`
8. Activate required plugins (ACF, Article Bulk Importer, Pods)
9. Import plugin configurations (Pods & ACF)
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

### Step 7: Update Permalinks

Go to WordPress Admin > Settings > Permalinks and click "Save Changes" to regenerate `.htaccess` file with proper rewrite rules.

### Step 8: Activate Required Plugins

Activate the following plugins from WordPress Admin > Plugins:
- **Advanced Custom Fields** (ACF)
- **Article Bulk Importer**
- **Pods - Custom Content Types and Fields**

### Step 9: Import Plugin Configurations

Import configuration files for plugins:

**Pods Configuration:**
1. Go to Pods Admin > Components
2. Enable "Migrate: Packages" component if not already enabled
3. Go to Pods Admin > Migrate Packages > Import/Export
4. Choose file `pods-package-2025.json`
5. Click "Import" to load configuration

**ACF Configuration:**
1. Go to Custom Fields > Tools
2. Click "Import Field Groups"
3. Choose file `acf-export.json`
4. Click "Import" to load field group configuration

## Accessing Your Site

After completing all steps:

1. **Visit your website**: http://yourdomain.com or https://yourdomain.com
2. **Access WordPress Admin**: http://yourdomain.com/wp-admin
3. Log in with your existing WordPress credentials

If you don't know the admin credentials, you can reset them from phpMyAdmin or create a new admin user.

