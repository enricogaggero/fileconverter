# Image Converter - WebP/AVIF to JPG/PNG

A web-based image converter and editor that supports WebP, AVIF, JPG, and PNG formats. Built with PHP and vanilla JavaScript.

## Features

- **Format conversion**: Convert between WebP, AVIF, JPG, and PNG
- **Resize**: Proportional resizing with preset dimensions (social media, web, display, print)
- **Crop**: Interactive crop tool with drag-and-drop, resize handles, and aspect ratio presets (1:1, 4:3, 16:9, etc.)
- **Two-step workflow**: Resize first, then crop on the resized result with full undo support
- **Configurable presets**: All dimension presets are loaded from an external `presets.json` file
- **Drag & drop upload**: Drag files directly into the browser
- **Error handling**: Bootstrap modal dialogs for user-friendly error messages
- **AVIF fallback**: Automatic MIME type detection fallback for servers with outdated MIME databases

## Requirements

- PHP 8.1+ (for AVIF support) or PHP 7.4+ (for WebP/JPG/PNG only)
- GD library with AVIF and WebP support
- Apache or Nginx web server

## Installation

1. Clone or copy the files to your web server document root:

```
fileconverter/
├── index.php
├── upload.php
├── convert.php
├── download.php
├── config.php
├── presets.json
├── .htaccess
├── .user.ini
├── uploads/       (created automatically)
└── converted/     (created automatically)
```

2. Set file permissions (Linux):

```bash
chmod 755 uploads/ converted/
```

3. For **PHP-FPM** servers, copy `.user.ini` to the project root. For **mod_php** servers, rename `_.htaccess` to `.htaccess`.

4. Verify PHP extensions are enabled:

```bash
php -m | grep -E "gd|fileinfo"
```

## Configuration

### Upload limits

Edit `.user.ini` (PHP-FPM) or `.htaccess` (mod_php):

```ini
upload_max_filesize = 4M
post_max_size = 5M
```

### Application settings

Edit `config.php`:

```php
define('MAX_FILE_SIZE', 4 * 1024 * 1024); // 4MB
define('UPLOAD_DIR', 'uploads/');
define('OUTPUT_DIR', 'converted/');
```

### Dimension presets

Edit `presets.json` to add, remove, or modify preset dimensions. Structure:

```json
{
    "category_key": {
        "label": "Category Name",
        "presets": [
            { "name": "Preset Name", "width": 1920, "height": 1080 }
        ]
    }
}
```

Categories included by default: Social Media, Web, Display, Print (300 DPI).

## Workflow

1. **Upload** an image (drag & drop or click to select)
2. **Step 1 - Resize**: Choose a preset or enter custom dimensions. Click "Applica Resize" to confirm. Use "Annulla resize" to undo (supports multiple undo levels).
3. **Step 2 - Crop**: Appears after resize. Select a preset, aspect ratio, or draw a custom crop area. Drag to reposition, use corner handles to resize.
4. **Convert**: Select output format (JPG/PNG), quality level, and click "Converti Immagine".
5. **Download**: Preview the result and download the converted file.

## License

All rights reserved.
