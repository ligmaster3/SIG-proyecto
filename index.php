<?php
// require_once 'config.php'; // Archivo de configuración para la conexión a la base de datos
require_once 'config/connection.php'; // Incluir la conexión a la base de datos
require_once 'components/header.php';

// Obtener estadísticas para el dashboard
$stats = [
    'prestamos' => [
        'total' => $conn->query("SELECT COUNT(*) FROM prestamos")->fetchColumn(),
        'activos' => $conn->query("SELECT COUNT(*) FROM prestamos WHERE fecha_devolucion_real IS NULL")->fetchColumn(),
        'vencidos' => $conn->query("SELECT COUNT(*) FROM prestamos WHERE fecha_devolucion_real IS NULL AND fecha_devolucion < CURDATE()")->fetchColumn()
    ],
    'libros' => [
        'total' => $conn->query("SELECT COUNT(*) FROM libros")->fetchColumn(),
        'disponibles' => $conn->query("SELECT COUNT(*) FROM libros WHERE disponible = 1")->fetchColumn(),
        'prestados' => $conn->query("SELECT COUNT(*) FROM libros WHERE disponible = 0")->fetchColumn()
    ],
    'usuarios' => [
        'total' => $conn->query("SELECT COUNT(*) FROM usuarios")->fetchColumn(),
        'activos' => $conn->query("SELECT COUNT(DISTINCT usuario_id) FROM prestamos WHERE fecha_devolucion_real IS NULL")->fetchColumn()
    ]
];

// Obtener préstamos recientes
$prestamosRecientes = $conn->query("
    SELECT p.*, l.titulo as libro, u.nombre as usuario, l.imagen
    FROM prestamos p
    JOIN libros l ON p.libro_id = l.id
    JOIN usuarios u ON p.usuario_id = u.id
    ORDER BY p.fecha_prestamo DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);
?>



<div class="dashboard-container">
    <!-- Sección de Estadísticas Rápidas -->
    <div class="stats-grid">
        <!-- Tarjeta de Préstamos Totales -->
        <div class="stat-card bg-gradient-primary">
            <div class="stat-icon">
                <i class="fas fa-exchange-alt"></i>
            </div>
            <div class="stat-info">
                <h3>Préstamos Totales</h3>
                <span class="stat-number"><?= $stats['prestamos']['total'] ?></span>
            </div>
            <a href="prestamos/listar.php" class="stat-link">Ver todos <i class="fas fa-arrow-right"></i></a>
        </div>

        <!-- Tarjeta de Libros Disponibles -->
        <div class="stat-card bg-gradient-success">
            <div class="stat-icon">
                <i class="fas fa-book-open"></i>
            </div>
            <div class="stat-info">
                <h3>Libros Disponibles</h3>
                <span class="stat-number"><?= $stats['libros']['disponibles'] ?></span>
                <span class="stat-subtext">de <?= $stats['libros']['total'] ?> totales</span>
            </div>
            <a href="libros/listar.php" class="stat-link">Explorar <i class="fas fa-arrow-right"></i></a>
        </div>

        <!-- Tarjeta de Préstamos Vencidos -->
        <div class="stat-card bg-gradient-danger">
            <div class="stat-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-info">
                <h3>Préstamos Vencidos</h3>
                <span class="stat-number"><?= $stats['prestamos']['vencidos'] ?></span>
                <span class="stat-subtext">de <?= $stats['prestamos']['activos'] ?> activos</span>
            </div>
            <a href="prestamos/listar.php?filter=vencidos" class="stat-link">Revisar <i
                    class="fas fa-arrow-right"></i></a>
        </div>

        <!-- Tarjeta de Usuarios Activos -->
        <div class="stat-card bg-secondary">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3>Usuarios Activos</h3>
                <span class="stat-number"><?= $stats['usuarios']['activos'] ?></span>
                <span class="stat-subtext">de <?= $stats['usuarios']['total'] ?> registrados</span>
            </div>
            <a href="usuarios/listar.php" class="stat-link">Administrar <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>

    <!-- Sección de Préstamos Recientes -->
    <div class="recent-loans">
        <div class="section-header">
            <h2><i class="fas fa-clock"></i> Préstamos Recientes</h2>
        </div>

        <div class="loans-grid">
            <?php foreach ($prestamosRecientes as $prestamo):
                $estado = $prestamo['fecha_devolucion_real'] ? 'returned' : ($prestamo['fecha_devolucion'] < date('Y-m-d') ? 'overdue' : 'active');
                $imagen = !empty($prestamo['imagen']) ?
                    "assets/img/libros/" . $prestamo['imagen'] :
                    "assets/img/libros/default.jpg";
            ?>
            <div class="loan-card <?= $estado ?>" data-return-date="<?= $prestamo['fecha_devolucion'] ?>">
                <div class="loan-cover">
                    <img src="<?= $imagen ?>" alt="Portada del libro">
                </div>
                <div class="loan-details">
                    <h4><?= htmlspecialchars($prestamo['libro']) ?></h4>
                    <p><i class="fas fa-user"></i> <?= htmlspecialchars($prestamo['usuario']) ?></p>
                    <div class="loan-dates">
                        <div>
                            <span class="date-label">Préstamo:</span>
                            <span class="date-value"><?= $prestamo['fecha_prestamo'] ?></span>
                        </div>
                        <div>
                            <span class="date-label">Devolución:</span>
                            <span class="date-value"><?= $prestamo['fecha_devolucion'] ?></span>
                        </div>
                    </div>
                </div>
                <div class="loan-status">
                    <span class="status-badge <?= $estado ?>">
                        <?= $estado == 'returned' ? 'Devuelto' : ($estado == 'overdue' ? 'Vencido' : 'Activo') ?>
                    </span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    particlesJS('particles-js', {
        particles: {
            number: {
                value: 60,
                density: {
                    enable: true,
                    value_area: 800
                }
            },
            color: {
                value: "#ffffff"
            },
            shape: {
                type: "circle"
            },
            opacity: {
                value: 0.5,
                random: true
            },
            size: {
                value: 3,
                random: true
            },
            line_linked: {
                enable: true,
                distance: 150,
                color: "#ffffff",
                opacity: 0.3,
                width: 1
            },
            move: {
                enable: true,
                speed: 2,
                direction: "none",
                random: true,
                straight: false,
                out_mode: "out"
            }
        },
        interactivity: {
            detect_on: "canvas",
            events: {
                onhover: {
                    enable: true,
                    mode: "repulse"
                },
                onclick: {
                    enable: true,
                    mode: "push"
                }
            }
        }
    });

    // Crear partículas manuales adicionales
    const header = document.querySelector('.header');
    for (let i = 0; i < 20; i++) {
        const particle = document.createElement('div');
        particle.classList.add('particle');

        // Posición aleatoria
        const size = Math.random() * 5 + 3;
        particle.style.width = `${size}px`;
        particle.style.height = `${size}px`;
        particle.style.left = `${Math.random() * 100}%`;
        particle.style.top = `${Math.random() * 100}%`;

        // Animación única
        particle.style.animationDuration = `${Math.random() * 5 + 3}s`;
        particle.style.animationDelay = `${Math.random() * 2}s`;

        header.appendChild(particle);
    }

    // Función para actualizar los estados de los préstamos
    function updateLoanStatus() {
        const loans = document.querySelectorAll('.loan-card');

        loans.forEach(loan => {
            const statusBadge = loan.querySelector('.status-badge');
            const returnDate = new Date(loan.dataset.returnDate);
            const currentDate = new Date();

            if (loan.classList.contains('returned')) {
                statusBadge.className = 'status-badge returned';
                statusBadge.textContent = 'Devuelto';
            } else if (returnDate < currentDate) {
                statusBadge.className = 'status-badge overdue';
                statusBadge.textContent = 'Vencido';
            } else {
                statusBadge.className = 'status-badge active';
                statusBadge.textContent = 'Activo';
            }
        });
    }

    // Llamar a la función inicialmente
    updateLoanStatus();

    // Actualizar cada minuto
    setInterval(updateLoanStatus, 60000);
});
</script>

<?php
require_once 'components/footer.php';
?>