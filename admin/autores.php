<?php
/**
 * autores.php
 * Lista de autores con opciones de gestión
 */

$page_title = 'Gestión de Autores - Admin Biblioteca UNICAB';
$page_header = 'Gestión de Autores';

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
            $cantidad = $repo->contarRecursosPorAutor($id);
            if ($cantidad > 0) {
                $mensaje = "No se puede eliminar el autor porque tiene $cantidad recurso(s) asociado(s)";
                $tipoMensaje = 'error';
            } else {
                $repo->eliminarAutor($id);
                $mensaje = 'Autor eliminado correctamente';
                $tipoMensaje = 'success';
            }
        } catch (Exception $e) {
            $mensaje = 'Error al eliminar: ' . $e->getMessage();
            $tipoMensaje = 'error';
        }
    }
}

// Obtener autores
$autores = $repo->obtenerAutoresConConteo();

// Búsqueda
$busqueda = trim($_GET['q'] ?? '');
if ($busqueda) {
    $autores = array_filter($autores, function($autor) use ($busqueda) {
        return stripos($autor['nombre'], $busqueda) !== false;
    });
}
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
                        <h2>Autores (<?php echo count($autores); ?>)</h2>
                        <div class="admin-table-actions">
                            <form method="GET" class="admin-search">
                                <span>&#128269;</span>
                                <input type="text" name="q" placeholder="Buscar autor..."
                                       value="<?php echo htmlspecialchars($busqueda); ?>">
                            </form>
                            <a href="autores_form.php" class="btn-admin success">+ Nuevo Autor</a>
                        </div>
                    </div>

                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Recursos</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($autores)): ?>
                                <?php foreach ($autores as $autor): ?>
                                <tr>
                                    <td><?php echo $autor['id_autor']; ?></td>
                                    <td><?php echo htmlspecialchars($autor['nombre']); ?></td>
                                    <td><?php echo $autor['cantidad_recursos']; ?></td>
                                    <td>
                                        <div class="table-actions">
                                            <a href="autores_form.php?id=<?php echo $autor['id_autor']; ?>"
                                               class="btn-icon edit" title="Editar">&#9998;</a>
                                            <?php if ($autor['cantidad_recursos'] == 0): ?>
                                            <form method="POST" style="display: inline;"
                                                  onsubmit="return confirm('¿Está seguro de eliminar este autor?');">
                                                <input type="hidden" name="id" value="<?php echo $autor['id_autor']; ?>">
                                                <button type="submit" name="eliminar" class="btn-icon delete" title="Eliminar">&#128465;</button>
                                            </form>
                                            <?php else: ?>
                                            <button class="btn-icon delete" disabled title="No se puede eliminar (tiene recursos)">&#128465;</button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" style="text-align: center; padding: 40px;">
                                        <?php if ($busqueda): ?>
                                            No se encontraron autores con "<?php echo htmlspecialchars($busqueda); ?>"
                                        <?php else: ?>
                                            No hay autores registrados
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    <?php include 'components/bucv_admin_scripts.php'; ?>
</body>
</html>
