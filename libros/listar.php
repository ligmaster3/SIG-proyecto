<?php
require_once '../config/connection.php'; // Include database connection
require_once '../components/header.php'; 

$stmt = $conn->query("
    SELECT l.*, c.nombre as categoria 
    FROM libros l 
    JOIN categorias c ON l.categoria_id = c.id
    ORDER BY l.titulo
");
$libros = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-book me-2"></i>Listado de Libros</h2>
    <a href="agregar.php" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>Agregar Libro
    </a>
</div>

<div class="table-responsive">
    <table class="table table-custom table-hover">
        <thead>
            <tr>
                <th>Portada</th>
                <th>Título</th>
                <th>Autor</th>
                <th>Categoría</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($libros as $libro): 
                $imagen = !empty($libro['imagen']) ? 
                    "../assets/images/libros/" . $libro['imagen'] : 
                    "../assets/images/libros/default.jpg";
                $estado = $libro['disponible'] ? 
                    '<span class="status-active">Disponible</span>' : 
                    '<span class="status-overdue">Prestado</span>';
            ?>
            <tr>
                <td>
                    <img src="<?= $imagen ?>" alt="Portada" class="book-cover">
                </td>
                <td><?= htmlspecialchars($libro['titulo']) ?></td>
                <td><?= htmlspecialchars($libro['autor']) ?></td>
                <td><?= htmlspecialchars($libro['categoria']) ?></td>
                <td><?= $estado ?></td>
                <td>
                    <a href="editar.php?id=<?= $libro['id'] ?>" class="btn btn-sm btn-warning me-1">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="eliminar.php?id=<?= $libro['id'] ?>" class="btn btn-sm btn-danger">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once '../components/footer.php'; ?>