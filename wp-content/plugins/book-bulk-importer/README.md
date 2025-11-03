# Book Bulk Importer Plugin

A WordPress plugin for bulk importing books with Pods custom fields from CSV files.

## Features

- Import books from CSV files
- Support for Pods custom fields including repeatable fields
- Two formats for alternative titles (indexed columns or pipe-separated)
- Dry run validation mode
- Real-time import progress
- Update existing books option
- Comprehensive error logging

## Installation

1. Upload the plugin folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Ensure Pods plugin is installed and activated
4. Create a custom post type called 'book' with the following Pods fields:
   - `author` (Plain Text)
   - `other_title` (Plain Text - Simple Repeatable)
   - `isbn` (Plain Text)

## Usage

1. Go to **Tools > Book Bulk Importer** in your WordPress admin
2. Upload a CSV file or download the sample template
3. Choose whether to update existing books
4. Run validation first (dry run)
5. Import the books

### CSV Format

The CSV file should contain the following columns:

**Required columns:**
- `title` - Book title (required)

**Optional columns:**
- `content` - Book description/content
- `author` - Book author
- `other_title` - Alternative titles (see formats below)
- `isbn` - ISBN number
- `publication_date` - Publication date (YYYY-MM-DD format)
- `status` - Post status (publish/draft/private)

### Alternative Title Formats

**Method 1: Indexed Columns (Recommended)**
Use separate columns for each alternative title:
```csv
other_title[0].other_title,other_title[1].other_title,other_title[2].other_title
```

**Method 2: Pipe-Separated (Legacy)**
Use a single column with pipe-separated values:
```csv
other_title
"Title1|Title2|Title3"
```

### Sample CSV Content (Indexed Format)

```csv
title,content,author,other_title[0].other_title,other_title[1].other_title,other_title[2].other_title,isbn,publication_date,status
"Sample Book 1","This is the description","Author Name","Alt Title 1","Alt Title 2","Alt Title 3","9780123456789","2024-01-15","publish"
"Sample Book 2","Another description","Author 2","Single Alt Title","","","9780987654321","2024-02-20","draft"
```

### Sample CSV Content (Legacy Format)

```csv
title,content,author,other_title,isbn,publication_date,status
"Sample Book 1","This is the description","Author Name","Alt Title 1|Alt Title 2|Alt Title 3","9780123456789","2024-01-15","publish"
```

## Field Mapping

- `title` → Post title
- `content` → Post content
- `author` → Pods field: author
- `other_title` → Pods repeatable field: other_title
- `isbn` → Pods field: isbn
- `publication_date` → Post date
- `status` → Post status

## Troubleshooting

### Common Issues

1. **"Pods plugin not detected"**
   - Ensure Pods plugin is installed and activated
   - Check that the 'book' post type exists with required fields

2. **"Alternative titles not saving"**
   - Check CSV format matches expected pattern
   - Verify Pods field 'other_title' is configured as Simple Repeatable
   - Check debug logs in wp-content/book-importer-debug.log

3. **"CSV validation failed"**
   - Ensure all required columns are present
   - Check date format (YYYY-MM-DD)
   - Verify file encoding is UTF-8

### Debug Information

The plugin creates detailed logs in:
- `wp-content/book-importer-debug.log` - Import process logs
- WordPress error logs - PHP errors and exceptions

## Requirements

- WordPress 5.0+
- PHP 7.4+
- Pods plugin
- Custom post type 'book' with Pods fields

## File Structure

```
book-bulk-importer/
├── book-bulk-importer.php          # Main plugin file
├── README.md                       # This file
├── sample-books.csv               # Legacy format sample
├── sample-indexed.csv             # Indexed format sample
├── assets/
│   ├── admin.css                  # Admin styles
│   └── admin.js                   # Admin JavaScript
├── includes/
│   ├── class-admin-page.php       # Admin interface
│   ├── class-book-importer.php    # Core import logic
│   └── class-csv-importer.php     # CSV parsing
└── templates/
    └── admin-page.php             # Admin page template
```

## Changelog

### Version 1.2.0
- Added indexed column format support
- Improved CSV validation
- Enhanced error logging
- Better repeatable field handling

### Version 1.1.0
- Added dry run validation
- Improved error handling
- Added progress indicators

### Version 1.0.0
- Initial release
- Basic CSV import functionality
- Pods integration