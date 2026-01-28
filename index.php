<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Convertitore Immagini WebP/AVIF</title>
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
            max-width: 900px;
            margin: 30px auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        h1 { font-size: 2.5em; margin-bottom: 10px; }
        .subtitle { font-size: 1.1em; opacity: 0.9; }
        .content { padding: 40px; }

        .upload-area {
            border: 3px dashed #667eea;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            margin-bottom: 30px;
            transition: all 0.3s;
            cursor: pointer;
        }
        .upload-area:hover { background: #f8f9ff; border-color: #764ba2; }
        .upload-area.hidden { display: none; }
        .upload-area i { font-size: 60px; color: #667eea; margin-bottom: 20px; }
        .file-input { display: none; }

        .form-group { margin-bottom: 20px; }

        label { display: block; margin-bottom: 8px; font-weight: 600; color: #333; }

        select, input[type="number"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            background: white;
            transition: border 0.3s;
        }
        select:focus, input[type="number"]:focus { border-color: #667eea; outline: none; }

        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 18px;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            transition: transform 0.2s, box-shadow 0.2s;
            font-weight: 600;
        }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3); }
        .btn:active { transform: translateY(0); }

        .btn-apply {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            margin-top: 15px;
        }
        .btn-apply:hover { box-shadow: 0 10px 20px rgba(40, 167, 69, 0.3); }

        .btn-reset {
            background: #dc3545;
            margin-top: 8px;
            font-size: 14px;
            padding: 10px 20px;
        }
        .btn-reset:hover { box-shadow: 0 10px 20px rgba(220, 53, 69, 0.3); }

        .btn-secondary {
            background: #6c757d;
            margin-top: 10px;
        }
        .btn-secondary:hover { box-shadow: 0 10px 20px rgba(108, 117, 125, 0.3); }

        /* Editor */
        .editor-section { display: none; margin-bottom: 30px; }
        .editor-section.active { display: block; }

        .editor-container {
            position: relative;
            margin: 20px auto;
            background: #f0f0f0;
            border-radius: 8px;
            overflow: hidden;
            display: inline-block;
        }
        .editor-wrapper { text-align: center; }
        #editorCanvas { display: block; cursor: crosshair; }

        .crop-box {
            position: absolute;
            border: 2px dashed #667eea;
            background: rgba(102, 126, 234, 0.1);
            cursor: move;
            display: none;
        }
        .crop-box.active { display: block; }

        .crop-handle {
            position: absolute;
            width: 12px;
            height: 12px;
            background: #667eea;
            border: 2px solid white;
            border-radius: 2px;
        }
        .crop-handle.nw { top: -6px; left: -6px; cursor: nw-resize; }
        .crop-handle.ne { top: -6px; right: -6px; cursor: ne-resize; }
        .crop-handle.sw { bottom: -6px; left: -6px; cursor: sw-resize; }
        .crop-handle.se { bottom: -6px; right: -6px; cursor: se-resize; }

        .editor-controls {
            background: #f8f9ff;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }

        .control-row { display: flex; gap: 15px; margin-bottom: 15px; flex-wrap: wrap; }
        .control-group { flex: 1; min-width: 120px; }
        .control-group label { font-size: 14px; margin-bottom: 5px; }
        .control-group input[type="number"] { padding: 8px; }

        .checkbox-group { display: flex; align-items: center; gap: 10px; margin-top: 15px; }
        .checkbox-group input[type="checkbox"] { width: 20px; height: 20px; cursor: pointer; }
        .checkbox-group label { margin: 0; cursor: pointer; }

        .section-block {
            margin-top: 25px;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
        }
        .section-title {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 20px;
            font-size: 16px;
            margin: 0;
        }
        .section-block .editor-controls { margin-top: 0; border-radius: 0; }

        .image-info {
            background: #e8f4e8;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 14px;
        }
        .image-info span { font-weight: 600; color: #28a745; }

        .resize-applied-info {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 10px 15px;
            border-radius: 8px;
            margin-top: 10px;
            font-size: 14px;
            display: none;
        }
        .resize-applied-info.active { display: block; }

        .crop-presets { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 15px; }
        .preset-btn {
            padding: 6px 12px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 5px;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.2s;
        }
        .preset-btn:hover { border-color: #667eea; color: #667eea; }
        .preset-btn.active { background: #667eea; color: white; border-color: #667eea; }

        .preset-selector { display: flex; gap: 10px; margin-bottom: 10px; }
        .preset-select { flex: 1; }

        .crop-section { display: none; }
        .crop-section.active { display: block; }

        .supported-formats {
            margin-top: 30px;
            padding: 20px;
            background: #f8f9ff;
            border-radius: 10px;
        }
        .formats-list { display: flex; justify-content: center; gap: 20px; margin-top: 10px; flex-wrap: wrap; }
        .format-badge {
            background: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
            color: #667eea;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        footer { text-align: center; padding: 20px; color: #666; font-size: 0.9em; border-top: 1px solid #eee; }

        @media (max-width: 600px) {
            .container { margin: 10px; }
            .content { padding: 20px; }
            header { padding: 30px 20px; }
            h1 { font-size: 1.8em; }
            .control-row { flex-direction: column; }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-exchange-alt"></i> Convertitore Immagini</h1>
            <p class="subtitle">Converti, ridimensiona e ritaglia immagini WebP e AVIF</p>
        </header>

        <div class="content">
            <form id="uploadForm" action="upload.php" method="POST" enctype="multipart/form-data">
                <div class="upload-area" id="dropArea">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <h3>Trascina qui il tuo file</h3>
                    <p>oppure clicca per selezionare</p>
                    <input type="file" name="image" id="fileInput" class="file-input" accept=".webp,.avif,.jpg,.jpeg,.png" required>
                </div>

                <!-- Editor Section -->
                <div class="editor-section" id="editorSection">
                    <div class="image-info" id="imageInfo">
                        Dimensioni originali: <span id="originalSize">-</span>
                    </div>

                    <!-- STEP 1: Ridimensiona -->
                    <div class="section-block">
                        <h3 class="section-title"><i class="fas fa-expand-arrows-alt"></i> Step 1 - Ridimensiona</h3>
                        <div class="editor-controls">
                            <div class="form-group">
                                <label><i class="fas fa-ruler-combined"></i> Dimensioni predefinite</label>
                                <div class="preset-selector">
                                    <select id="presetCategory" class="preset-select">
                                        <option value="">-- Categoria --</option>
                                    </select>
                                    <select id="presetSize" class="preset-select" disabled>
                                        <option value="">-- Dimensione --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="control-row">
                                <div class="control-group">
                                    <label><i class="fas fa-arrows-alt-h"></i> Larghezza (px)</label>
                                    <input type="number" id="resizeWidth" name="resize_width" min="1" max="10000">
                                </div>
                                <div class="control-group">
                                    <label><i class="fas fa-arrows-alt-v"></i> Altezza (px)</label>
                                    <input type="number" id="resizeHeight" name="resize_height" min="1" max="10000">
                                </div>
                            </div>
                            <button type="button" class="btn btn-apply" id="applyResizeBtn">
                                <i class="fas fa-check"></i> Applica Resize
                            </button>
                            <div class="resize-applied-info" id="resizeAppliedInfo">
                                <i class="fas fa-check-circle"></i> Resize applicato: <span id="resizedSize">-</span>
                                <button type="button" class="btn btn-reset" id="resetResizeBtn">
                                    <i class="fas fa-undo"></i> Annulla resize
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2: Ritaglia (visibile dopo resize) -->
                    <div class="section-block crop-section" id="cropSection">
                        <h3 class="section-title"><i class="fas fa-crop-alt"></i> Step 2 - Ritaglia</h3>
                        <div class="editor-controls">
                            <div class="form-group">
                                <label><i class="fas fa-ruler-combined"></i> Dimensioni predefinite</label>
                                <div class="preset-selector">
                                    <select id="cropPresetCategory" class="preset-select">
                                        <option value="">-- Categoria --</option>
                                    </select>
                                    <select id="cropPresetSize" class="preset-select" disabled>
                                        <option value="">-- Dimensione --</option>
                                    </select>
                                </div>
                            </div>

                            <div class="crop-presets">
                                <button type="button" class="preset-btn" data-ratio="free">Libero</button>
                                <button type="button" class="preset-btn" data-ratio="1:1">1:1</button>
                                <button type="button" class="preset-btn" data-ratio="4:3">4:3</button>
                                <button type="button" class="preset-btn" data-ratio="16:9">16:9</button>
                                <button type="button" class="preset-btn" data-ratio="3:2">3:2</button>
                                <button type="button" class="preset-btn" data-ratio="2:3">2:3</button>
                            </div>
                        </div>
                    </div>

                    <!-- Canvas preview (visibile dopo resize) -->
                    <div class="crop-section" id="canvasSection">
                        <div class="editor-wrapper">
                            <div class="editor-container" id="editorContainer">
                                <canvas id="editorCanvas"></canvas>
                                <div class="crop-box" id="cropBox">
                                    <div class="crop-handle nw" data-handle="nw"></div>
                                    <div class="crop-handle ne" data-handle="ne"></div>
                                    <div class="crop-handle sw" data-handle="sw"></div>
                                    <div class="crop-handle se" data-handle="se"></div>
                                </div>
                            </div>
                        </div>

                        <div class="editor-controls">
                            <div class="control-row">
                                <div class="control-group">
                                    <label>X</label>
                                    <input type="number" id="cropX" name="crop_x" min="0" value="0">
                                </div>
                                <div class="control-group">
                                    <label>Y</label>
                                    <input type="number" id="cropY" name="crop_y" min="0" value="0">
                                </div>
                                <div class="control-group">
                                    <label>Larghezza</label>
                                    <input type="number" id="cropWidth" name="crop_width" min="1">
                                </div>
                                <div class="control-group">
                                    <label>Altezza</label>
                                    <input type="number" id="cropHeight" name="crop_height" min="1">
                                </div>
                            </div>
                            <div class="checkbox-group">
                                <input type="checkbox" id="enableCrop" name="enable_crop">
                                <label for="enableCrop"><i class="fas fa-crop-alt"></i> Applica ritaglio</label>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-secondary" id="changeFileBtn">
                        <i class="fas fa-redo"></i> Cambia immagine
                    </button>
                </div>

                <!-- Hidden fields -->
                <input type="hidden" id="originalWidth" name="original_width">
                <input type="hidden" id="originalHeight" name="original_height">

                <div class="form-group">
                    <label for="format"><i class="fas fa-file-export"></i> Converti in formato:</label>
                    <select name="format" id="format" required>
                        <option value="">Seleziona formato...</option>
                        <option value="png">PNG (alta qualita, trasparenza)</option>
                        <option value="jpg">JPG (alta compressione)</option>
                    </select>
                </div>

                <div class="form-group" id="qualityGroup">
                    <label for="quality"><i class="fas fa-sliders-h"></i> Qualita (solo JPG):</label>
                    <select name="quality" id="quality">
                        <option value="90">Alta (90%)</option>
                        <option value="80" selected>Media (80%)</option>
                        <option value="70">Bassa (70%)</option>
                    </select>
                </div>

                <button type="submit" class="btn" id="convertBtn">
                    <i class="fas fa-sync-alt"></i> Converti Immagine
                </button>
            </form>

            <div class="supported-formats">
                <h3><i class="fas fa-check-circle"></i> Formati supportati:</h3>
                <div class="formats-list">
                    <span class="format-badge">WebP</span>
                    <span class="format-badge">AVIF</span>
                    <span class="format-badge">PNG</span>
                    <span class="format-badge">JPG</span>
                </div>
            </div>
        </div>

        <footer>
            <p>Convertitore Immagini - Tutti i diritti riservati</p>
        </footer>
    </div>

    <!-- Modal Errore Bootstrap -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="errorModalLabel"><i class="fas fa-exclamation-triangle"></i> Errore</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Chiudi"></button>
                </div>
                <div class="modal-body" id="errorModalBody">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Controlla se c'è un errore dal server
            const urlParams = new URLSearchParams(window.location.search);
            const errorMsg = urlParams.get('error');
            if (errorMsg) {
                document.getElementById('errorModalBody').textContent = errorMsg;
                const modal = new bootstrap.Modal(document.getElementById('errorModal'));
                modal.show();
                // Pulisci URL
                window.history.replaceState({}, '', window.location.pathname);
            }
            const fileInput = document.getElementById('fileInput');
            const dropArea = document.getElementById('dropArea');
            const editorSection = document.getElementById('editorSection');
            const formatSelect = document.getElementById('format');
            const qualityGroup = document.getElementById('qualityGroup');
            const convertBtn = document.getElementById('convertBtn');
            const changeFileBtn = document.getElementById('changeFileBtn');
            const applyResizeBtn = document.getElementById('applyResizeBtn');
            const resetResizeBtn = document.getElementById('resetResizeBtn');
            const resizeAppliedInfo = document.getElementById('resizeAppliedInfo');
            const cropSection = document.getElementById('cropSection');
            const canvasSection = document.getElementById('canvasSection');

            const canvas = document.getElementById('editorCanvas');
            const ctx = canvas.getContext('2d');
            const cropBox = document.getElementById('cropBox');
            const editorContainer = document.getElementById('editorContainer');

            const resizeWidthInput = document.getElementById('resizeWidth');
            const resizeHeightInput = document.getElementById('resizeHeight');
            const cropXInput = document.getElementById('cropX');
            const cropYInput = document.getElementById('cropY');
            const cropWidthInput = document.getElementById('cropWidth');
            const cropHeightInput = document.getElementById('cropHeight');
            const enableCropCheckbox = document.getElementById('enableCrop');

            let originalImage = null;
            let originalWidth = 0;
            let originalHeight = 0;
            let aspectRatio = 1;
            let scale = 1;
            let presetsData = null;

            // Dimensioni di lavoro dopo resize (usate dal crop)
            let workingWidth = 0;
            let workingHeight = 0;
            let resizeApplied = false;

            // Stack undo resize
            let resizeHistory = [];

            // Preset selectors
            const presetCategory = document.getElementById('presetCategory');
            const presetSize = document.getElementById('presetSize');
            const cropPresetCategory = document.getElementById('cropPresetCategory');
            const cropPresetSize = document.getElementById('cropPresetSize');

            fetch('presets.json')
                .then(r => r.json())
                .then(data => {
                    presetsData = data;
                    [presetCategory, cropPresetCategory].forEach(sel => {
                        Object.keys(data).forEach(key => {
                            const opt = document.createElement('option');
                            opt.value = key;
                            opt.textContent = data[key].label;
                            sel.appendChild(opt);
                        });
                    });
                });

            function populateSizeDropdown(catValue, sizeSelect) {
                sizeSelect.innerHTML = '<option value="">-- Dimensione --</option>';
                if (!catValue || !presetsData[catValue]) {
                    sizeSelect.disabled = true;
                    return;
                }
                presetsData[catValue].presets.forEach((p, i) => {
                    const opt = document.createElement('option');
                    opt.value = i;
                    opt.textContent = `${p.name} (${p.width}x${p.height})`;
                    sizeSelect.appendChild(opt);
                });
                sizeSelect.disabled = false;
            }

            // Resize presets
            presetCategory.addEventListener('change', function() {
                populateSizeDropdown(this.value, presetSize);
            });
            presetSize.addEventListener('change', function() {
                const cat = presetCategory.value;
                if (!cat || this.value === '') return;
                const preset = presetsData[cat].presets[parseInt(this.value)];
                // Calcola dimensioni mantenendo le proporzioni originali
                // Usa la larghezza del preset e calcola l'altezza proporzionale
                resizeWidthInput.value = preset.width;
                resizeHeightInput.value = Math.round(preset.width / aspectRatio);
            });

            // Crop presets
            cropPresetCategory.addEventListener('change', function() {
                populateSizeDropdown(this.value, cropPresetSize);
            });
            cropPresetSize.addEventListener('change', function() {
                const cat = cropPresetCategory.value;
                if (!cat || this.value === '') return;
                const preset = presetsData[cat].presets[parseInt(this.value)];
                if (!originalImage) return;

                cropState.ratio = preset.width / preset.height;

                let cw, ch;
                if (workingWidth / workingHeight > cropState.ratio) {
                    ch = Math.min(workingHeight, preset.height);
                    cw = ch * cropState.ratio;
                } else {
                    cw = Math.min(workingWidth, preset.width);
                    ch = cw / cropState.ratio;
                }
                if (cw > workingWidth) { cw = workingWidth; ch = cw / cropState.ratio; }
                if (ch > workingHeight) { ch = workingHeight; cw = ch * cropState.ratio; }

                cropState.width = cw;
                cropState.height = ch;
                cropState.x = (workingWidth - cw) / 2;
                cropState.y = (workingHeight - ch) / 2;

                document.querySelectorAll('.preset-btn').forEach(b => b.classList.remove('active'));
                updateCropBox();
                cropBox.classList.add('active');
                enableCropCheckbox.checked = true;
            });

            // Crop state
            let cropState = {
                x: 0, y: 0, width: 0, height: 0, ratio: null,
                dragging: false, resizing: false, handle: null,
                startX: 0, startY: 0, startCrop: {}
            };

            // Ratio preset buttons
            document.querySelectorAll('.preset-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.preset-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    const ratio = this.dataset.ratio;
                    if (ratio === 'free') {
                        cropState.ratio = null;
                    } else {
                        const [w, h] = ratio.split(':').map(Number);
                        cropState.ratio = w / h;
                    }
                    if (originalImage) initCropBox();
                });
            });

            // ========== APPLY RESIZE ==========
            applyResizeBtn.addEventListener('click', function() {
                if (!originalImage) return;
                const rw = parseInt(resizeWidthInput.value) || originalWidth;
                const rh = parseInt(resizeHeightInput.value) || originalHeight;

                // Salva stato corrente nello stack undo
                resizeHistory.push({
                    width: workingWidth,
                    height: workingHeight,
                    resizeApplied: resizeApplied
                });

                workingWidth = rw;
                workingHeight = rh;
                resizeApplied = true;

                // Aggiorna info
                document.getElementById('resizedSize').textContent = `${rw} x ${rh} px`;
                resizeAppliedInfo.classList.add('active');
                updateUndoBtn();

                // Mostra sezione crop + canvas
                cropSection.classList.add('active');
                canvasSection.classList.add('active');

                // Ridisegna canvas con dimensioni ridimensionate
                drawCanvas();

                // Reset crop
                cropBox.classList.remove('active');
                enableCropCheckbox.checked = false;
                cropWidthInput.value = rw;
                cropHeightInput.value = rh;
                cropXInput.value = 0;
                cropYInput.value = 0;
            });

            // ========== UNDO RESIZE ==========
            resetResizeBtn.addEventListener('click', function() {
                if (resizeHistory.length === 0) return;

                const prev = resizeHistory.pop();

                workingWidth = prev.width;
                workingHeight = prev.height;
                resizeApplied = prev.resizeApplied;

                resizeWidthInput.value = workingWidth;
                resizeHeightInput.value = workingHeight;
                aspectRatio = workingWidth / workingHeight;

                if (!resizeApplied) {
                    // Torna allo stato iniziale
                    resizeAppliedInfo.classList.remove('active');
                    cropSection.classList.remove('active');
                    canvasSection.classList.remove('active');
                    cropBox.classList.remove('active');
                    enableCropCheckbox.checked = false;
                } else {
                    // Torna a un resize precedente
                    document.getElementById('resizedSize').textContent = `${workingWidth} x ${workingHeight} px`;
                    drawCanvas();
                    cropBox.classList.remove('active');
                    enableCropCheckbox.checked = false;
                    cropWidthInput.value = workingWidth;
                    cropHeightInput.value = workingHeight;
                    cropXInput.value = 0;
                    cropYInput.value = 0;
                }

                updateUndoBtn();
            });

            function updateUndoBtn() {
                const count = resizeHistory.length;
                resetResizeBtn.innerHTML = count > 0
                    ? `<i class="fas fa-undo"></i> Annulla resize (${count})`
                    : `<i class="fas fa-undo"></i> Annulla resize`;
            }

            // File handling
            dropArea.addEventListener('click', () => fileInput.click());

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, e => { e.preventDefault(); e.stopPropagation(); }, false);
            });
            ['dragenter', 'dragover'].forEach(eventName => {
                dropArea.addEventListener(eventName, () => {
                    dropArea.style.backgroundColor = '#f0f2ff';
                    dropArea.style.borderColor = '#764ba2';
                }, false);
            });
            ['dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, () => {
                    dropArea.style.backgroundColor = '';
                    dropArea.style.borderColor = '';
                }, false);
            });

            dropArea.addEventListener('drop', e => {
                const files = e.dataTransfer.files;
                if (files.length > 0) { fileInput.files = files; handleFile(files[0]); }
            }, false);

            fileInput.addEventListener('change', function() {
                if (this.files.length > 0) handleFile(this.files[0]);
            });

            changeFileBtn.addEventListener('click', () => {
                fileInput.value = '';
                dropArea.classList.remove('hidden');
                editorSection.classList.remove('active');
                cropSection.classList.remove('active');
                canvasSection.classList.remove('active');
                resizeAppliedInfo.classList.remove('active');
                cropBox.classList.remove('active');
                originalImage = null;
                resizeApplied = false;
                resizeHistory = [];
                updateUndoBtn();
            });

            function showError(msg) {
                document.getElementById('errorModalBody').textContent = msg;
                const modal = new bootstrap.Modal(document.getElementById('errorModal'));
                modal.show();
            }

            function handleFile(file) {
                const validTypes = ['image/webp', 'image/avif', 'image/jpeg', 'image/png'];
                if (!validTypes.includes(file.type) && !file.name.match(/\.(webp|avif|jpe?g|png)$/i)) {
                    showError('Tipo di file non supportato. Formati ammessi: WebP, AVIF, JPG, PNG.');
                    return;
                }
                if (file.size > 4 * 1024 * 1024) {
                    showError('File troppo grande. Dimensione massima: 4MB.');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = new Image();
                    img.onload = function() {
                        originalImage = img;
                        originalWidth = img.width;
                        originalHeight = img.height;
                        workingWidth = img.width;
                        workingHeight = img.height;
                        aspectRatio = img.width / img.height;
                        resizeApplied = false;
                        resizeHistory = [];
                        updateUndoBtn();

                        document.getElementById('originalSize').textContent = `${img.width} x ${img.height} px`;
                        document.getElementById('originalWidth').value = img.width;
                        document.getElementById('originalHeight').value = img.height;

                        resizeWidthInput.value = img.width;
                        resizeHeightInput.value = img.height;

                        // Nascondi crop finche non si applica il resize
                        cropSection.classList.remove('active');
                        canvasSection.classList.remove('active');
                        resizeAppliedInfo.classList.remove('active');
                        cropBox.classList.remove('active');
                        enableCropCheckbox.checked = false;

                        dropArea.classList.add('hidden');
                        editorSection.classList.add('active');

                        cropWidthInput.value = img.width;
                        cropHeightInput.value = img.height;
                        cropXInput.value = 0;
                        cropYInput.value = 0;
                    };
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }

            function drawCanvas() {
                const maxWidth = Math.min(800, window.innerWidth - 80);
                const maxHeight = 500;

                scale = Math.min(maxWidth / workingWidth, maxHeight / workingHeight, 1);

                canvas.width = workingWidth * scale;
                canvas.height = workingHeight * scale;

                ctx.drawImage(originalImage, 0, 0, canvas.width, canvas.height);

                editorContainer.style.width = canvas.width + 'px';
                editorContainer.style.height = canvas.height + 'px';
            }

            function initCropBox() {
                let cw, ch;
                if (cropState.ratio) {
                    if (workingWidth / workingHeight > cropState.ratio) {
                        ch = workingHeight;
                        cw = ch * cropState.ratio;
                    } else {
                        cw = workingWidth;
                        ch = cw / cropState.ratio;
                    }
                } else {
                    cw = workingWidth;
                    ch = workingHeight;
                }

                cropState.width = cw;
                cropState.height = ch;
                cropState.x = (workingWidth - cw) / 2;
                cropState.y = (workingHeight - ch) / 2;

                updateCropBox();
                cropBox.classList.add('active');
                enableCropCheckbox.checked = true;
            }

            function updateCropBox() {
                cropBox.style.left = (cropState.x * scale) + 'px';
                cropBox.style.top = (cropState.y * scale) + 'px';
                cropBox.style.width = (cropState.width * scale) + 'px';
                cropBox.style.height = (cropState.height * scale) + 'px';

                cropXInput.value = Math.round(cropState.x);
                cropYInput.value = Math.round(cropState.y);
                cropWidthInput.value = Math.round(cropState.width);
                cropHeightInput.value = Math.round(cropState.height);
            }

            // Crop box dragging
            cropBox.addEventListener('mousedown', function(e) {
                if (e.target.classList.contains('crop-handle')) {
                    cropState.resizing = true;
                    cropState.handle = e.target.dataset.handle;
                } else {
                    cropState.dragging = true;
                }
                cropState.startX = e.clientX;
                cropState.startY = e.clientY;
                cropState.startCrop = { ...cropState };
                e.preventDefault();
            });

            document.addEventListener('mousemove', function(e) {
                if (!cropState.dragging && !cropState.resizing) return;

                const dx = (e.clientX - cropState.startX) / scale;
                const dy = (e.clientY - cropState.startY) / scale;

                if (cropState.dragging) {
                    cropState.x = Math.max(0, Math.min(workingWidth - cropState.width, cropState.startCrop.x + dx));
                    cropState.y = Math.max(0, Math.min(workingHeight - cropState.height, cropState.startCrop.y + dy));
                } else if (cropState.resizing) {
                    const handle = cropState.handle;
                    let newX = cropState.startCrop.x;
                    let newY = cropState.startCrop.y;
                    let newW = cropState.startCrop.width;
                    let newH = cropState.startCrop.height;

                    if (handle.includes('e')) newW = Math.max(20, cropState.startCrop.width + dx);
                    if (handle.includes('w')) { newW = Math.max(20, cropState.startCrop.width - dx); newX = cropState.startCrop.x + cropState.startCrop.width - newW; }
                    if (handle.includes('s')) newH = Math.max(20, cropState.startCrop.height + dy);
                    if (handle.includes('n')) { newH = Math.max(20, cropState.startCrop.height - dy); newY = cropState.startCrop.y + cropState.startCrop.height - newH; }

                    if (cropState.ratio) {
                        if (handle === 'se' || handle === 'ne') { newH = newW / cropState.ratio; }
                        else { newW = newH * cropState.ratio; }
                    }

                    newX = Math.max(0, newX);
                    newY = Math.max(0, newY);
                    newW = Math.min(newW, workingWidth - newX);
                    newH = Math.min(newH, workingHeight - newY);

                    cropState.x = newX;
                    cropState.y = newY;
                    cropState.width = newW;
                    cropState.height = newH;
                }
                updateCropBox();
            });

            document.addEventListener('mouseup', function() {
                cropState.dragging = false;
                cropState.resizing = false;
            });

            // Manual crop input
            [cropXInput, cropYInput, cropWidthInput, cropHeightInput].forEach(input => {
                input.addEventListener('change', function() {
                    cropState.x = Math.max(0, parseInt(cropXInput.value) || 0);
                    cropState.y = Math.max(0, parseInt(cropYInput.value) || 0);
                    cropState.width = Math.max(1, parseInt(cropWidthInput.value) || 1);
                    cropState.height = Math.max(1, parseInt(cropHeightInput.value) || 1);

                    if (cropState.x + cropState.width > workingWidth) cropState.width = workingWidth - cropState.x;
                    if (cropState.y + cropState.height > workingHeight) cropState.height = workingHeight - cropState.y;

                    updateCropBox();
                    cropBox.classList.add('active');
                    enableCropCheckbox.checked = true;
                });
            });

            // Resize ratio lock
            resizeWidthInput.addEventListener('input', function() {
                if (originalImage) {
                    resizeHeightInput.value = Math.round(this.value / aspectRatio);
                }
            });
            resizeHeightInput.addEventListener('input', function() {
                if (originalImage) {
                    resizeWidthInput.value = Math.round(this.value * aspectRatio);
                }
            });

            // Quality visibility
            formatSelect.addEventListener('change', function() {
                qualityGroup.style.display = this.value === 'jpg' ? 'block' : 'none';
            });

            // Form submit
            document.getElementById('uploadForm').addEventListener('submit', function(e) {
                if (!fileInput.files.length) {
                    e.preventDefault();
                    showError('Per favore seleziona un file.');
                    return;
                }
                if (!formatSelect.value) {
                    e.preventDefault();
                    showError('Per favore seleziona un formato di output.');
                    return;
                }
                convertBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Conversione in corso...';
                convertBtn.disabled = true;
            });
        });
    </script>
</body>
</html>
