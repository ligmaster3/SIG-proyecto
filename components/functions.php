<?php
function obtenerCategorias($conn) {
    $stmt = $conn->query("SELECT * FROM categorias");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerUsuarios($conn) {
    $stmt = $conn->query("SELECT * FROM usuarios");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerLibro($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM libros WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>