<?php
/**
 * bucv_autores.php
 * API para gestión de autores
 * Operaciones CRUD: listar, detalle, crear, actualizar, eliminar
 */

header('Content-Type: application/json; charset=utf-8');

require_once(__DIR__ . '/../repositories/bucv_1cc2s4B3.php');

$repo = new BucvRepository();

// Obtener acción
$accion = $_GET['accion'] ?? $_POST['accion'] ?? 'listar';

try {
    switch ($accion) {
        case 'listar':
            // Lista todos los autores (simple)
            $autores = $repo->obtenerAutores();
            echo json_encode([
                'success' => true,
                'data' => $autores
            ]);
            break;

        case 'listar_con_conteo':
            // Lista autores con conteo de recursos
            $autores = $repo->obtenerAutoresConConteo();
            echo json_encode([
                'success' => true,
                'data' => $autores
            ]);
            break;

        case 'detalle':
            // Obtener autor por ID
            $id = intval($_GET['id'] ?? 0);
            if ($id <= 0) {
                throw new Exception('ID de autor no válido');
            }
            $autor = $repo->obtenerAutorPorId($id);
            if (!$autor) {
                throw new Exception('Autor no encontrado');
            }
            echo json_encode([
                'success' => true,
                'data' => $autor
            ]);
            break;

        case 'crear':
            // Crear nuevo autor
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido');
            }
            $nombre = trim($_POST['nombre'] ?? '');
            if (empty($nombre)) {
                throw new Exception('El nombre del autor es requerido');
            }
            $id = $repo->crearAutor($nombre);
            echo json_encode([
                'success' => true,
                'data' => [
                    'id_autor' => $id,
                    'nombre' => $nombre
                ],
                'message' => 'Autor creado correctamente'
            ]);
            break;

        case 'actualizar':
            // Actualizar autor existente
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido');
            }
            $id = intval($_POST['id'] ?? 0);
            $nombre = trim($_POST['nombre'] ?? '');
            if ($id <= 0) {
                throw new Exception('ID de autor no válido');
            }
            if (empty($nombre)) {
                throw new Exception('El nombre del autor es requerido');
            }
            $repo->actualizarAutor($id, $nombre);
            echo json_encode([
                'success' => true,
                'data' => [
                    'id_autor' => $id,
                    'nombre' => $nombre
                ],
                'message' => 'Autor actualizado correctamente'
            ]);
            break;

        case 'eliminar':
            // Eliminar autor
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido');
            }
            $id = intval($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new Exception('ID de autor no válido');
            }
            // Verificar si tiene recursos asociados
            $cantidadRecursos = $repo->contarRecursosPorAutor($id);
            if ($cantidadRecursos > 0) {
                throw new Exception("No se puede eliminar el autor porque tiene $cantidadRecursos recurso(s) asociado(s)");
            }
            $repo->eliminarAutor($id);
            echo json_encode([
                'success' => true,
                'message' => 'Autor eliminado correctamente'
            ]);
            break;

        default:
            throw new Exception('Acción no válida');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
