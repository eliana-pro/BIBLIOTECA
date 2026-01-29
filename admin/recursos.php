<?php
/**
 * recursos.php
 * Lista de recursos con opciones de gestión
 */

$page_title = 'Gestión de Recursos - Admin Biblioteca UNICAB';
$page_header = 'Gestión de Recursos';

require_once(__DIR__ . '/components/bucv_admin_auth.php');
require_once(__DIR__ . '/../repositories/bucv_1cc2s4B3.php');

$repo = new BucvRepository();
$mensaje = '';
$tipoMensaje = '';

// Procesar eliminación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar'])) {
    $id = intval($_POST['id'] ?? 0);
    if ($id > 0) {
        try {
            $repo->eliminarRecurso($id);
            $mensaje = 'Recurso eliminado correctamente';
            $tipoMensaje = 'success';
        } catch (Exception $e) {
            $mensaje = 'Error al eliminar: ' . $e->getMessage();
            $tipoMensaje = 'error';
        }
    }
}

// Búsqueda y filtros
$busqueda = trim($_GET['q'] ?? '');
$filtros = [];

// Obtener recursos
$recursos = $repo->obtenerRecursos($busqueda, $filtros);

// Paginación
$pagina = max(1, intval($_GET['pagina'] ?? 1));
$porPagina = 15;
$total = count($recursos);
$totalPaginas = ceil($total / $porPagina);
$offset = ($pagina - 1) * $porPagina;
$recursosPagina = array_slice($recursos, $offset, $porPagina);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'components/bucv_admin_head.php'; ?>
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'components/bucv_admin_sidebar.php'; ?>

        <main class="admin-main">
            <?php include 'components/bucv_admin_header.php'; ?>

            <div class="admin-content">
                <?php if ($mensaje): ?>
                    <div class="alert alert-<?php echo $tipoMensaje; ?>">
                        <?php echo htmlspecialchars($mensaje); ?>
                    </div>
                <?php endif; ?>

                <div class="admin-table-container">
                    <div class="admin-table-header">
                        <h2>Recursos (<?php echo $total; ?>)</h2>
                        <div class="admin-table-actions">
                            <form method="GET" class="admin-search">
                                <span>&#128269;</span>
                                <input type="text" name="q" placeholder="Buscar recurso..."
                                       value="<?php echo htmlspecialchars($busqueda); ?>">
                            </form>
                            <a href="recursos_form.php" class="btn-admin success">+ Nuevo Recurso</a>
                        </div>
                    </div>

                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Título</th>
                                <th>Autor</th>
                                <th>Tipo</th>
                                <th>Año</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recursosPagina)): ?>
                                <?php foreach ($recursosPagina as $recurso): ?>
                                <tr>
                                    <td><?php echo $recurso['id_recursos']; ?></td>
                                    <td>
                                        <a href="../pages/bucv_detalle_recurso.php?id=<?php echo $recurso['id_recursos']; ?>"
                                           target="_blank" style="color: var(--color-primary); text-decoration: none;">
                                            <?php echo htmlspecialchars(mb_substr($recurso['titulo'], 0, 50)); ?>
                                            <?php if (mb_strlen($recurso['titulo']) > 50) echo '...'; ?>
                                        </a>
                                    </td>
                                    <td><?php echo htmlspecialchars($recurso['autor'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($recurso['tipo_recurso'] ?? 'N/A'); ?></td>
                                    <td><?php echo $recurso['año'] ?? 'S/F'; ?></td>
                                    <td>
                                        <div class="table-actions">
                                            <a href="recursos_form.php?id=<?php echo $recurso['id_recursos']; ?>"
                                               class="btn-icon edit" title="Editar">&#9998;</a>
                                            <form method="POST" style="display: inline;"
                                                  onsubmit="return confirm('¿Está seguro de eliminar este recurso?');">
                                                <input type="hidden" name="id" value="<?php echo $recurso['id_recursos']; ?>">
                                                <button type="submit" name="eliminar" class="btn-icon delete" title="Eliminar">&#128465;</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 40px;">
                                        <?php if ($busqueda): ?>
                                            No se encontraron recursos con "<?php echo htmlspecialchars($busqueda); ?>"
                                        <?php else: ?>
                                            No hay recursos registrados
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <?php if ($totalPaginas > 1): ?>
                    <div class="admin-pagination">
                        <?php if ($pagina > 1): ?>
                            <a href="?pagina=<?php echo $pagina - 1; ?>&q=<?php echo urlencode($busqueda); ?>">
                                <button>&laquo;</button>
                            </a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                            <?php if ($i == $pagina): ?>
                                <button class="active"><?php echo $i; ?></button>
                            <?php else: ?>
                                <a href="?pagina=<?php echo $i; ?>&q=<?php echo urlencode($busqueda); ?>">
                                    <button><?php echo $i; ?></button>
                                </a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($pagina < $totalPaginas): ?>
                            <a href="?pagina=<?php echo $pagina + 1; ?>&q=<?php echo urlencode($busqueda); ?>">
                                <button>&raquo;</button>
                            </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    <?php include 'components/bucv_admin_scripts.php'; ?>
</body>
</html>
