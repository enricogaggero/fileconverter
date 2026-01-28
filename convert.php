<?php
require_once 'config.php';

session_start();

if (!isset($_SESSION['upload_path']) || !isset($_SESSION['output_path'])) {
    die('Nessun file da convertire.');
}

$upload_path = $_SESSION['upload_path'];
$output_path = $_SESSION['output_path'];
$format = $_SESSION['format'];
$quality = $_SESSION['quality'];

// Parametri resize
$resize_width = $_SESSION['resize_width'] ?? null;
$resize_height = $_SESSION['resize_height'] ?? null;
$original_width = $_SESSION['original_width'] ?? null;
$original_height = $_SESSION['original_height'] ?? null;

// Parametri crop
$enable_crop = $_SESSION['enable_crop'] ?? false;
$crop_x = $_SESSION['crop_x'] ?? 0;
$crop_y = $_SESSION['crop_y'] ?? 0;
$crop_width = $_SESSION['crop_width'] ?? null;
$crop_height = $_SESSION['crop_height'] ?? null;

// Determina tipo di immagine
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $upload_path);
finfo_close($finfo);

// Fallback per AVIF/WebP non riconosciuti dal database MIME del server
if ($mime_type === 'application/octet-stream') {
    $extension = strtolower(pathinfo($upload_path, PATHINFO_EXTENSION));
    $ext_to_mime = [
        'avif' => 'image/avif',
        'webp' => 'image/webp',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png'
    ];
    if (isset($ext_to_mime[$extension])) {
        $mime_type = $ext_to_mime[$extension];
    }
}

try {
    // Carica l'immagine in base al tipo
    switch ($mime_type) {
        case 'image/webp':
            $image = imagecreatefromwebp($upload_path);
            break;
            
        case 'image/avif':
            // PHP 8.1+ richiesto per AVIF
            if (function_exists('imagecreatefromavif')) {
                $image = imagecreatefromavif($upload_path);
            } else {
                throw new Exception('Il supporto AVIF richiede PHP 8.1+');
            }
            break;
            
        case 'image/jpeg':
            $image = imagecreatefromjpeg($upload_path);
            break;
            
        case 'image/png':
            $image = imagecreatefrompng($upload_path);
            // Mantieni trasparenza per PNG
            imagealphablending($image, false);
            imagesavealpha($image, true);
            break;
            
        default:
            throw new Exception('Formato immagine non supportato');
    }
    
    if (!$image) {
        throw new Exception('Impossibile creare immagine dalla sorgente');
    }

    // Ottieni dimensioni originali
    $src_width = imagesx($image);
    $src_height = imagesy($image);

    // STEP 1: Applica RESIZE prima
    if ($resize_width > 0 && $resize_height > 0 && ($resize_width != $original_width || $resize_height != $original_height)) {
        $resized = imagecreatetruecolor($resize_width, $resize_height);

        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
        imagefill($resized, 0, 0, $transparent);

        imagecopyresampled($resized, $image, 0, 0, 0, 0, $resize_width, $resize_height, $src_width, $src_height);

        imagedestroy($image);
        $image = $resized;
        $src_width = $resize_width;
        $src_height = $resize_height;
    }

    // STEP 2: Applica CROP sul risultato ridimensionato
    if ($enable_crop && $crop_width > 0 && $crop_height > 0) {
        $crop_x = max(0, min($crop_x, $src_width - 1));
        $crop_y = max(0, min($crop_y, $src_height - 1));
        $crop_width = min($crop_width, $src_width - $crop_x);
        $crop_height = min($crop_height, $src_height - $crop_y);

        $cropped = imagecreatetruecolor($crop_width, $crop_height);

        imagealphablending($cropped, false);
        imagesavealpha($cropped, true);
        $transparent = imagecolorallocatealpha($cropped, 0, 0, 0, 127);
        imagefill($cropped, 0, 0, $transparent);

        imagecopyresampled($cropped, $image, 0, 0, $crop_x, $crop_y, $crop_width, $crop_height, $crop_width, $crop_height);

        imagedestroy($image);
        $image = $cropped;
        $src_width = $crop_width;
        $src_height = $crop_height;
    }

    // Crea immagine finale (copia diretta, le operazioni sono gia state applicate)
    $new_image = imagecreatetruecolor($src_width, $src_height);

    // Mantieni trasparenza per PNG
    if ($format === 'png') {
        imagealphablending($new_image, false);
        imagesavealpha($new_image, true);
        $transparent = imagecolorallocatealpha($new_image, 0, 0, 0, 127);
        imagefill($new_image, 0, 0, $transparent);
    }

    imagecopyresampled($new_image, $image, 0, 0, 0, 0, $src_width, $src_height, $src_width, $src_height);
    
    // Salva nel formato richiesto
    $success = false;
    switch ($format) {
        case 'png':
            $success = imagepng($new_image, $output_path, 9); // 9 = massima compressione
            break;
            
        case 'jpg':
        case 'jpeg':
            $success = imagejpeg($new_image, $output_path, $quality);
            break;
    }
    
    // Libera memoria
    imagedestroy($image);
    imagedestroy($new_image);
    
    if (!$success) {
        throw new Exception('Errore nel salvataggio dell\'immagine convertita');
    }
    
    // Pulisci sessione
    unset($_SESSION['upload_path']);
    
    // Reindirizza al download
    header('Location: download.php?file=' . urlencode(basename($output_path)));
    exit;
    
} catch (Exception $e) {
    // Pulisci file temporanei
    if (file_exists($upload_path)) {
        unlink($upload_path);
    }
    if (file_exists($output_path)) {
        unlink($output_path);
    }
    
    die('Errore nella conversione: ' . $e->getMessage());
}
?>