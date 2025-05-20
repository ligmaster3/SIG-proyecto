<?php
require_once 'config/connection.php';

session_start();
try {
    $accion = $_POST['accion'] ?? '';

    switch ($accion) {
        case 'agregar':
            // Validar y sanitizar datos
            $codigo = trim($_POST['codigo']);
            $nombre = trim($_POST['nombre']);
            $apellido = trim($_POST['apellido']);
            $genero = trim($_POST['genero']);
            $carrera = (int)$_POST['carrera'];
            $email = trim($_POST['email'] ?? '');
            $hora_entrada = isset($_POST['hora_entrada']) && $_POST['hora_entrada'] ? $_POST['hora_entrada'] : null;
            $hora_salida = isset($_POST['hora_salida']) && $_POST['hora_salida'] ? $_POST['hora_salida'] : null;
            // Convertir formato de datetime-local a datetime SQL
            function normalizarFecha($fecha)
            {
                if (!$fecha) return null;
                $fecha = str_replace('T', ' ', $fecha);
                if (strlen($fecha) == 16) $fecha .= ':00';
                return $fecha;
            }
            $hora_entrada = normalizarFecha($hora_entrada);
            $hora_salida = normalizarFecha($hora_salida);

            if (empty($codigo) || empty($nombre) || empty($apellido) || empty($genero) || empty($carrera)) {
                throw new Exception('Todos los campos obligatorios deben ser completados');
            }

            // Insertar nuevo estudiante
            $sql = "INSERT INTO estudiantes (codigo_estudiante, nombre, apellido, genero, id_carrera, email) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $params = [$codigo, $nombre, $apellido, $genero, $carrera, $email];

            ejecutarConsulta($sql, $params);

            // Insertar registro de asistencia si se proporcionan horas
            if ($hora_entrada || $hora_salida) {
                // Obtener el id del estudiante recién insertado
                $stmt = ejecutarConsulta("SELECT id_estudiante FROM estudiantes WHERE codigo_estudiante = ? ORDER BY id_estudiante DESC LIMIT 1", [$codigo]);
                $nuevo = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($nuevo) {
                    $sql_insert = "INSERT INTO registro_asistencia (id_estudiante, fecha_entrada, fecha_salida) VALUES (?, ?, ?)";
                    ejecutarConsulta($sql_insert, [$nuevo['id_estudiante'], $hora_entrada, $hora_salida]);
                }
            }

            $_SESSION['exito'] = "Estudiante registrado correctamente.";
            header("Location: index.php");
            exit;
        case 'editar':
            $id_estudiante = (int)$_POST['id_estudiante'];
            $codigo = trim($_POST['codigo']);
            $nombre = trim($_POST['nombre']);
            $apellido = trim($_POST['apellido']);
            $genero = trim($_POST['genero']);
            $carrera = (int)$_POST['carrera'];
            $email = trim($_POST['email'] ?? '');
            $hora_entrada = isset($_POST['hora_entrada']) && $_POST['hora_entrada'] ? $_POST['hora_entrada'] : null;
            $hora_salida = isset($_POST['hora_salida']) && $_POST['hora_salida'] ? $_POST['hora_salida'] : null;
            // Convertir formato de datetime-local a datetime SQL
            function normalizarFecha($fecha)
            {
                if (!$fecha) return null;
                $fecha = str_replace('T', ' ', $fecha);
                if (strlen($fecha) == 16) $fecha .= ':00';
                return $fecha;
            }
            $hora_entrada = normalizarFecha($hora_entrada);
            $hora_salida = normalizarFecha($hora_salida);
            if (empty($codigo) || empty($nombre) || empty($apellido) || empty($genero) || empty($carrera)) {
                throw new Exception('Todos los campos obligatorios deben ser completados');
            }
            $sql = "UPDATE estudiantes SET codigo_estudiante = ?, nombre = ?, apellido = ?, genero = ?, id_carrera = ?, email = ? WHERE id_estudiante = ?";
            $params = [$codigo, $nombre, $apellido, $genero, $carrera, $email, $id_estudiante];
            ejecutarConsulta($sql, $params);
            // Actualizar o crear registro de asistencia si se proporcionan horas
            if ($hora_entrada || $hora_salida) {
                // Buscar el último registro de asistencia
                $stmt = ejecutarConsulta("SELECT * FROM registro_asistencia WHERE id_estudiante = ? ORDER BY fecha_entrada DESC, id DESC LIMIT 1", [$id_estudiante]);
                $registro = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($registro) {
                    // Actualizar el registro existente
                    $sql_update = "UPDATE registro_asistencia SET ";
                    $params_update = [];
                    $set = [];
                    if ($hora_entrada) {
                        $set[] = "fecha_entrada = ?";
                        $params_update[] = $hora_entrada;
                    }
                    if ($hora_salida) {
                        $set[] = "fecha_salida = ?";
                        $params_update[] = $hora_salida;
                    }
                    $sql_update .= implode(", ", $set) . " WHERE id = ?";
                    $params_update[] = $registro['id'];
                    ejecutarConsulta($sql_update, $params_update);
                } else {
                    // Crear nuevo registro
                    $sql_insert = "INSERT INTO registro_asistencia (id_estudiante, fecha_entrada, fecha_salida) VALUES (?, ?, ?)";
                    ejecutarConsulta($sql_insert, [$id_estudiante, $hora_entrada, $hora_salida]);
                }
            }
            $_SESSION['exito'] = "Estudiante actualizado correctamente.";
            header("Location: index.php");
            exit;
        case 'eliminar':
            $id_estudiante = (int)$_POST['id_estudiante'];
            // Verificar si el estudiante tiene préstamos activos
            $sql = "SELECT COUNT(*) as total FROM prestamos WHERE id_estudiante = ? AND (estado = 'Pendiente' OR estado = 'Atrasado')";
            $stmt = ejecutarConsulta($sql, [$id_estudiante]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result['total'] > 0) {
                throw new Exception('No se puede eliminar el estudiante porque tiene préstamos activos');
            }
            // Eliminar estudiante
            ejecutarConsulta("DELETE FROM estudiantes WHERE id_estudiante = ?", [$id_estudiante]);
            $_SESSION['exito'] = "Estudiante eliminado correctamente.";
            header("Location: index.php");
            exit;
        case 'registrar_asistencia':
            $codigo = trim($_POST['codigo']);
            $tipo_registro = $_POST['tipo_registro'];
            $fecha_registro = $_POST['fecha_registro'];

            // Buscar el estudiante por código
            $stmt = ejecutarConsulta("SELECT id_estudiante FROM estudiantes WHERE codigo_estudiante = ?", [$codigo]);
            $estudiante = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$estudiante) {
                throw new Exception('Estudiante no encontrado');
            }
            $id_estudiante = $estudiante['id_estudiante'];
            $fecha = date('Y-m-d', strtotime($fecha_registro));

            if ($tipo_registro === 'entrada') {
                // Registrar entrada (nueva fila)
                $sql = "INSERT INTO registro_asistencia (id_estudiante, fecha, hora_entrada) VALUES (?, ?, ?)";
                ejecutarConsulta($sql, [$id_estudiante, $fecha, $fecha_registro]);
                $_SESSION['exito'] = "Entrada registrada correctamente.";
            } else if ($tipo_registro === 'salida') {
                // Registrar salida (actualizar la última fila sin salida de hoy)
                $sql = "UPDATE registro_asistencia SET hora_salida = ? WHERE id_estudiante = ? AND fecha = ? AND hora_salida IS NULL ORDER BY hora_entrada DESC LIMIT 1";
                ejecutarConsulta($sql, [$fecha_registro, $id_estudiante, $fecha]);
                $_SESSION['exito'] = "Salida registrada correctamente.";
            }
            header("Location: index.php");
            exit;
        default:
            throw new Exception('Acción no válida');
    }
} catch (Exception $e) {
    $_SESSION['errores'] = ["Error al registrar el estudiante: " . $e->getMessage()];
    header("Location: index.php");
    exit;
}
