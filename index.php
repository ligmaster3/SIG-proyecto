<?php
require_once 'config/connection.php';
session_start();

// Generar token CSRF
$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;

// Obtener carreras para el formulario
$stmt_carreras = ejecutarConsulta("
    SELECT c.id_carrera, c.nombre_carrera, f.nombre_facultad 
    FROM carreras c
    JOIN facultades f ON c.id_facultad = f.id_facultad
    ORDER BY f.nombre_facultad, c.nombre_carrera
");
$carreras = $stmt_carreras->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión Académica</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f8f9fa;
    }

    :root {
        --primary-color: #3a0ca3;
        --primary-light: #4361ee;
        --secondary-color: #7209b7;
        --accent-color: #4cc9f0;
        --light-color: #f8f9fa;
        --dark-color: #212529;
        --border-radius: 12px;
        --box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        --transition: all 0.3s ease;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #e4e9f2 100%);
        color: #333;
        line-height: 1.6;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
    }

    .container {
        width: 100%;
        max-width: 800px;
        margin: 0 auto;
        padding: 3rem;
        background-color: #fff;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        position: relative;
        overflow: hidden;
    }

    .container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 8px;
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #eaeaea;
        margin-bottom: 2rem;
    }

    .modal-title {
        font-size: 1.8rem;
        color: var(--primary-color);
        font-weight: 600;
        position: relative;
    }

    .modal-title::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 0;
        width: 50px;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        border-radius: 2px;
    }

    .btn-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #999;
        transition: var(--transition);
    }

    .btn-close:hover {
        color: var(--dark-color);
        transform: rotate(90deg);
    }

    .modal-body {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
    }

    .full-width {
        grid-column: span 2;
    }

    .form-label {
        display: block;
        margin-bottom: 0.75rem;
        font-weight: 500;
        color: #555;
        font-size: 0.95rem;
    }

    .form-control,
    .form-select {
        width: 100%;
        padding: 0.9rem 1.2rem;
        font-size: 1rem;
        border: 2px solid #eaeaea;
        border-radius: var(--border-radius);
        background-color: #fff;
        transition: var(--transition);
    }

    .form-control:focus,
    .form-select:focus {
        outline: none;
        border-color: var(--primary-light);
        box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.15);
    }

    .input-group {
        position: relative;
        margin-bottom: 0.5rem;
    }

    .input-field {
        position: relative;
    }

    .input-icon {
        position: absolute;
        top: 50%;
        left: 1.2rem;
        transform: translateY(-50%);
        color: var(--primary-light);
        font-size: 1.2rem;
    }

    .has-icon {
        padding-left: 3rem;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        padding-top: 2rem;
        margin-top: 2rem;
        border-top: 1px solid #eaeaea;
    }

    .btn {
        padding: 0.9rem 2rem;
        font-size: 1rem;
        font-weight: 500;
        border: none;
        border-radius: var(--border-radius);
        cursor: pointer;
        transition: var(--transition);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-primary {
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        color: white;
        box-shadow: 0 4px 15px rgba(66, 99, 235, 0.2);
    }

    .btn-primary:hover {
        background: linear-gradient(90deg, var(--secondary-color), var(--primary-color));
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(66, 99, 235, 0.3);
    }

    .btn-secondary {
        background-color: #f1f3f5;
        color: #495057;
    }

    .btn-secondary:hover {
        background-color: #e9ecef;
        transform: translateY(-2px);
    }

    .form-title {
        color: var(--primary-color);
        font-size: 2.5rem;
        text-align: center;
        margin-bottom: 0.5rem;
        font-weight: 700;
    }

    .form-subtitle {
        color: #666;
        text-align: center;
        margin-bottom: 2.5rem;
        font-weight: normal;
        font-size: 1.1rem;
    }

    .highlight {
        color: var(--secondary-color);
    }

    .required-indicator {
        color: var(--secondary-color);
        margin-left: 3px;
    }

    .form-footer-text {
        text-align: center;
        margin-top: 2rem;
        color: #888;
        font-size: 0.9rem;
    }

    .alert {
        padding: 1rem;
        margin-bottom: 1.5rem;
        border-radius: var(--border-radius);
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    /* Animaciones */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .container {
        animation: fadeIn 0.6s ease-out;
    }

    .input-group {
        animation: fadeIn 0.6s ease-out;
        animation-fill-mode: both;
    }

    .input-group:nth-child(1) {
        animation-delay: 0.1s;
    }

    .input-group:nth-child(2) {
        animation-delay: 0.2s;
    }

    .input-group:nth-child(3) {
        animation-delay: 0.3s;
    }

    .input-group:nth-child(4) {
        animation-delay: 0.4s;
    }

    .input-group:nth-child(5) {
        animation-delay: 0.5s;
    }

    @media (max-width: 768px) {
        .modal-body {
            grid-template-columns: 1fr;
        }

        .full-width {
            grid-column: span 1;
        }

        .container {
            padding: 2rem 1.5rem;
        }

        .form-title {
            font-size: 2rem;
        }
    }
    </style>
</head>

<body>

    <div style="position: absolute; top: 30px; left: 30px; z-index: 1001;">
        <a href="dashboard.php" class="btn btn-secondary"
            style="padding: 0.7rem 1.5rem; font-size: 1rem; border-radius: 8px; text-decoration: none; color: #495057; background: #f1f3f5; border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.07);">
            <i class="fas fa-arrow-left"></i> Volver al Dashboard
        </a>
    </div>

    <div class="container">
        <h1 class="form-title">Sistema Académico</h1>
        <h3 class="form-subtitle">Registro de <span class="highlight">Nuevos Estudiantes</span></h3>

        <?php if (isset($_SESSION['errores']) && !empty($_SESSION['errores'])): ?>
        <div class="alert alert-danger" role="alert">
            <strong>Error:</strong>
            <ul>
                <?php foreach ($_SESSION['errores'] as $error): ?>
                <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php
            unset($_SESSION['errores']);
        endif;

        if (isset($_SESSION['exito'])): ?>
        <div class="alert alert-success" role="alert">
            <strong>Éxito!</strong> <?= htmlspecialchars($_SESSION['exito'], ENT_QUOTES, 'UTF-8'); ?>
        </div>
        <?php
            unset($_SESSION['exito']);
        endif;
        ?>

        <div class="modal-header">
            <h5 class="modal-title" id="agregarEstudianteModalLabel">Datos Personales</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar">✕</button>
        </div>

        <form id="formAgregarEstudiante" action="acciones_estudiantes.php" method="POST">
            <input type="hidden" name="accion" value="agregar">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

            <div class="modal-body">
                <div class="input-group">
                    <label for="codigo" class="form-label">ID de Estudiante</label>
                    <div class="input-group">
                        <i class="input-icon fas fa-id-card"></i>
                        <input type="text" class="form-control has-icon" id="codigo" name="codigo"
                            placeholder="Ingrese código de estudiante" required>
                    </div>
                </div>

                <div class="input-group">
                    <label for="nombre" class="form-label">Nombre <span class="required-indicator">*</span></label>
                    <div class="input-field">
                        <i class="input-icon fas fa-user"></i>
                        <input type="text" class="form-control has-icon" id="nombre" name="nombre"
                            placeholder="Ingrese su nombre" required>
                    </div>
                </div>

                <div class="input-group">
                    <label for="apellido" class="form-label">Apellido <span class="required-indicator">*</span></label>
                    <div class="input-field">
                        <i class="input-icon fas fa-user"></i>
                        <input type="text" class="form-control has-icon" id="apellido" name="apellido"
                            placeholder="Ingrese su apellido" required>
                    </div>
                </div>

                <div class="input-group">
                    <label for="genero" class="form-label">Género <span class="required-indicator">*</span></label>
                    <div class="input-field">
                        <i class="input-icon fas fa-venus-mars"></i>
                        <select class="form-select has-icon" id="genero" name="genero" required>
                            <option value="">Seleccione una opción</option>
                            <option value="Hombre">Hombre</option>
                            <option value="Mujer">Mujer</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                </div>

                <div class="input-group">
                    <label for="email" class="form-label">Email <span class="required-indicator">*</span></label>
                    <div class="input-field">
                        <i class="input-icon fas fa-envelope"></i>
                        <input type="email" class="form-control has-icon" id="email" name="email"
                            placeholder="correo@ejemplo.com" required>
                    </div>
                </div>

                <div class="input-group full-width">
                    <label for="carrera" class="form-label">Carrera <span class="required-indicator">*</span></label>
                    <div class="input-field">
                        <i class="input-icon fas fa-graduation-cap"></i>
                        <select class="form-select has-icon" id="carrera" name="carrera" required>
                            <option value="">Seleccione una carrera</option>
                            <?php foreach ($carreras as $carrera): ?>
                            <option value="<?= htmlspecialchars($carrera['id_carrera'], ENT_QUOTES, 'UTF-8'); ?>">
                                <?= htmlspecialchars($carrera['nombre_facultad'] . ' - ' . $carrera['nombre_carrera'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>


                <div class="input-group">
                    <label for="hora_entrada" class="form-label">Fecha y Hora de Entrada</label>
                    <div class="input-field">
                        <i class="input-icon fas fa-calendar-alt"></i>
                        <input type="datetime-local" class="form-control has-icon" id="fecha_entrada"
                            name="hora_entrada">
                    </div>
                </div>
                <div class="input-group">
                    <label for="hora_salida" class="form-label">Fecha y Hora de Salida</label>
                    <div class="input-field">
                        <i class="input-icon fas fa-calendar-alt"></i>
                        <input type="datetime-local" class="form-control has-icon" id="fecha_salida" name="hora_salida">
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="window.location.href='index.php'">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Registrar Estudiante
                </button>
            </div>

            <p class="form-footer-text">Los campos marcados con <span class="required-indicator">*</span> son
                obligatorios</p>
        </form>
    </div>

    <!-- Botón flotante para escanear -->
    <button id="scan-button" class="scan-button">
        <i class="fas fa-id-card"></i> Escanear Carnet
    </button>

    <!-- Modal del escáner -->
    <div id="scanner-modal" class="scanner-modal">
        <div class="scanner-content">
            <h2>Escaneando Carnet Universitario</h2>
            <div id="scanner-container">
                <video id="scanner-video"></video>
                <div class="scan-overlay"></div>
            </div>
            <div class="scanner-buttons">
                <button id="scan-barcode-btn">
                    <i class="fas fa-barcode"></i> Escanear Código
                </button>
                <button id="scan-ocr-btn">
                    <i class="fas fa-text-height"></i> Leer Texto
                </button>
                <button id="close-scanner-btn">
                    <i class="fas fa-times"></i> Cancelar
                </button>
            </div>
            <div id="scan-result"></div>
        </div>
    </div>

    <!-- Agrega esto en el head -->
    <script src="https://cdn.jsdelivr.net/npm/quagga/dist/quagga.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tesseract.js@4/dist/tesseract.min.js"></script>

    <script>
    // Validación del formulario
    document.getElementById('formAgregarEstudiante').addEventListener('submit', function(e) {
        let valid = true;
        // Validar campos obligatorios
        document.querySelectorAll('[required]').forEach(field => {
            if (!field.value.trim()) {
                valid = false;
                field.style.borderColor = 'red';
            } else {
                field.style.borderColor = '#eaeaea';
            }
        });

        if (!valid) {
            e.preventDefault();
            alert('Por favor complete todos los campos obligatorios marcados con *');
        }
    });

    // Configuración del escáner
    const scanButton = document.getElementById('scan-button');
    const scannerModal = document.getElementById('scanner-modal');
    const closeScannerBtn = document.getElementById('close-scanner-btn');
    const scanBarcodeBtn = document.getElementById('scan-barcode-btn');
    const scanOcrBtn = document.getElementById('scan-ocr-btn');
    const scanResult = document.getElementById('scan-result');
    const videoElement = document.getElementById('scanner-video');

    let scannerActive = false;
    let quaggaRunning = false;
    let availableCameras = [];
    let selectedCameraId = null;

    // Estilos dinámicos para el escáner
    const scannerStyles = document.createElement('style');
    scannerStyles.textContent = `
        .scan-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 15px 25px;
            background: linear-gradient(90deg, #3a0ca3, #7209b7);
            color: white;
            border: none;
            border-radius: 50px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            cursor: pointer;
            z-index: 999;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: bold;
            font-size: 1rem;
        }
        
        .scanner-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            z-index: 1000;
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
        }
        
        .scanner-content {
            width: 100%;
            max-width: 1000px;
            text-align: center;
        }
        
        #scanner-container {
            width: 100%;
            max-width: 800px;
            height: 300px;
            border: 3px solid #3a0ca3;
            position: relative;
            overflow: hidden;
            margin: 20px auto;
        }
        
        #scanner-video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .scan-overlay {
            position: absolute;
            top: 10;
            left: 0;
            width: 90%;
            height: 85%;
            border: 2px dashed white;
            pointer-events: none;
        }
        
        .scanner-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        
        .scanner-buttons button {
            padding: 10px 20px;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        #scan-barcode-btn {
            background: #3a0ca3;
        }
        
        #scan-ocr-btn {
            background: #7209b7;
        }
        
        #close-scanner-btn {
            background: #dc3545;
        }
        
        #scan-result {
            margin-top: 20px;
            max-width: 100%;
            word-break: break-all;
            background: rgba(255,255,255,0.1);
            padding: 15px;
            border-radius: 5px;
        }
        
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
            margin-right: 10px;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .camera-select-container {
            margin-top: 15px;
            margin-bottom: 15px;
            width: 100%;
            text-align: center;
        }
        
        .camera-select {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
            padding: 8px 15px;
            border-radius: 5px;
            width: 80%;
            max-width: 300px;
            cursor: pointer;
        }
        
        .camera-select option {
            background: #333;
            color: white;
        }
        
        .camera-toggle {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-left: 10px;
        }
        
        .camera-toggle:hover {
            background: rgba(255,255,255,0.3);
        }
    `;
    document.head.appendChild(scannerStyles);

    // Crear el selector de cámaras
    const cameraSelectContainer = document.createElement('div');
    cameraSelectContainer.className = 'camera-select-container';
    cameraSelectContainer.innerHTML = `
        <select id="camera-select" class="camera-select">
            <option value="">Cargando cámaras...</option>
        </select>
        <button id="camera-toggle" class="camera-toggle" title="Cambiar entre cámara frontal y trasera">
            <i class="fas fa-sync-alt"></i>
        </button>
    `;

    // Insertar el selector de cámaras después del título en el modal
    const scannerContent = document.querySelector('.scanner-content');
    scannerContent.insertBefore(cameraSelectContainer, scannerContent.querySelector('#scanner-container'));

    const cameraSelect = document.getElementById('camera-select');
    const cameraToggle = document.getElementById('camera-toggle');

    // Función para enumerar las cámaras disponibles
    async function enumerateCameras() {
        try {
            const devices = await navigator.mediaDevices.enumerateDevices();
            availableCameras = devices.filter(device => device.kind === 'videoinput');

            // Actualizar el selector de cámaras
            cameraSelect.innerHTML = '';

            if (availableCameras.length === 0) {
                cameraSelect.innerHTML = '<option value="">No se encontraron cámaras</option>';
                return;
            }

            availableCameras.forEach((camera, index) => {
                const option = document.createElement('option');
                option.value = camera.deviceId;
                option.text = camera.label || `Cámara ${index + 1}`;
                cameraSelect.appendChild(option);
            });

            // Seleccionar la cámara trasera por defecto si está disponible
            const backCamera = availableCameras.find(camera =>
                camera.label.toLowerCase().includes('back') ||
                camera.label.toLowerCase().includes('trasera') ||
                camera.label.toLowerCase().includes('environment')
            );

            if (backCamera) {
                cameraSelect.value = backCamera.deviceId;
                selectedCameraId = backCamera.deviceId;
            } else if (availableCameras.length > 0) {
                cameraSelect.value = availableCameras[0].deviceId;
                selectedCameraId = availableCameras[0].deviceId;
            }

        } catch (err) {
            console.error('Error al enumerar cámaras:', err);
            cameraSelect.innerHTML = '<option value="">Error al cargar cámaras</option>';
        }
    }

    // Cambiar entre cámaras frontal y trasera
    cameraToggle.addEventListener('click', () => {
        if (availableCameras.length <= 1) return;

        const currentIndex = availableCameras.findIndex(camera => camera.deviceId === selectedCameraId);
        const nextIndex = (currentIndex + 1) % availableCameras.length;

        selectedCameraId = availableCameras[nextIndex].deviceId;
        cameraSelect.value = selectedCameraId;

        // Reiniciar el escáner con la nueva cámara
        if (scannerActive) {
            if (quaggaRunning) {
                startBarcodeScanner();
            } else {
                startOCRScanner();
            }
        }
    });

    // Cambiar la cámara cuando se selecciona una diferente
    cameraSelect.addEventListener('change', () => {
        selectedCameraId = cameraSelect.value;

        // Reiniciar el escáner con la nueva cámara
        if (scannerActive) {
            if (quaggaRunning) {
                startBarcodeScanner();
            } else {
                startOCRScanner();
            }
        }
    });

    // Eventos del escáner
    scanButton.addEventListener('click', () => {
        scannerModal.style.display = 'flex';
        scanResult.textContent = '';

        // Enumerar cámaras disponibles cuando se abre el modal
        enumerateCameras();
    });

    closeScannerBtn.addEventListener('click', () => {
        closeScanner();
    });

    scanBarcodeBtn.addEventListener('click', () => {
        startBarcodeScanner();
    });

    scanOcrBtn.addEventListener('click', () => {
        startOCRScanner();
    });

    // Función para mostrar mensajes de carga
    function showLoading(message) {
        scanResult.innerHTML = `<div class="loading-spinner"></div>${message}`;
    }

    // Función para cerrar el escáner (usada por el botón de cerrar)
    function closeScanner() {
        // Detener la cámara
        if (videoElement.srcObject) {
            videoElement.srcObject.getTracks().forEach(track => track.stop());
            videoElement.srcObject = null;
        }

        // Detener Quagga solo si está en ejecución
        if (quaggaRunning && typeof Quagga !== 'undefined') {
            try {
                Quagga.stop();
            } catch (err) {
                console.error("Error al detener Quagga:", err);
            }
            quaggaRunning = false;
        }

        scannerActive = false;
        scannerModal.style.display = 'none';
    }

    // Función para detener solo la cámara (sin tocar Quagga)
    function stopCamera() {
        if (videoElement.srcObject) {
            videoElement.srcObject.getTracks().forEach(track => track.stop());
            videoElement.srcObject = null;
        }
        scannerActive = false;
    }

    // Obtener restricciones de cámara basadas en la selección
    function getCameraConstraints() {
        const constraints = {
            video: {
                width: {
                    ideal: 1280
                },
                height: {
                    ideal: 720
                }
            },
            audio: false
        };

        if (selectedCameraId) {
            constraints.video.deviceId = {
                exact: selectedCameraId
            };
        } else {
            constraints.video.facingMode = {
                ideal: 'environment'
            };
        }

        return constraints;
    }


    // --- AMPLIAR CAMPO DE VISIÓN ---
    // Cambiar el overlay y el área de captura para que sea más grande (OCR)
    function startOCRScanner() {
        stopCamera();
        scannerActive = true;
        showLoading('Preparando cámara para OCR...');

        (async () => {
            try {
                const constraints = getCameraConstraints();
                const stream = await navigator.mediaDevices.getUserMedia(constraints);
                videoElement.srcObject = stream;
                await videoElement.play();

                // Overlay más grande (50% de alto, 90% de ancho)
                const overlay = document.createElement('div');
                overlay.style.position = 'absolute';
                overlay.style.top = '10%';
                overlay.style.left = '5%';
                overlay.style.width = '90%';
                overlay.style.height = '70%';
                overlay.style.border = '2px dashed yellow';
                overlay.style.pointerEvents = 'none';
                videoElement.parentNode.appendChild(overlay);

                scanResult.innerHTML = 'Coloque el carnet en el área marcada y haga clic para capturar';

                videoElement.onclick = async function() {
                    if (!scannerActive) return;
                    showLoading('Procesando imagen...');

                    // Captura más amplia
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    canvas.width = videoElement.videoWidth;
                    canvas.height = videoElement.videoHeight * 0.5;
                    ctx.drawImage(
                        videoElement,
                        0, videoElement.videoHeight * 0.1, // y origen
                        videoElement.videoWidth, videoElement.videoHeight * 0.5, // alto origen
                        0, 0,
                        canvas.width, canvas.height
                    );

                    const processedCanvas = preprocessImageForOCR(canvas);
                    try {
                        const {
                            data: {
                                text
                            }
                        } = await Tesseract.recognize(
                            processedCanvas.toDataURL('image/jpeg', 0.9),
                            'spa+eng', {
                                logger: m => console.log(m),
                                tessedit_pageseg_mode: 6,
                                tessedit_char_whitelist: 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-@. '
                            }
                        );
                        // Procesar el texto reconocido
                        processOCRText(text);
                    } catch (err) {
                        console.error(err);
                        scanResult.innerHTML = 'Error en OCR: ' + err.message;
                    } finally {
                        videoElement.parentNode.removeChild(overlay);
                    }
                };
            } catch (err) {
                console.error(err);
                scanResult.innerHTML = 'Error al acceder a la cámara: ' + err.message;
            }
        })();
    }

    // --- SOPORTE PARA MÁS CÓDIGOS DE BARRAS Y QR ---
    function startBarcodeScanner() {
        stopCamera();
        if (quaggaRunning && typeof Quagga !== 'undefined') {
            try {
                Quagga.stop();
                quaggaRunning = false;
            } catch (err) {
                console.error("Error al detener Quagga:", err);
            }
        }
        scannerActive = true;
        showLoading('Iniciando escáner de código...');
        if (typeof Quagga === 'undefined') {
            scanResult.innerHTML = 'Error: No se pudo cargar la biblioteca Quagga.';
            return;
        }
        try {
            const constraints = getCameraConstraints();
            Quagga.init({
                inputStream: {
                    name: "Live",
                    type: "LiveStream",
                    target: videoElement,
                    constraints: constraints.video,
                },
                decoder: {
                    readers: [
                        "code_128_reader",
                        "ean_reader",
                        "ean_8_reader",
                        "code_39_reader",
                        "code_39_vin_reader",
                        "upc_reader",
                        "upc_e_reader",
                        "codabar_reader",
                        "i2of5_reader",
                        "2of5_reader",
                        "code_93_reader",
                        "qr_reader" // Soporte para QR
                    ]
                },
            }, function(err) {
                if (err) {
                    console.error(err);
                    scanResult.innerHTML = 'Error al iniciar el escáner: ' + err.message;
                    return;
                }
                quaggaRunning = true;
                Quagga.start();
                scanResult.innerHTML = 'Escáner activo. Apunte al código de barras o QR del carnet.';
            });
            Quagga.onDetected(function(result) {
                if (!scannerActive) return;
                const code = result.codeResult.code;
                // Mostrar modal de confirmación antes de autocompletar
                showConfirmModal({
                    'Código detectado': code
                }, () => {
                    // Autocompletar solo si el usuario acepta
                    scanResult.innerHTML = 'Código detectado: ' + code;
                    // Simulación de datos del carnet (en producción esto vendría de una API)
                    const fakeData = code.split('|');
                    if (fakeData.length >= 5) {
                        document.getElementById('codigo').value = fakeData[0];
                        document.getElementById('nombre').value = fakeData[1];
                        document.getElementById('apellido').value = fakeData[2];
                        document.getElementById('email').value = fakeData[3];
                        document.getElementById('carrera').value = fakeData[4];
                        scanResult.innerHTML += '<br><br>Datos del carnet cargados automáticamente!';
                        setTimeout(() => {
                            closeScanner();
                        }, 2000);
                    } else {
                        scanResult.innerHTML += '<br><br>Formato de código no reconocido.';
                    }
                }, () => {
                    scanResult.innerHTML = 'Lectura cancelada por el usuario.';
                });
            });
        } catch (err) {
            console.error("Error al inicializar Quagga:", err);
            scanResult.innerHTML = 'Error al inicializar el escáner: ' + err.message;
        }
    }

    // --- MODIFICAR PROCESAMIENTO DE OCR PARA CONFIRMAR ANTES DE AUTOCOMPLETAR ---
    function processOCRText(text) {
        const lines = text.split('\n').filter(line => line.trim() !== '');
        let dataFound = false;
        const patterns = {
            codigo: /(?:cod|id|matr[ií]cula|n°)\s*[:.]?\s*(\d{5,10})/i,
            nombre: /(?:nombre)\s*[:.]?\s*([A-Z][a-z]+(?:\s+[A-Z][a-z]+)*)/i,
            apellido: /(?:apellidos?)\s*[:.]?\s*([A-Z][a-z]+(?:\s+[A-Z][a-z]+)*)/i,
            email: /(\S+@(?:[a-z]+\.)?(?:universidad|edu|univ|uni)\.[a-z]{2,3})/i,
            carrera: /(?:carrera|programa)\s*[:.]?\s*([A-Z][A-Za-z\s]+)/i
        };
        const detected = {};
        for (const line of lines) {
            for (const [field, pattern] of Object.entries(patterns)) {
                const match = line.match(pattern);
                if (match && match[1]) {
                    detected[field] = match[1].trim();
                    dataFound = true;
                }
            }
        }
        if (!dataFound) {
            let codigoDetectado = null;
            let cedulaDetectada = null;
            const codigoMatch = text.match(/\b\d{8}\b/);
            if (codigoMatch) {
                codigoDetectado = codigoMatch[0];
            }
            const cedulaMatch = text.match(/\d{3}-\d{3,4}-\d{4}/);
            if (cedulaMatch) {
                cedulaDetectada = cedulaMatch[0];
            }
            if (codigoDetectado || cedulaDetectada) {
                buscarEstudiantePorCodigoOCedula(codigoDetectado, cedulaDetectada);
                return;
            }
        }
        if (dataFound) {
            // Mostrar modal de confirmación antes de autocompletar
            showConfirmModal(detected, () => {
                if (detected.codigo) document.getElementById('codigo').value = detected.codigo;
                if (detected.nombre) document.getElementById('nombre').value = detected.nombre;
                if (detected.apellido) document.getElementById('apellido').value = detected.apellido;
                if (detected.email) document.getElementById('email').value = detected.email;
                if (detected.carrera) document.getElementById('carrera').value = detected.carrera;
                scanResult.innerHTML = 'Datos extraídos con éxito!...';
                setTimeout(() => {
                    closeScanner();
                }, 3000);
            }, () => {
                scanResult.innerHTML = 'Lectura cancelada por el usuario.';
            });
        } else {
            scanResult.innerHTML = 'No se pudieron extraer datos automáticamente.<br><br>' +
                'Texto reconocido:<br><pre style="text-align: left; background: rgba(0,0,0,0.3); padding: 10px; border-radius: 5px; max-height: 150px; overflow-y: auto;">' +
                text + '</pre>' +
                '<br>Por favor ingrese los datos manualmente.';
        }
    }

    function buscarEstudiantePorCodigoOCedula(codigo, cedula) {
        fetch('buscar_estudiante.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `codigo=${encodeURIComponent(codigo || '')}&cedula=${encodeURIComponent(cedula || '')}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    document.getElementById('codigo').value = data.data.codigo_estudiante || '';
                    document.getElementById('nombre').value = data.data.nombre || '';
                    document.getElementById('apellido').value = data.data.apellido || '';
                    document.getElementById('email').value = data.data.email || '';
                    document.getElementById('carrera').value = data.data.id_carrera || '';
                    scanResult.innerHTML = 'Datos del estudiante cargados desde la base de datos.';
                    setTimeout(() => {
                        closeScanner();
                    }, 2000);
                } else {
                    scanResult.innerHTML = 'Estudiante no encontrado en la base de datos.';
                }
            })
            .catch(err => {
                scanResult.innerHTML = 'Error al buscar estudiante: ' + err.message;
            });
    }

    // Función para preprocesar la imagen antes del OCR
    function preprocessImageForOCR(canvas) {
        // Aumentar resolución del canvas
        const scale = 2; // Duplicar resolución
        const tempCanvas = document.createElement('canvas');
        tempCanvas.width = canvas.width * scale;
        tempCanvas.height = canvas.height * scale;
        const ctx = tempCanvas.getContext('2d');
        ctx.imageSmoothingEnabled = true;
        ctx.drawImage(canvas, 0, 0, tempCanvas.width, tempCanvas.height);

        // Convertir a escala de grises
        let imageData = ctx.getImageData(0, 0, tempCanvas.width, tempCanvas.height);
        let data = imageData.data;
        for (let i = 0; i < data.length; i += 4) {
            const avg = (data[i] + data[i + 1] + data[i + 2]) / 3;
            data[i] = avg;
            data[i + 1] = avg;
            data[i + 2] = avg;
        }

        // Aumentar contraste y brillo
        const contrast = 60; // Más contraste
        const brightness = 20; // Más brillo
        const factor = (259 * (contrast + 255)) / (255 * (259 - contrast));
        for (let i = 0; i < data.length; i += 4) {
            data[i] = factor * (data[i] - 128) + 128 + brightness;
            data[i + 1] = factor * (data[i + 1] - 128) + 128 + brightness;
            data[i + 2] = factor * (data[i + 2] - 128) + 128 + brightness;
        }

        // Binarización (umbral)
        const threshold = 140;
        for (let i = 0; i < data.length; i += 4) {
            const v = data[i] > threshold ? 255 : 0;
            data[i] = data[i + 1] = data[i + 2] = v;
        }

        // Filtro de nitidez (kernel simple)
        const sharpenKernel = [
            0, -1, 0,
            -1, 5, -1,
            0, -1, 0
        ];
        imageData = applyConvolution(imageData, sharpenKernel, tempCanvas.width, tempCanvas.height);
        ctx.putImageData(imageData, 0, 0);
        return tempCanvas;
    }

    // Filtro de convolución para nitidez
    function applyConvolution(imageData, kernel, width, height) {
        const output = new ImageData(width, height);
        const src = imageData.data;
        const dst = output.data;
        const side = Math.round(Math.sqrt(kernel.length));
        const halfSide = Math.floor(side / 2);
        for (let y = 0; y < height; y++) {
            for (let x = 0; x < width; x++) {
                let r = 0,
                    g = 0,
                    b = 0;
                for (let ky = 0; ky < side; ky++) {
                    for (let kx = 0; kx < side; kx++) {
                        const px = x + kx - halfSide;
                        const py = y + ky - halfSide;
                        if (px >= 0 && px < width && py >= 0 && py < height) {
                            const offset = (py * width + px) * 4;
                            const weight = kernel[ky * side + kx];
                            r += src[offset] * weight;
                            g += src[offset + 1] * weight;
                            b += src[offset + 2] * weight;
                        }
                    }
                }
                const i = (y * width + x) * 4;
                dst[i] = Math.min(Math.max(r, 0), 255);
                dst[i + 1] = Math.min(Math.max(g, 0), 255);
                dst[i + 2] = Math.min(Math.max(b, 0), 255);
                dst[i + 3] = src[i + 3];
            }
        }
        return output;
    }

    // Limpiar al cerrar la página
    window.addEventListener('beforeunload', closeScanner);
    </script>
</body>

</html>