<?php
/**
 * autores_form.php
 * Formulario para crear/editar autores
 */

require_once(__DIR__ . '/components/bucv_admin_auth.php');
require_once(__DIR__ . '/../repositories/bucv_1cc2s4B3.php');

$repo = new BucvRepository();

$id = intval($_GET['id'] ?? 0);
$autor = null;
$mensaje = '';
$tipoMensaje = '';

// Si es edición, cargar autor
if ($id > 0) {
    $autor = $repo->obtenerAutorPorId($id);
    if (!$autor) {
        header('Location: autores.php');
        exit;
    }
}

$page_title = $id > 0 ? 'Editar Autor - Admin' : 'Nuevo Autor - Admin';
$page_header = $id > 0 ? 'Editar Autor' : 'Nuevo Autor';

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');

    if (empty($nombre)) {
        $mensaje = 'El nombre del autor es requerido';
        $tipoMensaje = 'error';
    } else {
        try {
            if ($id > 0) {
                $repo->actualizarAutor($id, $nombre);
                $mensaje = 'Autor actualizado correctamente';
            } else {
                $id = $repo->crearAutor($nombre);
                $mensaje = 'Autor creado correctamente';
            }
            $tipoMensaje = 'success';
            $autor = $repo->obtenerAutorPorId($id);
        } catch (Exception $e) {
            $mensaje = 'Error: ' . $e->getMessage();
            $tipoMensaje = 'error';
        }
    }
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

                <div class="admin-form">
                    <h2><?php echo $id > 0 ? 'Editar Autor' : 'Nuevo Autor'; ?></h2>

                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="nombre">Nombre del autor *</label>
                            <input type="text" id="nombre" name="nombre" required
                                   value="<?php echo htmlspecialchars($autor['nombre'] ?? $_POST['nombre'] ?? ''); ?>"
                                   placeholder="Ej: Gabriel García Márquez">
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-admin primary">
                                <?php echo $id > 0 ? 'Guardar Cambios' : 'Crear Autor'; ?>
                            </button>
                            <a href="autores.php" class="btn-admin secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
    <?php include 'components/bucv_admin_scripts.php'; ?>
</body>
</html>
