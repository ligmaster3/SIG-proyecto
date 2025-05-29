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
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        main {
            display: flex;
          
        }


        header {
            padding: 30px;
            /* margin: 10px; */
            border: solid 1px transparent;
            border-radius: var(--border-radius);
            background: linear-gradient(45deg, rgb(93, 178, 226), rgb(93, 226, 95));
        }

        .container {
            width: 100%;
            /* max-width: 800px; */
            /* margin: 10px; */
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
            padding-top: 3rem;
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

        .mb-5 {
            display: flex;
        }

        .d-grid {
            display: flex;
            justify-content: center;
            padding: 10px;
        }

        .form-title {
            color: var(--light-color);
            font-size: 2.5rem;
            text-align: center;
            margin-bottom: 0.5rem;
            font-weight: 700;
            font-style: bold;
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
            main {
                flex-direction: row;
                align-items: flex-start;
            }

            .container {
                flex: 2;
            } header {margin-top: 40px;}

            .login-container {
                flex: 1;
                margin-left: 2rem;
            }

            .modal-body {
                grid-template-columns: 1fr;
            }
            .d-grid {
                display: block;
            }
            .d-grid, .btn{
                margin-top: 5x  px;
               width: 100%;
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
            .form-section {
                margin: 0px !important;
            }
        }

        @media (max-width: 767px) {
            main {
                flex-direction: column;
            }

            .container,
            .login-container {
                width: 100%;
                margin: 0;
            }

          
        }

        /* Ajustes adicionales para mejor responsividad */
        @media (max-width: 576px) {
            .container {
                padding: 1.5rem;
            }

            .form-section {
                padding: 1.5rem !important;
            }

            .modal-body {
                grid-template-columns: 1fr;
            }

            .full-width {
                grid-column: span 1;
            }
        }

        .scanner-container {
            width: 100%;
            max-width: 500px;
            margin: 20px auto;
            border: 2px dashed #ccc;
            padding: 10px;
        }

        #scanner-view {
            width: 100%;
            height: 300px;
            background-color: #f0f0f0;
        }

        .hidden {
            display: none;
        }

        .form-section {
            margin: 10px 0 0 30px;
            width: 100%;
            height: 15rem;
            padding: 20px;
            border-radius: var(--border-radius);
            background-color: #f8f9fa;
            box-shadow: var(--box-shadow);
        }
    </style>
</head>

<body class="registro">

    <div style="position: absolute; top: 50px; left: 30px; z-index: 1001;">
        <a href="dashboard.php" class="btn btn-secondary"
            style="padding: 0.7rem 1.5rem; font-size: 1rem; border-radius: 8px; text-decoration: none; color: #495057; background: #f1f3f5; border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.07);">
            <i class="fas fa-arrow-left"></i> Volver al Dashboard
        </a>
    </div>
    <header>
        <h1 class="form-title">Sistema Académico</h1>
        <h3 class="form-subtitle">Registro de <span class="highlight">Nuevos Estudiantes</span></h3>
    </header>
    <main>
        <div class="container">

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
            </div>
            <div class="mb-5">
                <div class="d-grid gap-2 modal-body">
                    <button type="button" class="btn btn-primary" id="btnManual">Manual</button>
                    <button type="button" class="btn btn-primary" id="btnQR">Escanear QR</button>
                    <button type="button" class="btn btn-primary" id="btnOCR">Usar OCR</button>
                </div>
            </div>
            <form id="formAgregarEstudiante" action="acciones_estudiantes.php" method="POST">
                <input type="hidden" name="accion" value="agregar">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                <div class="container">
                    <!-- Formulario Manual -->
                    <div id="formManual">
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

                    <!-- Lector QR -->
                    <div id="formQR" class="hidden">
                        <div class="scanner-container">
                            <div id="scanner-view"></div>
                        </div>
                        <div class="mb-3">
                            <button type="button" class="btn btn-danger w-100" id="stopScanner">Detener Escáner</button>
                        </div>
                    </div>

                    <!-- Formulario OCR -->
                    <div id="formOCR" class="hidden">
                        <div class="mb-3">
                            <label for="ocrImage" class="form-label">Subir imagen del carnet</label>
                            <input class="form-control" type="file" id="ocrImage" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <button type="button" class="btn btn-info w-100" id="processOCR">Procesar Imagen</button>
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

        <div class="form-section col-md-6">
            <h3>Iniciar Sesión</h3>
            <form method="post" action="login.php">
                <div class="mb-3">
                    <label for="login_codigo" class="form-label">Código de Estudiante</label>
                    <div class="input-field">
                        <i class="input-icon fas fa-id-card"></i>
                        <input type="text" class="form-control has-icon" id="login_codigo"
                            name="codigo_estudiante" required
                            placeholder="Ingrese su código de estudiante">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Ingresar
                </button>
            </form>
        </div>


    </main>



    <script>
        // Control de pestañas del formulario
        document.getElementById('btnManual').addEventListener('click', function() {
            document.getElementById('formManual').classList.remove('hidden');
            document.getElementById('formQR').classList.add('hidden');
            document.getElementById('formOCR').classList.add('hidden');
            stopScanner();
            this.classList.add('active', 'btn-primary');
            this.classList.remove('btn-secondary');
            document.getElementById('btnQR').classList.remove('active', 'btn-primary');
            document.getElementById('btnQR').classList.add('btn-secondary');
            document.getElementById('btnOCR').classList.remove('active', 'btn-primary');
            document.getElementById('btnOCR').classList.add('btn-secondary');
        });

        document.getElementById('btnQR').addEventListener('click', function() {
            document.getElementById('formManual').classList.add('hidden');
            document.getElementById('formQR').classList.remove('hidden');
            document.getElementById('formOCR').classList.add('hidden');
            initScanner();
            this.classList.add('active', 'btn-primary');
            this.classList.remove('btn-secondary');
            document.getElementById('btnManual').classList.remove('active', 'btn-primary');
            document.getElementById('btnManual').classList.add('btn-secondary');
            document.getElementById('btnOCR').classList.remove('active', 'btn-primary');
            document.getElementById('btnOCR').classList.add('btn-secondary');
        });

        document.getElementById('btnOCR').addEventListener('click', function() {
            document.getElementById('formManual').classList.add('hidden');
            document.getElementById('formQR').classList.add('hidden');
            document.getElementById('formOCR').classList.remove('hidden');
            stopScanner();
            this.classList.add('active', 'btn-primary');
            this.classList.remove('btn-secondary');
            document.getElementById('btnManual').classList.remove('active', 'btn-primary');
            document.getElementById('btnManual').classList.add('btn-secondary');
            document.getElementById('btnQR').classList.remove('active', 'btn-primary');
            document.getElementById('btnQR').classList.add('btn-secondary');
        });

        
    </script>
</body>

</html>