<?php
require_once 'config.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Crea directory se non esistono
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}
if (!file_exists(OUTPUT_DIR)) {
    mkdir(OUTPUT_DIR, 0777, true);
}

// Controlla se è stato caricato un file
if (!isset($_FILES['image'])) {
    $max_post = ini_get('post_max_size');
    $max_upload = ini_get('upload_max_filesize');
    $msg = "Il file supera i limiti del server (upload: {$max_upload}, post: {$max_post}). Dimensione massima consentita: 4MB.";
    header('Location: index.php?error=' . urlencode($msg));
    exit;
}

if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    $errors = [
        UPLOAD_ERR_INI_SIZE   => 'Il file supera la dimensione massima consentita dal server (' . ini_get('upload_max_filesize') . '). Massimo: 4MB.',
        UPLOAD_ERR_FORM_SIZE  => 'Il file supera la dimensione massima consentita (4MB).',
        UPLOAD_ERR_PARTIAL    => 'Il file è stato caricato solo parzialmente. Riprova.',
        UPLOAD_ERR_NO_FILE    => 'Nessun file selezionato.',
        UPLOAD_ERR_NO_TMP_DIR => 'Errore server: cartella temporanea mancante.',
        UPLOAD_ERR_CANT_WRITE => 'Errore server: impossibile scrivere il file su disco.',
        UPLOAD_ERR_EXTENSION  => 'Upload bloccato da un\'estensione del server.',
    ];
    $code = $_FILES['image']['error'];
    $msg = $errors[$code] ?? "Errore sconosciuto (codice: {$code})";
    header('Location: index.php?error=' . urlencode($msg));
    exit;
}

// Controlla dimensione massima
if ($_FILES['image']['size'] > MAX_FILE_SIZE) {
    header('Location: index.php?error=' . urlencode('File troppo grande. Dimensione massima: 4MB.'));
    exit;
}

$file = $_FILES['image'];
$format = $_POST['format'] ?? 'png';
$quality = $_POST['quality'] ?? 80;

// Parametri resize
$resize_width = isset($_POST['resize_width']) ? (int)$_POST['resize_width'] : null;
$resize_height = isset($_POST['resize_height']) ? (int)$_POST['resize_height'] : null;
$original_width = isset($_POST['original_width']) ? (int)$_POST['original_width'] : null;
$original_height = isset($_POST['original_height']) ? (int)$_POST['original_height'] : null;

// Parametri crop
$enable_crop = isset($_POST['enable_crop']);
$crop_x = isset($_POST['crop_x']) ? (int)$_POST['crop_x'] : 0;
$crop_y = isset($_POST['crop_y']) ? (int)$_POST['crop_y'] : 0;
$crop_width = isset($_POST['crop_width']) ? (int)$_POST['crop_width'] : null;
$crop_height = isset($_POST['crop_height']) ? (int)$_POST['crop_height'] : null;

// Controlla tipo file
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

// Fallback per AVIF/WebP non riconosciuti dal database MIME del server
$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$ext_to_mime = [
    'avif' => 'image/avif',
    'webp' => 'image/webp',
    'jpg'  => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png'  => 'image/png'
];

// Se finfo non riconosce il tipo, usa l'estensione
if ($mime_type === 'application/octet-stream' && isset($ext_to_mime[$extension])) {
    $mime_type = $ext_to_mime[$extension];
}

$allowed_mimes = ['image/webp', 'image/avif', 'image/jpeg', 'image/png'];
$allowed_extensions = ['webp', 'avif', 'jpg', 'jpeg', 'png'];

// Verifica sia MIME che estensione per sicurezza
if (!in_array($mime_type, $allowed_mimes) || !in_array($extension, $allowed_extensions)) {
    header('Location: index.php?error=' . urlencode('Tipo di file non supportato. Formati ammessi: WebP, AVIF, JPG, PNG.'));
    exit;
}

// Genera nomi file univoci
$original_filename = basename($file['name']);
$upload_path = UPLOAD_DIR . uniqid('upload_') . '_' . $original_filename;
$output_filename = uniqid('converted_') . '.' . $format;
$output_path = OUTPUT_DIR . $output_filename;

// Sposta il file caricato
if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
    header('Location: index.php?error=' . urlencode('Errore nel salvataggio del file sul server.'));
    exit;
}

// Salva informazioni in sessione per la conversione
$_SESSION['upload_path'] = $upload_path;
$_SESSION['output_path'] = $output_path;
$_SESSION['output_filename'] = $output_filename;
$_SESSION['format'] = $format;
$_SESSION['quality'] = $quality;

// Salva parametri resize
$_SESSION['resize_width'] = $resize_width;
$_SESSION['resize_height'] = $resize_height;
$_SESSION['original_width'] = $original_width;
$_SESSION['original_height'] = $original_height;

// Salva parametri crop
$_SESSION['enable_crop'] = $enable_crop;
$_SESSION['crop_x'] = $crop_x;
$_SESSION['crop_y'] = $crop_y;
$_SESSION['crop_width'] = $crop_width;
$_SESSION['crop_height'] = $crop_height;

// Reindirizza alla pagina di conversione
header('Location: convert.php');
exit;
?>