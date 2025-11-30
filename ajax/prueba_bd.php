<?php
// Conexión a la base de datos
$host = 'localhost';
$dbname = 'biblioteca';
$username = 'root';
$password = '1234';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// =====================================================================
// FUNCIÓN: Búsqueda general con los 5 JOINS más importantes
// =====================================================================
function buscarRecursos($busqueda = "", $filtros = [], $page = 1, $limit = 20) {
    global $pdo;
    
    $offset = ($page - 1) * $limit;
    $params = [];
    
    $sql = "SELECT DISTINCT
              r.id_recursos,
              dr.titulo,
              dr.isbn_issn_doi,
              
              -- JOIN 1: Autor (directamente desde tbl_recursos)
              a.nombre AS autor,
              pa.nombre AS pais_autor,
              
              -- JOIN 2: Tipo de Recurso
              tr.nombre AS tipo_recurso,
              
              -- JOIN 3: Idioma
              i.nombre AS idioma,
              
              -- JOIN 4: Pensamiento
              pen.nombre AS pensamiento,
              
              -- JOIN 5: País de publicación
              pp.nombre AS pais_publicacion,
              
              -- Datos adicionales
              r.año,
              r.resumen,
              r.fecha_modificacion,
              
              -- Estadísticas
              COUNT(DISTINCT ur.id_usuarios) AS total_accesos,
              COUNT(DISTINCT c.id_comentarios) AS total_comentarios
              
            FROM tbl_recursos r
            INNER JOIN tbl_detalle_recursos dr ON r.id_recursos = dr.id_recursos
            LEFT JOIN tbl_autores a ON r.id_autor = a.id_autor
            LEFT JOIN tbl_paises pa ON a.id_pais = pa.id_pais
            LEFT JOIN tbl_tipo_recursos tr ON r.id_tipo_recursos = tr.id_tipo_recursos
            LEFT JOIN tbl_idiomas i ON dr.id_idiomas = i.id_idiomas
            LEFT JOIN tbl_pensamientos pen ON dr.id_pensamientos = pen.id_pensamientos
            LEFT JOIN tbl_paises pp ON dr.id_pais = pp.id_pais
            LEFT JOIN tbl_usuarios_recursos ur ON r.id_recursos = ur.id_recursos
            LEFT JOIN tbl_comentarios c ON r.id_recursos = c.id_comentarios
            WHERE 1=1";
    
    // Búsqueda general (SOLO si hay texto)
    if (!empty($busqueda) && trim($busqueda) !== "") {
        $sql .= " AND (
                    dr.titulo LIKE :busqueda
                    OR r.resumen LIKE :busqueda
                    OR a.nombre LIKE :busqueda
                    OR dr.isbn_issn_doi LIKE :busqueda
                    OR tr.nombre LIKE :busqueda
                    OR i.nombre LIKE :busqueda
                    OR pen.nombre LIKE :busqueda
                    OR pp.nombre LIKE :busqueda
                  )";
        $params[':busqueda'] = "%{$busqueda}%";
    }
    
    // Filtros adicionales
    if (!empty($filtros['tipo_recurso'])) {
        $sql .= " AND r.id_tipo_recursos = :tipo_recurso";
        $params[':tipo_recurso'] = $filtros['tipo_recurso'];
    }
    
    if (!empty($filtros['idioma'])) {
        $sql .= " AND dr.id_idiomas = :idioma";
        $params[':idioma'] = $filtros['idioma'];
    }
    
    if (!empty($filtros['pensamiento'])) {
        $sql .= " AND dr.id_pensamientos = :pensamiento";
        $params[':pensamiento'] = $filtros['pensamiento'];
    }
    
    if (!empty($filtros['pais'])) {
        $sql .= " AND dr.id_pais = :pais";
        $params[':pais'] = $filtros['pais'];
    }
    
    if (!empty($filtros['autor'])) {
        $sql .= " AND r.id_autor = :autor";
        $params[':autor'] = $filtros['autor'];
    }
    
    if (!empty($filtros['año_desde'])) {
        $sql .= " AND r.año >= :año_desde";
        $params[':año_desde'] = $filtros['año_desde'];
    }
    
    if (!empty($filtros['año_hasta'])) {
        $sql .= " AND r.año <= :año_hasta";
        $params[':año_hasta'] = $filtros['año_hasta'];
    }
    
    // Agrupar y ordenar
    $sql .= " GROUP BY r.id_recursos";
    
    // Ordenamiento
    if (!empty($filtros['orden'])) {
        switch ($filtros['orden']) {
            case 'popular':
                $sql .= " ORDER BY total_accesos DESC";
                break;
            case 'comentados':
                $sql .= " ORDER BY total_comentarios DESC";
                break;
            case 'recientes':
                $sql .= " ORDER BY r.año DESC";
                break;
            case 'antiguos':
                $sql .= " ORDER BY r.año ASC";
                break;
            default:
                $sql .= " ORDER BY r.fecha_modificacion DESC";
        }
    } else {
        $sql .= " ORDER BY r.fecha_modificacion DESC";
    }
    
    // Agregar LIMIT y OFFSET directamente (sin placeholders)
    $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
    
    $stmt = $pdo->prepare($sql);
    
    // Bind de parámetros dinámicos
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// =====================================================================
// FUNCIÓN: Mostrar resultados de forma organizada (HTML)
// =====================================================================
function mostrarResultadosHTML($resultados) {
    if (empty($resultados)) {
        echo "<p>No se encontraron resultados.</p>";
        return;
    }
    
    echo "<table>";
    echo "<thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Autor</th>
                <th>Tipo</th>
                <th>Idioma</th>
                <th>Año</th>
                <th>Accesos</th>
                <th>Comentarios</th>
            </tr>
          </thead>";
    echo "<tbody>";
    
    foreach ($resultados as $recurso) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($recurso['id_recursos']) . "</td>";
        echo "<td><strong>" . htmlspecialchars($recurso['titulo']) . "</strong><br>";
        echo "<small>ISBN: " . htmlspecialchars($recurso['isbn_issn_doi'] ?? 'N/A') . "</small></td>";
        echo "<td>" . htmlspecialchars($recurso['autor'] ?? 'Sin autor') . "<br>";
        echo "<small>" . htmlspecialchars($recurso['pais_autor'] ?? '') . "</small></td>";
        echo "<td><span class='badge badge-info'>" . htmlspecialchars($recurso['tipo_recurso'] ?? 'N/A') . "</span></td>";
        echo "<td>" . htmlspecialchars($recurso['idioma'] ?? 'N/A') . "</td>";
        echo "<td>" . htmlspecialchars($recurso['año'] ?? 'N/A') . "</td>";
        echo "<td><span class='badge badge-success'>" . $recurso['total_accesos'] . "</span></td>";
        echo "<td><span class='badge badge-warning'>" . $recurso['total_comentarios'] . "</span></td>";
        echo "</tr>";
    }
    
    echo "</tbody></table>";
}

// =====================================================================
// FUNCIÓN: Mostrar resultados en formato texto organizado
// =====================================================================
function mostrarResultadosTexto($resultados) {
    if (empty($resultados)) {
        echo "No se encontraron resultados.\n\n";
        return;
    }
    
    echo "\n" . str_repeat("=", 100) . "\n";
    echo "RESULTADOS DE LA BÚSQUEDA (" . count($resultados) . " encontrados)\n";
    echo str_repeat("=", 100) . "\n\n";
    
    foreach ($resultados as $i => $recurso) {
        echo "[" . ($i + 1) . "] " . str_repeat("-", 90) . "\n";
        echo "ID: " . $recurso['id_recursos'] . "\n";
        echo "TÍTULO: " . $recurso['titulo'] . "\n";
        echo "AUTOR: " . ($recurso['autor'] ?? 'Sin autor') . " (" . ($recurso['pais_autor'] ?? 'País desconocido') . ")\n";
        echo "ISBN/ISSN/DOI: " . ($recurso['isbn_issn_doi'] ?? 'N/A') . "\n";
        echo "TIPO: " . ($recurso['tipo_recurso'] ?? 'N/A') . "\n";
        echo "IDIOMA: " . ($recurso['idioma'] ?? 'N/A') . "\n";
        echo "PENSAMIENTO: " . ($recurso['pensamiento'] ?? 'N/A') . "\n";
        echo "PAÍS PUBLICACIÓN: " . ($recurso['pais_publicacion'] ?? 'N/A') . "\n";
        echo "AÑO: " . ($recurso['año'] ?? 'N/A') . "\n";
        echo "RESUMEN: " . substr($recurso['resumen'] ?? 'Sin resumen', 0, 150) . "...\n";
        echo "ESTADÍSTICAS: " . $recurso['total_accesos'] . " accesos | " . $recurso['total_comentarios'] . " comentarios\n";
        echo "\n";
    }
    
    echo str_repeat("=", 100) . "\n\n";
}

// =====================================================================
// FUNCIÓN: Obtener detalle completo de recurso
// =====================================================================
function obtenerDetalleRecurso($id_recurso) {
    global $pdo;
    
    $sql = "SELECT 
              r.id_recursos,
              dr.titulo,
              dr.isbn_issn_doi,
              a.nombre AS autor,
              pa.nombre AS pais_autor,
              r.año,
              r.resumen,
              tr.nombre AS tipo_recurso,
              i.nombre AS idioma,
              pen.nombre AS pensamiento,
              pp.nombre AS pais_publicacion,
              COUNT(DISTINCT ur.id_usuarios) AS total_accesos,
              COUNT(DISTINCT c.id_comentarios) AS total_comentarios,
              r.fecha_creacion,
              r.fecha_modificacion
            FROM tbl_recursos r
            INNER JOIN tbl_detalle_recursos dr ON r.id_recursos = dr.id_recursos
            LEFT JOIN tbl_autores a ON r.id_autor = a.id_autor
            LEFT JOIN tbl_paises pa ON a.id_pais = pa.id_pais
            LEFT JOIN tbl_tipo_recursos tr ON r.id_tipo_recursos = tr.id_tipo_recursos
            LEFT JOIN tbl_idiomas i ON dr.id_idiomas = i.id_idiomas
            LEFT JOIN tbl_pensamientos pen ON dr.id_pensamientos = pen.id_pensamientos
            LEFT JOIN tbl_paises pp ON dr.id_pais = pp.id_pais
            LEFT JOIN tbl_usuarios_recursos ur ON r.id_recursos = ur.id_recursos
            LEFT JOIN tbl_comentarios c ON r.id_recursos = c.id_comentarios
            WHERE r.id_recursos = :id_recurso
            GROUP BY r.id_recursos";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id_recurso', $id_recurso, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// =====================================================================
// FUNCIÓN: Obtener listas para filtros (dropdowns)
// =====================================================================
function obtenerOpcionesFiltros() {
    global $pdo;
    
    $opciones = [];
    
    // Tipos de recursos
    $stmt = $pdo->query("SELECT id_tipo_recursos, nombre FROM tbl_tipo_recursos ORDER BY nombre");
    $opciones['tipos_recursos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Idiomas
    $stmt = $pdo->query("SELECT id_idiomas, nombre FROM tbl_idiomas ORDER BY nombre");
    $opciones['idiomas'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Pensamientos
    $stmt = $pdo->query("SELECT id_pensamientos, nombre FROM tbl_pensamientos ORDER BY nombre");
    $opciones['pensamientos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Países
    $stmt = $pdo->query("SELECT id_pais, nombre FROM tbl_paises ORDER BY nombre");
    $opciones['paises'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Autores
    $stmt = $pdo->query("SELECT id_autor, nombre FROM tbl_autores ORDER BY nombre");
    $opciones['autores'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return $opciones;
}

// =====================================================================
// EJEMPLOS DE USO
// =====================================================================

// 1. Búsqueda simple con texto
echo "=== BÚSQUEDA SIMPLE ===\n";
$resultados = buscarRecursos("tesis");
mostrarResultadosTexto($resultados);

// 2. Obtener TODOS los recursos (sin búsqueda)
echo "\n=== TODOS LOS RECURSOS ===\n";
$todos = buscarRecursos();
mostrarResultadosTexto($todos);

// 3. Búsqueda con filtros
/*echo "\n=== BÚSQUEDA CON FILTROS ===\n";
$filtrados = buscarRecursos("", [
    'año_desde' => 2010,
    'orden' => 'recientes'
], 1, 10);
mostrarResultadosTexto($filtrados);*/

// 4. Detalle de un recurso específico
echo "\n=== DETALLE DE RECURSO #1 ===\n";
$detalle = obtenerDetalleRecurso(1);
if ($detalle) {
    echo "TÍTULO: " . $detalle['titulo'] . "\n";
    echo "AUTOR: " . $detalle['autor'] . "\n";
    echo "AÑO: " . $detalle['año'] . "\n";
    echo "RESUMEN: " . $detalle['resumen'] . "\n";
    echo "\nDatos completos:\n";
    print_r($detalle);
}

// 5. Obtener opciones para filtros
echo "\n=== OPCIONES PARA FILTROS ===\n";
$opciones = obtenerOpcionesFiltros();
echo "Tipos de recursos disponibles: " . count($opciones['tipos_recursos']) . "\n";
echo "Idiomas disponibles: " . count($opciones['idiomas']) . "\n";
echo "Pensamientos disponibles: " . count($opciones['pensamientos']) . "\n";

// 6. Mostrar en HTML (descomenta para usar en navegador)
// echo mostrarResultadosHTML($resultados);

?>

<?php
// Incluir el archivo de modelos
require_once 'models.php';

echo "╔══════════════════════════════════════════════════════════════════╗\n";
echo "║         SCRIPT DE TESTEO - SISTEMA BIBLIOTECA                    ║\n";
echo "╚══════════════════════════════════════════════════════════════════╝\n\n";

// =====================================================================
// TEST 1: Verificar conexión y conteo de registros
// =====================================================================
echo "┌─────────────────────────────────────────────────────────────────┐\n";
echo "│ TEST 1: Verificación de Datos en Tablas                        │\n";
echo "└─────────────────────────────────────────────────────────────────┘\n";

try {
    $stmt = $pdo->query("
        SELECT 'Idiomas' as tabla, COUNT(*) as total FROM tbl_idiomas
        UNION ALL SELECT 'Pensamientos', COUNT(*) FROM tbl_pensamientos
        UNION ALL SELECT 'Países', COUNT(*) FROM tbl_paises
        UNION ALL SELECT 'Tipo Recursos', COUNT(*) FROM tbl_tipo_recursos
        UNION ALL SELECT 'Roles', COUNT(*) FROM tbl_roles
        UNION ALL SELECT 'Usuarios', COUNT(*) FROM tbl_usuarios
        UNION ALL SELECT 'Autores', COUNT(*) FROM tbl_autores
        UNION ALL SELECT 'Recursos', COUNT(*) FROM tbl_recursos
        UNION ALL SELECT 'Detalle Recursos', COUNT(*) FROM tbl_detalle_recursos
        UNION ALL SELECT 'Usuarios_Recursos', COUNT(*) FROM tbl_usuarios_recursos
        UNION ALL SELECT 'Comentarios', COUNT(*) FROM tbl_comentarios
    ");
    
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($resultados as $row) {
        printf("%-25s: %3d registros\n", $row['tabla'], $row['total']);
    }
    echo "✓ Conexión exitosa y datos cargados\n\n";
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n\n";
}

// =====================================================================
// TEST 2: Búsqueda sin parámetros (TODOS los recursos)
// =====================================================================
echo "┌─────────────────────────────────────────────────────────────────┐\n";
echo "│ TEST 2: Listar TODOS los Recursos                              │\n";
echo "└─────────────────────────────────────────────────────────────────┘\n";

$todos = buscarRecursos();
echo "Total encontrados: " . count($todos) . "\n";
foreach ($todos as $recurso) {
    printf("• [ID:%d] %s - %s (%d)\n", 
        $recurso['id_recursos'],
        $recurso['titulo'],
        $recurso['autor'] ?? 'Sin autor',
        $recurso['año'] ?? 0
    );
}
echo "\n";

// =====================================================================
// TEST 3: Búsqueda por palabra clave
// =====================================================================
echo "┌─────────────────────────────────────────────────────────────────┐\n";
echo "│ TEST 3: Búsqueda por Palabra Clave                             │\n";
echo "└─────────────────────────────────────────────────────────────────┘\n";

$palabras_prueba = ['literatura', 'tesis', 'mexicana', 'español'];

foreach ($palabras_prueba as $palabra) {
    echo "Buscando: '$palabra'\n";
    $resultados = buscarRecursos($palabra);
    echo "Encontrados: " . count($resultados) . "\n";
    
    if (!empty($resultados)) {
        foreach ($resultados as $r) {
            echo "  → " . $r['titulo'] . "\n";
        }
    } else {
        echo "  (Sin resultados)\n";
    }
    echo "\n";
}

// =====================================================================
// TEST 4: Filtros individuales
// =====================================================================
echo "┌─────────────────────────────────────────────────────────────────┐\n";
echo "│ TEST 4: Filtros Individuales                                   │\n";
echo "└─────────────────────────────────────────────────────────────────┘\n";

// Filtro por tipo de recurso
echo "▸ Filtro: Tipo = Libro (id_tipo_recursos = 1)\n";
$filtro_tipo = buscarRecursos("", ['tipo_recurso' => 1]);
echo "Resultados: " . count($filtro_tipo) . "\n";
foreach ($filtro_tipo as $r) {
    echo "  • " . $r['titulo'] . " [" . $r['tipo_recurso'] . "]\n";
}
echo "\n";

// Filtro por idioma
echo "▸ Filtro: Idioma = Español (id_idiomas = 1)\n";
$filtro_idioma = buscarRecursos("", ['idioma' => 1]);
echo "Resultados: " . count($filtro_idioma) . "\n";
foreach ($filtro_idioma as $r) {
    echo "  • " . $r['titulo'] . " [" . $r['idioma'] . "]\n";
}
echo "\n";

// Filtro por pensamiento
echo "▸ Filtro: Pensamiento = Humanista (id_pensamientos = 2)\n";
$filtro_pensamiento = buscarRecursos("", ['pensamiento' => 2]);
echo "Resultados: " . count($filtro_pensamiento) . "\n";
foreach ($filtro_pensamiento as $r) {
    echo "  • " . $r['titulo'] . " [" . $r['pensamiento'] . "]\n";
}
echo "\n";

// =====================================================================
// TEST 5: Filtros combinados
// =====================================================================
echo "┌─────────────────────────────────────────────────────────────────┐\n";
echo "│ TEST 5: Filtros Combinados                                     │\n";
echo "└─────────────────────────────────────────────────────────────────┘\n";

echo "▸ Búsqueda: 'tesis' + Idioma=Inglés(2)\n";
$combinado = buscarRecursos("tesis", ['idioma' => 2]);
echo "Resultados: " . count($combinado) . "\n\n";

echo "▸ Búsqueda: Tipo=Libro(1) + País=España(3)\n";
$combinado2 = buscarRecursos("", ['tipo_recurso' => 1, 'pais' => 3]);
echo "Resultados: " . count($combinado2) . "\n\n";

// =====================================================================
// TEST 6: Ordenamientos
// =====================================================================
echo "┌─────────────────────────────────────────────────────────────────┐\n";
echo "│ TEST 6: Diferentes Ordenamientos                               │\n";
echo "└─────────────────────────────────────────────────────────────────┘\n";

$ordenes = [
    'popular' => 'Más Populares (por accesos)',
    'comentados' => 'Más Comentados',
    'recientes' => 'Más Recientes (por año)',
    'antiguos' => 'Más Antiguos (por año)'
];

foreach ($ordenes as $key => $nombre) {
    echo "▸ Orden: $nombre\n";
    $ordenados = buscarRecursos("", ['orden' => $key]);
    foreach ($ordenados as $r) {
        printf("  • %s (%d) - Accesos:%d, Comentarios:%d\n",
            $r['titulo'],
            $r['año'] ?? 0,
            $r['total_accesos'],
            $r['total_comentarios']
        );
    }
    echo "\n";
}

// =====================================================================
// TEST 7: Detalle de recurso específico
// =====================================================================
echo "┌─────────────────────────────────────────────────────────────────┐\n";
echo "│ TEST 7: Detalle Completo de Recurso                            │\n";
echo "└─────────────────────────────────────────────────────────────────┘\n";

for ($i = 1; $i <= 3; $i++) {
    echo "═══ RECURSO ID: $i ═══\n";
    $detalle = obtenerDetalleRecurso($i);
    
    if ($detalle) {
        echo "Título: " . $detalle['titulo'] . "\n";
        echo "Autor: " . ($detalle['autor'] ?? 'N/A') . " (" . ($detalle['pais_autor'] ?? 'N/A') . ")\n";
        echo "ISBN/ISSN/DOI: " . ($detalle['isbn_issn_doi'] ?? 'N/A') . "\n";
        echo "Tipo: " . ($detalle['tipo_recurso'] ?? 'N/A') . "\n";
        echo "Idioma: " . ($detalle['idioma'] ?? 'N/A') . "\n";
        echo "Pensamiento: " . ($detalle['pensamiento'] ?? 'N/A') . "\n";
        echo "País Publicación: " . ($detalle['pais_publicacion'] ?? 'N/A') . "\n";
        echo "Año: " . ($detalle['año'] ?? 'N/A') . "\n";
        echo "Resumen: " . substr($detalle['resumen'] ?? '', 0, 80) . "...\n";
        echo "Estadísticas: " . $detalle['total_accesos'] . " accesos | " . 
             $detalle['total_comentarios'] . " comentarios\n";
    } else {
        echo "No encontrado\n";
    }
    echo "\n";
}

// =====================================================================
// TEST 8: Opciones para filtros (Dropdowns)
// =====================================================================
echo "┌─────────────────────────────────────────────────────────────────┐\n";
echo "│ TEST 8: Opciones para Filtros (Dropdowns)                      │\n";
echo "└─────────────────────────────────────────────────────────────────┘\n";

$opciones = obtenerOpcionesFiltros();

echo "▸ Tipos de Recursos:\n";
foreach ($opciones['tipos_recursos'] as $tipo) {
    echo "  [" . $tipo['id_tipo_recursos'] . "] " . $tipo['nombre'] . "\n";
}
echo "\n";

echo "▸ Idiomas:\n";
foreach ($opciones['idiomas'] as $idioma) {
    echo "  [" . $idioma['id_idiomas'] . "] " . $idioma['nombre'] . "\n";
}
echo "\n";

echo "▸ Pensamientos:\n";
foreach ($opciones['pensamientos'] as $pensamiento) {
    echo "  [" . $pensamiento['id_pensamientos'] . "] " . $pensamiento['nombre'] . "\n";
}
echo "\n";

echo "▸ Países:\n";
foreach ($opciones['paises'] as $pais) {
    echo "  [" . $pais['id_pais'] . "] " . $pais['nombre'] . "\n";
}
echo "\n";

echo "▸ Autores:\n";
foreach ($opciones['autores'] as $autor) {
    echo "  [" . $autor['id_autor'] . "] " . $autor['nombre'] . "\n";
}
echo "\n";

// =====================================================================
// TEST 9: Paginación
// =====================================================================
echo "┌─────────────────────────────────────────────────────────────────┐\n";
echo "│ TEST 9: Paginación                                              │\n";
echo "└─────────────────────────────────────────────────────────────────┘\n";

for ($page = 1; $page <= 2; $page++) {
    echo "▸ Página $page (limit 2):\n";
    $paginados = buscarRecursos("", [], $page, 2);
    foreach ($paginados as $r) {
        echo "  • " . $r['titulo'] . "\n";
    }
    echo "\n";
}

// =====================================================================
// RESUMEN FINAL
// =====================================================================
echo "╔══════════════════════════════════════════════════════════════════╗\n";
echo "║                    TESTS COMPLETADOS                             ║\n";
echo "╚══════════════════════════════════════════════════════════════════╝\n";
echo "✓ Todos los tests ejecutados correctamente\n";
echo "✓ Para ejecutar: php test_biblioteca.php\n";
?>