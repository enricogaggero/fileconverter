<?php
require_once 'config.php';

session_start();

$filename = isset($_GET['file']) ? basename($_GET['file']) : '';
$filepath = OUTPUT_DIR . $filename;

// Download diretto se richiesto via parametro
if (isset($_GET['dl']) && $filename && file_exists($filepath)) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $filepath);
    finfo_close($finfo);

    header('Content-Type: ' . $mime_type);
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . filesize($filepath));
    header('Cache-Control: no-cache, no-store, must-revalidate');
    readfile($filepath);
    exit;
}

// Verifica file
if (!$filename || !file_exists($filepath)) {
    header('Location: index.php');
    exit;
}

// Info file
$file_size = filesize($filepath);
$file_ext = strtoupper(pathinfo($filename, PATHINFO_EXTENSION));
list($img_width, $img_height) = getimagesize($filepath);

function formatSize($bytes) {
    if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
    if ($bytes >= 1024) return round($bytes / 1024, 2) . ' KB';
    return $bytes . ' B';
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conversione completata</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 30px auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .success-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        .success-header i { font-size: 50px; margin-bottom: 15px; }
        .success-header h1 { font-size: 2em; }

        .content { padding: 40px; }

        .preview-area {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin-bottom: 25px;
        }

        .preview-area img {
            max-width: 100%;
            max-height: 400px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .file-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .detail-card {
            background: #f8f9ff;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }

        .detail-card .label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .detail-card .value {
            font-size: 1.3em;
            font-weight: 700;
            color: #333;
            margin-top: 5px;
        }

        .btn {
            display: block;
            width: 100%;
            padding: 15px 30px;
            font-size: 18px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            border: none;
            text-align: center;
            text-decoration: none;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }

        .btn-download {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            margin-bottom: 10px;
        }

        .btn-new {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 0.9em;
            border-top: 1px solid #eee;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="success-header">
            <i class="fas fa-check-circle"></i>
            <h1>Conversione completata!</h1>
        </div>

        <div class="content">
            <div class="preview-area">
                <img src="<?php echo htmlspecialchars(OUTPUT_DIR . $filename); ?>" alt="Immagine convertita">
            </div>

            <div class="file-details">
                <div class="detail-card">
                    <div class="label">Formato</div>
                    <div class="value"><?php echo $file_ext; ?></div>
                </div>
                <div class="detail-card">
                    <div class="label">Dimensioni</div>
                    <div class="value"><?php echo $img_width; ?> x <?php echo $img_height; ?></div>
                </div>
                <div class="detail-card">
                    <div class="label">Peso</div>
                    <div class="value"><?php echo formatSize($file_size); ?></div>
                </div>
            </div>

            <a href="download.php?file=<?php echo urlencode($filename); ?>&dl=1" class="btn btn-download">
                <i class="fas fa-download"></i> Scarica immagine
            </a>
            <a href="index.php" class="btn btn-new">
                <i class="fas fa-plus"></i> Converti un'altra immagine
            </a>
        </div>

        <footer>
            <p>Convertitore Immagini - Tutti i diritti riservati</p>
        </footer>
    </div>
</body>
</html>
