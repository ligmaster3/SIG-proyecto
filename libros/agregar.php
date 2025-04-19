<?php
require_once '../config/connection.php';
require_once '../components/header.php';
require_once '../components/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Procesar imagen
    $imagenNombre = null;
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $imagenNombre = uniqid() . '.' . $extension;
        $rutaDestino = "../../assets/images/libros/" . $imagenNombre;
        
        if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
            $error = "Error al subir la imagen";
        }
    }

    // Insertar libro
    $stmt = $conn->prepare("
        INSERT INTO libros 
        (titulo, autor, isbn, categoria_id, editorial, anio, imagen, disponible) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 1)
    ");
    $stmt->execute([
        $_POST['titulo'],
        $_POST['autor'],
        $_POST['isbn'],
        $_POST['categoria_id'],
        $_POST['editorial'],
        $_POST['anio'],
        $imagenNombre
    ]);
    
    header("Location: listar.php");
    exit;
}

$categorias = obtenerCategorias($conn);
?>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h4><i class="fas fa-plus-circle me-2"></i>Agregar Nuevo Libro</h4>
    </div>
    <div class="card-body">
        <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Título *</label>
                        <input type="text" class="form-control" name="titulo" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Autor *</label>
                        <input type="text" class="form-control" name="autor" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ISBN *</label>
                        <input type="text" class="form-control" name="isbn" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Categoría *</label>
                        <select class="form-select" name="categoria_id" required>
                            <?php foreach ($categorias as $categoria): ?>
                            <option value="<?= $categoria['id'] ?>"><?= htmlspecialchars($categoria['nombre']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Portada del Libro</label>
                        <div class="upload-area" id="uploadArea">
                            <i class="fas fa-cloud-upload-alt fa-3x mb-3 text-primary"></i>
                            <p>Haz clic o arrastra una imagen aquí</p>
                            <input type="file" name="imagen" id="imagenInput" accept="image/*" style="display: none;">
                            <div id="imagePreview" class="text-center"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Editorial</label>
                        <input type="text" class="form-control" name="editorial">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Año de publicación</label>
                        <input type="number" class="form-control" name="anio" min="1900" max="<?= date('Y') ?>">
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-custom mt-3">
                <i class="fas fa-save me-1"></i>Guardar Libro
            </button>
        </form>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadArea = document.getElementById('uploadArea');
    const imageInput = document.getElementById('imagenInput');
    const imagePreview = document.getElementById('imagePreview');

    uploadArea.addEventListener('click', () => imageInput.click());

    imageInput.addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            const file = e.target.files[0];
            if (file.type.match('image.*')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.innerHTML =
                        `<img src="${e.target.result}" class="preview-image" />`;
                    uploadArea.style.borderColor = 'var(--success-color)';
                };
                reader.readAsDataURL(file);
            }
        }
    });

    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.style.borderColor = 'var(--primary-color)';
        uploadArea.style.backgroundColor = 'rgba(52, 152, 219, 0.1)';
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.style.borderColor = '#ddd';
        uploadArea.style.backgroundColor = '';
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.style.borderColor = '#ddd';
        uploadArea.style.backgroundColor = '';

        if (e.dataTransfer.files.length > 0) {
            imageInput.files = e.dataTransfer.files;
            const event = new Event('change');
            imageInput.dispatchEvent(event);
        }
    });
});
</script>

<?php require_once '../components/footer.php'; ?>