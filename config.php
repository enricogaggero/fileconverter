<?php
// Configurazione
define('MAX_FILE_SIZE', 4 * 1024 * 1024); // 4MB
define('ALLOWED_TYPES', ['webp', 'avif', 'jpg', 'jpeg', 'png']);
define('UPLOAD_DIR', 'uploads/');
define('OUTPUT_DIR', 'converted/');

// Abilita error reporting per debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Controlla estensioni PHP necessarie
function checkExtensions() {
    $required = ['gd', 'fileinfo'];
    $missing = [];
    
    foreach ($required as $ext) {
        if (!extension_loaded($ext)) {
            $missing[] = $ext;
        }
    }
    
    if (!empty($missing)) {
        die("Estensioni PHP mancanti: " . implode(', ', $missing));
    }
}

checkExtensions();
?>