<?php
/**
 * bucv_recursos.php
 * Endpoint AJAX para operaciones con recursos de la biblioteca
 * Soporta: listar, buscar, filtrar, obtener por ID, estadísticas
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');

require_once(__DIR__ . '/../repositories/bucv_1cc2s4B3.php');

try {
    $repo = new BucvRepository();
    $accion = $_GET['accion'] ?? $_POST['accion'] ?? 'listar';

    switch ($accion) {
        case 'listar':
        case 'buscar':
            // Parámetros de búsqueda
            $busqueda = $_GET['q'] ?? $_POST['q'] ?? '';

            // Filtros
            $filtros = [];

            // Tipo de recurso (puede ser múltiple)
            if (!empty($_GET['tipo_recurso'])) {
                $filtros['tipo_recurso'] = is_array($_GET['tipo_recurso'])
                    ? $_GET['tipo_recurso']
                    : explode(',', $_GET['tipo_recurso']);
            }

            // Pensamiento (puede ser múltiple)
            if (!empty($_GET['pensamiento'])) {
                $filtros['pensamiento'] = is_array($_GET['pensamiento'])
                    ? $_GET['pensamiento']
                    : explode(',', $_GET['pensamiento']);
            }

            // Idioma (puede ser múltiple)
            if (!empty($_GET['idioma'])) {
                $filtros['idioma'] = is_array($_GET['idioma'])
                    ? $_GET['idioma']
                    : explode(',', $_GET['idioma']);
            }

            // Rango de años
            if (!empty($_GET['año_min'])) {
                $filtros['año_min'] = intval($_GET['año_min']);
            }
            if (!empty($_GET['año_max'])) {
                $filtros['año_max'] = intval($_GET['año_max']);
            }

            // Autor (puede ser múltiple)
            if (!empty($_GET['autor'])) {
                $filtros['autor'] = is_array($_GET['autor'])
                    ? $_GET['autor']
                    : explode(',', $_GET['autor']);
            }

            // Ordenamiento
            if (!empty($_GET['orden'])) {
                $filtros['orden'] = $_GET['orden'];
            }

            $recursos = $repo->obtenerRecursos($busqueda, $filtros);

            echo json_encode([
                'success' => true,
                'data' => $recursos,
                'total' => count($recursos)
            ]);
            break;

        case 'detalle':
            $id = intval($_GET['id'] ?? $_POST['id'] ?? 0);

            if ($id <= 0) {
                throw new Exception('ID de recurso inválido');
            }

            $recurso = $repo->obtenerRecursoPorId($id);

            if (!$recurso) {
                throw new Exception('Recurso no encontrado');
            }

            echo json_encode([
                'success' => true,
                'data' => $recurso
            ]);
            break;

        case 'relacionados':
            $id = intval($_GET['id'] ?? $_POST['id'] ?? 0);
            $limite = intval($_GET['limite'] ?? 4);

            if ($id <= 0) {
                throw new Exception('ID de recurso inválido');
            }

            $relacionados = $repo->obtenerRecursosRelacionados($id, $limite);

            echo json_encode([
                'success' => true,
                'data' => $relacionados
            ]);
            break;

        case 'estadisticas':
            $stats = $repo->obtenerEstadisticas();

            echo json_encode([
                'success' => true,
                'data' => $stats
            ]);
            break;

        case 'idiomas':
            $idiomas = $repo->obtenerIdiomas();

            echo json_encode([
                'success' => true,
                'data' => $idiomas
            ]);
            break;

        case 'pensamientos':
            $pensamientos = $repo->obtenerPensamientos();

            echo json_encode([
                'success' => true,
                'data' => $pensamientos
            ]);
            break;

        case 'generos':
            $generos = $repo->obtenerGeneros();

            echo json_encode([
                'success' => true,
                'data' => $generos
            ]);
            break;

        case 'paises':
            $paises = $repo->obtenerPaises();

            echo json_encode([
                'success' => true,
                'data' => $paises
            ]);
            break;

        case 'autores':
            $autores = $repo->obtenerAutores();

            echo json_encode([
                'success' => true,
                'data' => $autores
            ]);
            break;

        case 'obtener_para_edicion':
            $id = intval($_GET['id'] ?? $_POST['id'] ?? 0);

            if ($id <= 0) {
                throw new Exception('ID de recurso inválido');
            }

            $recurso = $repo->obtenerRecursoParaEdicion($id);

            if (!$recurso) {
                throw new Exception('Recurso no encontrado');
            }

            echo json_encode([
                'success' => true,
                'data' => $recurso
            ]);
            break;

        case 'actualizar':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido');
            }

            $id = intval($_POST['id'] ?? 0);

            if ($id <= 0) {
                throw new Exception('ID de recurso inválido');
            }

            $datos = [
                'titulo' => $_POST['titulo'] ?? '',
                'año' => intval($_POST['año'] ?? 0),
                'resumen' => $_POST['resumen'] ?? '',
                'isbn_issn_doi' => $_POST['isbn_issn_doi'] ?? '',
                'id_tipo_recursos' => intval($_POST['id_tipo_recursos'] ?? 0),
                'id_autor' => intval($_POST['id_autor'] ?? 0),
                'id_idiomas' => intval($_POST['id_idiomas'] ?? 0),
                'id_pensamientos' => intval($_POST['id_pensamientos'] ?? 0),
                'id_pais' => intval($_POST['id_pais'] ?? 0)
            ];

            if (empty($datos['titulo'])) {
                throw new Exception('El título es requerido');
            }

            $repo->actualizarRecurso($id, $datos);

            echo json_encode([
                'success' => true,
                'message' => 'Recurso actualizado correctamente'
            ]);
            break;

        default:
            throw new Exception('Acción no válida');
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
