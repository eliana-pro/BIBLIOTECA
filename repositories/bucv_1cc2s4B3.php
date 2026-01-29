<?php
/**
 * bucv_1cc2s4B3.php
 * Clase principal para conexión y operaciones con la base de datos
 * de la Biblioteca UNICAB
 * Estructura basada en la BD real: tbl_recursos + tbl_detalle_recursos
 */

require_once(__DIR__ . '/../config/DotEnv.php');
(new \clases\DevCoder\DotEnv(__DIR__ . '/../.env'))->load();

class BucvRepository
{
    private $conn;

    public function __construct()
    {
        if (getenv('APP_ENV') == "local") {
            $this->conn = new mysqli(
                getenv('DB_HOST'),
                getenv('DB_USERNAME_L'),
                getenv('DB_PASSWORD_L'),
                getenv('DB_DATABASE'),
                getenv('DB_PORT')
            );
        } else {
            $this->conn = new mysqli(
                getenv('DB_HOST'),
                getenv('DB_USERNAME_P'),
                getenv('DB_PASSWORD_P'),
                getenv('DB_DATABASE')
            );
        }

        if ($this->conn->connect_error) {
            throw new Exception("Error de conexión: " . $this->conn->connect_error);
        }

        $this->conn->set_charset("utf8");
    }

    // ========================================
    // FUNCIONES DE RECURSOS
    // ========================================

    /**
     * Obtener todos los recursos con sus relaciones
     */
    public function obtenerRecursos($busqueda = '', $filtros = [])
    {
        $sql = "
            SELECT
                r.id_recursos,
                d.titulo,
                r.año,
                r.resumen,
                d.isbn_issn_doi,
                tr.nombre AS tipo_recurso,
                a.nombre AS autor,
                i.nombre AS idioma,
                p.nombre AS pensamiento,
                pa.nombre AS pais,
                r.fecha_creacion,
                r.fecha_modificacion
            FROM tbl_recursos r
            LEFT JOIN tbl_detalle_recursos d ON r.id_recursos = d.id_recursos
            LEFT JOIN tbl_tipo_recursos tr ON r.id_tipo_recursos = tr.id_tipo_recursos
            LEFT JOIN tbl_autores a ON r.id_autor = a.id_autor
            LEFT JOIN tbl_idiomas i ON d.id_idiomas = i.id_idiomas
            LEFT JOIN tbl_pensamientos p ON d.id_pensamientos = p.id_pensamientos
            LEFT JOIN tbl_paises pa ON d.id_pais = pa.id_pais
            WHERE 1=1
        ";

        $params = [];
        $types = '';

        // Búsqueda por texto
        if (!empty($busqueda)) {
            $sql .= " AND (
                d.titulo LIKE ?
                OR r.resumen LIKE ?
                OR d.isbn_issn_doi LIKE ?
                OR tr.nombre LIKE ?
                OR a.nombre LIKE ?
                OR p.nombre LIKE ?
            )";
            $busquedaParam = '%' . $busqueda . '%';
            for ($j = 0; $j < 6; $j++) {
                $params[] = $busquedaParam;
                $types .= 's';
            }
        }

        // Filtro por tipo de recurso
        if (!empty($filtros['tipo_recurso'])) {
            if (is_array($filtros['tipo_recurso'])) {
                $placeholders = implode(',', array_fill(0, count($filtros['tipo_recurso']), '?'));
                $sql .= " AND tr.nombre IN ($placeholders)";
                foreach ($filtros['tipo_recurso'] as $tipo) {
                    $params[] = $tipo;
                    $types .= 's';
                }
            } else {
                $sql .= " AND tr.nombre = ?";
                $params[] = $filtros['tipo_recurso'];
                $types .= 's';
            }
        }

        // Filtro por pensamiento
        if (!empty($filtros['pensamiento'])) {
            if (is_array($filtros['pensamiento'])) {
                $placeholders = implode(',', array_fill(0, count($filtros['pensamiento']), '?'));
                $sql .= " AND p.nombre IN ($placeholders)";
                foreach ($filtros['pensamiento'] as $pens) {
                    $params[] = $pens;
                    $types .= 's';
                }
            } else {
                $sql .= " AND d.id_pensamientos = ?";
                $params[] = $filtros['pensamiento'];
                $types .= 'i';
            }
        }

        // Filtro por idioma
        if (!empty($filtros['idioma'])) {
            if (is_array($filtros['idioma'])) {
                $placeholders = implode(',', array_fill(0, count($filtros['idioma']), '?'));
                $sql .= " AND i.nombre IN ($placeholders)";
                foreach ($filtros['idioma'] as $idioma) {
                    $params[] = $idioma;
                    $types .= 's';
                }
            } else {
                $sql .= " AND d.id_idiomas = ?";
                $params[] = $filtros['idioma'];
                $types .= 'i';
            }
        }

        // Filtro por autor
        if (!empty($filtros['autor'])) {
            if (is_array($filtros['autor'])) {
                $placeholders = implode(',', array_fill(0, count($filtros['autor']), '?'));
                $sql .= " AND a.nombre IN ($placeholders)";
                foreach ($filtros['autor'] as $autor) {
                    $params[] = $autor;
                    $types .= 's';
                }
            } else {
                $sql .= " AND a.nombre = ?";
                $params[] = $filtros['autor'];
                $types .= 's';
            }
        }

        // Filtro por rango de años
        if (!empty($filtros['año_min'])) {
            $sql .= " AND r.año >= ?";
            $params[] = $filtros['año_min'];
            $types .= 'i';
        }
        if (!empty($filtros['año_max'])) {
            $sql .= " AND r.año <= ?";
            $params[] = $filtros['año_max'];
            $types .= 'i';
        }

        // Ordenamiento
        $orden = $filtros['orden'] ?? 'relevancia';
        if ($orden === 'asc') {
            $sql .= " ORDER BY r.año ASC";
        } elseif ($orden === 'desc') {
            $sql .= " ORDER BY r.año DESC";
        } else {
            $sql .= " ORDER BY r.fecha_creacion DESC";
        }

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error en prepare: " . $this->conn->error);
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $recursos = [];
        while ($row = $result->fetch_assoc()) {
            $recursos[] = $row;
        }

        return $recursos;
    }

    /**
     * Obtener un recurso por ID
     */
    public function obtenerRecursoPorId($id)
    {
        $sql = "
            SELECT
                r.id_recursos,
                d.titulo,
                r.año,
                r.resumen,
                d.isbn_issn_doi,
                tr.nombre AS tipo_recurso,
                a.nombre AS autor,
                i.nombre AS idioma,
                p.nombre AS pensamiento,
                pa.nombre AS pais,
                r.fecha_creacion,
                r.fecha_modificacion
            FROM tbl_recursos r
            LEFT JOIN tbl_detalle_recursos d ON r.id_recursos = d.id_recursos
            LEFT JOIN tbl_tipo_recursos tr ON r.id_tipo_recursos = tr.id_tipo_recursos
            LEFT JOIN tbl_autores a ON r.id_autor = a.id_autor
            LEFT JOIN tbl_idiomas i ON d.id_idiomas = i.id_idiomas
            LEFT JOIN tbl_pensamientos p ON d.id_pensamientos = p.id_pensamientos
            LEFT JOIN tbl_paises pa ON d.id_pais = pa.id_pais
            WHERE r.id_recursos = ?
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    /**
     * Obtener estadísticas de recursos
     */
    public function obtenerEstadisticas()
    {
        $stats = [];
        $stats['por_tipo'] = [];

        // Total por tipo de recurso
        $sql = "SELECT tr.nombre AS tipo_recurso, COUNT(*) as total
                FROM tbl_recursos r
                INNER JOIN tbl_tipo_recursos tr ON r.id_tipo_recursos = tr.id_tipo_recursos
                GROUP BY r.id_tipo_recursos, tr.nombre
                ORDER BY tr.nombre";
        $result = $this->conn->query($sql);

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $stats['por_tipo'][$row['tipo_recurso']] = (int)$row['total'];
            }
        }

        // Total general
        $sql = "SELECT COUNT(*) as total FROM tbl_recursos";
        $result = $this->conn->query($sql);
        if ($result) {
            $stats['total'] = (int)$result->fetch_assoc()['total'];
        } else {
            $stats['total'] = 0;
        }

        return $stats;
    }

    /**
     * Obtener recursos relacionados por pensamiento
     */
    public function obtenerRecursosRelacionados($idRecurso, $limite = 4)
    {
        $sql = "
            SELECT
                r.id_recursos,
                d.titulo,
                r.año,
                tr.nombre AS tipo_recurso,
                p.nombre AS pensamiento
            FROM tbl_recursos r
            LEFT JOIN tbl_detalle_recursos d ON r.id_recursos = d.id_recursos
            LEFT JOIN tbl_tipo_recursos tr ON r.id_tipo_recursos = tr.id_tipo_recursos
            LEFT JOIN tbl_pensamientos p ON d.id_pensamientos = p.id_pensamientos
            WHERE d.id_pensamientos = (
                SELECT id_pensamientos FROM tbl_detalle_recursos WHERE id_recursos = ?
            )
            AND r.id_recursos != ?
            LIMIT ?
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('iii', $idRecurso, $idRecurso, $limite);
        $stmt->execute();
        $result = $stmt->get_result();

        $recursos = [];
        while ($row = $result->fetch_assoc()) {
            $recursos[] = $row;
        }

        return $recursos;
    }

    /**
     * Obtener listado de idiomas
     */
    public function obtenerIdiomas()
    {
        $sql = "SELECT id_idiomas, nombre FROM tbl_idiomas ORDER BY nombre";
        $result = $this->conn->query($sql);

        $idiomas = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $idiomas[] = $row;
            }
        }

        return $idiomas;
    }

    /**
     * Obtener listado de pensamientos
     */
    public function obtenerPensamientos()
    {
        $sql = "SELECT id_pensamientos, nombre FROM tbl_pensamientos ORDER BY nombre";
        $result = $this->conn->query($sql);

        $pensamientos = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $pensamientos[] = $row;
            }
        }

        return $pensamientos;
    }

    /**
     * Obtener listado de géneros/tipos de recurso
     */
    public function obtenerGeneros()
    {
        $sql = "SELECT id_tipo_recursos, nombre FROM tbl_tipo_recursos ORDER BY nombre";
        $result = $this->conn->query($sql);

        $generos = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $generos[] = $row;
            }
        }

        return $generos;
    }

    /**
     * Obtener listado de países
     */
    public function obtenerPaises()
    {
        $sql = "SELECT id_pais, nombre FROM tbl_paises ORDER BY nombre";
        $result = $this->conn->query($sql);

        $paises = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $paises[] = $row;
            }
        }

        return $paises;
    }

    /**
     * Obtener listado de autores
     */
    public function obtenerAutores()
    {
        $sql = "SELECT id_autor, nombre FROM tbl_autores ORDER BY nombre";
        $result = $this->conn->query($sql);

        $autores = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $autores[] = $row;
            }
        }

        return $autores;
    }

    // ========================================
    // FUNCIONES DE USUARIOS
    // ========================================

    public function obtenerUsuarios()
    {
        $sql = "SELECT * FROM tbl_usuarios";
        $result = $this->conn->query($sql);

        $usuarios = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $usuarios[] = $row;
            }
        }

        return $usuarios;
    }

    /**
     * Crear nuevo usuario
     */
    public function crearUsuario($idRol, $nombre, $correo, $passwordHash)
    {
        $sql = "INSERT INTO tbl_usuarios (id_rol, nombre, correo, contraseña_hash) 
                VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('isss', $idRol, $nombre, $correo, $passwordHash);
        $stmt->execute();

        return $this->conn->insert_id;
    }

    // ========================================
    // FUNCIONES DE EDICIÓN DE RECURSOS
    // ========================================

    /**
     * Obtener recurso completo con IDs para edición
     */
    public function obtenerRecursoParaEdicion($id)
    {
        $sql = "
            SELECT
                r.id_recursos,
                d.titulo,
                r.año,
                r.resumen,
                d.isbn_issn_doi,
                r.id_tipo_recursos,
                r.id_autor,
                d.id_idiomas,
                d.id_pensamientos,
                d.id_pais
            FROM tbl_recursos r
            LEFT JOIN tbl_detalle_recursos d ON r.id_recursos = d.id_recursos
            WHERE r.id_recursos = ?
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    /**
     * Actualizar recurso
     */
    public function actualizarRecurso($id, $datos)
    {
        $this->conn->begin_transaction();

        try {
            // Actualizar tbl_recursos
            $sql = "UPDATE tbl_recursos SET
                    año = ?,
                    resumen = ?,
                    id_tipo_recursos = ?,
                    id_autor = ?,
                    fecha_modificacion = NOW()
                    WHERE id_recursos = ?";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param(
                'isiii',
                $datos['año'],
                $datos['resumen'],
                $datos['id_tipo_recursos'],
                $datos['id_autor'],
                $id
            );
            $stmt->execute();

            // Actualizar tbl_detalle_recursos
            $sql = "UPDATE tbl_detalle_recursos SET
                    titulo = ?,
                    isbn_issn_doi = ?,
                    id_idiomas = ?,
                    id_pensamientos = ?,
                    id_pais = ?
                    WHERE id_recursos = ?";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param(
                'ssiiii',
                $datos['titulo'],
                $datos['isbn_issn_doi'],
                $datos['id_idiomas'],
                $datos['id_pensamientos'],
                $datos['id_pais'],
                $id
            );
            $stmt->execute();

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    /**
     * Crear nuevo recurso
     */
    public function crearRecurso($datos)
    {
        $this->conn->begin_transaction();

        try {
            // Insertar en tbl_recursos
            $sql = "INSERT INTO tbl_recursos (año, resumen, id_tipo_recursos, id_autor, fecha_creacion, fecha_modificacion)
                    VALUES (?, ?, ?, ?, NOW(), NOW())";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param(
                'isii',
                $datos['año'],
                $datos['resumen'],
                $datos['id_tipo_recursos'],
                $datos['id_autor']
            );
            $stmt->execute();

            $idRecurso = $this->conn->insert_id;

            // Insertar en tbl_detalle_recursos
            $sql = "INSERT INTO tbl_detalle_recursos (id_recursos, titulo, isbn_issn_doi, id_idiomas, id_pensamientos, id_pais)
                    VALUES (?, ?, ?, ?, ?, ?)";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param(
                'issiii',
                $idRecurso,
                $datos['titulo'],
                $datos['isbn_issn_doi'],
                $datos['id_idiomas'],
                $datos['id_pensamientos'],
                $datos['id_pais']
            );
            $stmt->execute();

            $this->conn->commit();
            return $idRecurso;

        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    /**
     * Eliminar recurso
     */
    public function eliminarRecurso($id)
    {
        $this->conn->begin_transaction();

        try {
            // Eliminar de tbl_detalle_recursos primero
            $sql = "DELETE FROM tbl_detalle_recursos WHERE id_recursos = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('i', $id);
            $stmt->execute();

            // Eliminar de tbl_recursos
            $sql = "DELETE FROM tbl_recursos WHERE id_recursos = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('i', $id);
            $stmt->execute();

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    // ========================================
    // FUNCIONES DE AUTORES (CRUD)
    // ========================================

    /**
     * Obtener autor por ID
     */
    public function obtenerAutorPorId($id)
    {
        $sql = "SELECT id_autor, nombre FROM tbl_autores WHERE id_autor = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    /**
     * Crear nuevo autor
     */
    public function crearAutor($nombre)
    {
        $sql = "INSERT INTO tbl_autores (nombre) VALUES (?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $nombre);
        $stmt->execute();

        return $this->conn->insert_id;
    }

    /**
     * Actualizar autor
     */
    public function actualizarAutor($id, $nombre)
    {
        $sql = "UPDATE tbl_autores SET nombre = ? WHERE id_autor = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('si', $nombre, $id);
        $stmt->execute();

        return $stmt->affected_rows > 0;
    }

    /**
     * Eliminar autor
     */
    public function eliminarAutor($id)
    {
        // Verificar que no tenga recursos asociados
        $count = $this->contarRecursosPorAutor($id);
        if ($count > 0) {
            throw new Exception("No se puede eliminar: el autor tiene $count recurso(s) asociado(s)");
        }

        $sql = "DELETE FROM tbl_autores WHERE id_autor = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();

        return $stmt->affected_rows > 0;
    }

    /**
     * Contar recursos por autor
     */
    public function contarRecursosPorAutor($idAutor)
    {
        $sql = "SELECT COUNT(*) as total FROM tbl_recursos WHERE id_autor = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $idAutor);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return (int)$row['total'];
    }

    /**
     * Obtener autores con conteo de recursos
     */
    public function obtenerAutoresConConteo()
    {
        $sql = "SELECT a.id_autor, a.nombre, COUNT(r.id_recursos) as total_recursos
                FROM tbl_autores a
                LEFT JOIN tbl_recursos r ON a.id_autor = r.id_autor
                GROUP BY a.id_autor
                ORDER BY a.nombre";
        $result = $this->conn->query($sql);

        $autores = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $autores[] = $row;
            }
        }

        return $autores;
    }

    // ========================================
    // FUNCIONES DE AUTENTICACIÓN
    // ========================================

    /**
     * Validar usuario por correo y contraseña
     */
    public function validarUsuario($correo, $password)
    {
        $sql = "SELECT id_usuarios, id_rol, nombre, correo, contraseña_hash FROM tbl_usuarios WHERE correo = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $correo);
        $stmt->execute();
        $result = $stmt->get_result();

        $usuario = $result->fetch_assoc();

        if ($usuario && password_verify($password, $usuario['contraseña_hash'])) {
            unset($usuario['contraseña_hash']);
            return $usuario;
        }

        return null;
    }

    /**
     * Obtener usuario por ID
     */
    public function obtenerUsuarioPorId($id)
    {
        $sql = "SELECT id_usuarios, id_rol, nombre, correo FROM tbl_usuarios WHERE id_usuarios = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    // ========================================
    // FUNCIONES DE ESTADÍSTICAS ADMIN
    // ========================================

    /**
     * Obtener estadísticas completas para el dashboard admin
     */
    public function obtenerEstadisticasAdmin()
    {
        $stats = [];

        // Total de recursos
        $sql = "SELECT COUNT(*) as total FROM tbl_recursos";
        $result = $this->conn->query($sql);
        $stats['total_recursos'] = (int)$result->fetch_assoc()['total'];

        // Total de autores
        $sql = "SELECT COUNT(*) as total FROM tbl_autores";
        $result = $this->conn->query($sql);
        $stats['total_autores'] = (int)$result->fetch_assoc()['total'];

        // Total por tipo de recurso
        $sql = "SELECT tr.nombre, COUNT(*) as total
                FROM tbl_recursos r
                LEFT JOIN tbl_tipo_recursos tr ON r.id_tipo_recursos = tr.id_tipo_recursos
                GROUP BY r.id_tipo_recursos
                ORDER BY total DESC";
        $result = $this->conn->query($sql);
        $stats['por_tipo'] = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $stats['por_tipo'][] = $row;
            }
        }

        // Recursos recientes (últimos 5)
        $sql = "SELECT r.id_recursos, d.titulo, tr.nombre as tipo_recurso, r.fecha_creacion
                FROM tbl_recursos r
                LEFT JOIN tbl_detalle_recursos d ON r.id_recursos = d.id_recursos
                LEFT JOIN tbl_tipo_recursos tr ON r.id_tipo_recursos = tr.id_tipo_recursos
                ORDER BY r.fecha_creacion DESC
                LIMIT 5";
        $result = $this->conn->query($sql);
        $stats['recursos_recientes'] = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $stats['recursos_recientes'][] = $row;
            }
        }

        // Autores más activos (top 5)
        $sql = "SELECT a.id_autor, a.nombre, COUNT(r.id_recursos) as total_recursos
                FROM tbl_autores a
                LEFT JOIN tbl_recursos r ON a.id_autor = r.id_autor
                GROUP BY a.id_autor
                HAVING total_recursos > 0
                ORDER BY total_recursos DESC
                LIMIT 5";
        $result = $this->conn->query($sql);
        $stats['autores_activos'] = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $stats['autores_activos'][] = $row;
            }
        }

        return $stats;
    }
}
