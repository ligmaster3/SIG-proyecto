<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Metadatos -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca CRUBA - <?php echo $titulo_pagina ?? 'Dashboard'; ?></title>

    <!-- Fuentes -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- CSS Externos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">

    <!-- CSS Internos -->
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">

    <!-- Scripts Externos -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous">
    </script>

    <!-- Scripts Internos -->
    <script src="assets/js/notificaciones.js"></script>
    <script src="assets/js/script.js"></script>
</head>

<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="logo-container">
                <img src="assets/images/logo.png" alt="CRUBA Logo" class="logo">
                <h1><span>Biblioteca CRUBA</span></h1>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                        <a href="dashboard.php" title="Dashboard"><i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span></a>
                    </li>
                    <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                        <a href="index.php" title="Inicio"><i class="fas fa-home"></i> <span>Inicio</span></a>
                    </li>

                    <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'libros.php' ? 'active' : ''; ?>">
                        <a href="libros.php" title="Libros"><i class="fas fa-book"></i> <span>Libros</span></a>
                    </li>
                    <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'estudiantes.php' ? 'active' : ''; ?>">
                        <a href="estudiantes.php" title="Estudiantes"><i class="fas fa-book"></i>
                            <span>Estudiantes</span></a>
                    </li>
                    <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'prestamos.php' ? 'active' : ''; ?>">
                        <a href="prestamos.php" title="Préstamos"><i class="fas fa-exchange-alt"></i>
                            <span>Préstamos</span></a>
                    </li>
                    <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'reportes.php' ? 'active' : ''; ?>">
                        <a href="reportes.php" title="Reportes"><i class="fas fa-file-alt"></i>
                            <span>Reportes</span></a>
                    </li>
                    <?php if ($_SESSION['rol'] == 'Administrador'): ?>
                    <li>
                        <a href="#" data-title="Configuración"><i class="fas fa-cog"></i> <span>Configuración</span></a>
                    </li>
                    <?php endif; ?>
                </ul>


            </nav>
        </aside>
        <main class="main-content">
            <header class="top-bar">
                <div class="search-bar">
                    <input type="text" placeholder="Buscar...">
                    <button><i class="fas fa-search"></i></button>
                </div>
                <div class="user-info">
                    <span class="welcome">Bienvenido, <?php echo $_SESSION['nombre']; ?></span>
                    <div class="user-avatar">
                        <i class="fas fa-user-circle" title="Usuario"> </i>
                    </div>
                    <!-- Botón Cerrar Sesión para móvil/sidebar colapsada -->
                    <a href="logout.php" class="logout-btn top-bar-logout d-none d-lg-flex" title="Cerrar Sesión"><i
                            class="fas fa-sign-out-alt"></i> <span>Cerrar Sesión</span></a>
                </div>
            </header>

            <div class="content-wrapper">