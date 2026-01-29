<?php
/**
 * recursos_form.php
 * Formulario para crear/editar recursos
 */

require_once(__DIR__ . '/components/bucv_admin_auth.php');
require_once(__DIR__ . '/../repositories/bucv_1cc2s4B3.php');

$repo = new BucvRepository();

$id = intval($_GET['id'] ?? 0);
$recurso = null;
$mensaje = '';
$tipoMensaje = '';

// Si es edición, cargar recurso
if ($id > 0) {
    $recurso = $repo->obtenerRecursoParaEdicion($id);
    if (!$recurso) {
        header('Location: recursos.php');
        exit;
    }
}

$page_title = $id > 0 ? 'Editar Recurso - Admin' : 'Nuevo Recurso - Admin';
$page_header = $id > 0 ? 'Editar Recurso' : 'Nuevo Recurso';

// Cargar opciones para selects
$tiposRecurso = $repo->obtenerGeneros();
$autores = $repo->obtenerAutores();
$idiomas = $repo->obtenerIdiomas();
$pensamientos = $repo->obtenerPensamientos();
$paises = $repo->obtenerPaises();

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'titulo' => trim($_POST['titulo'] ?? ''),
        'año' => intval($_POST['año'] ?? 0),
        'resumen' => trim($_POST['resumen'] ?? ''),
        'isbn_issn_doi' => trim($_POST['isbn_issn_doi'] ?? ''),
        'id_tipo_recursos' => intval($_POST['id_tipo_recursos'] ?? 0),
        'id_autor' => intval($_POST['id_autor'] ?? 0),
        'id_idiomas' => intval($_POST['id_idiomas'] ?? 0),
        'id_pensamientos' => intval($_POST['id_pensamientos'] ?? 0),
        'id_pais' => intval($_POST['id_pais'] ?? 0)
    ];

    if (empty($datos['titulo'])) {
        $mensaje = 'El título es requerido';
        $tipoMensaje = 'error';
    } else {
        try {
            if ($id > 0) {
                $repo->actualizarRecurso($id, $datos);
                $mensaje = 'Recurso actualizado correctamente';
            } else {
                $id = $repo->crearRecurso($datos);
                $mensaje = 'Recurso creado correctamente';
            }
            $tipoMensaje = 'success';
            $recurso = $repo->obtenerRecursoParaEdicion($id);
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
                    <h2><?php echo $id > 0 ? 'Editar Recurso' : 'Nuevo Recurso'; ?></h2>

                    <form method="POST" action="">
                        <!-- Título -->
                        <div class="form-group">
                            <label for="titulo">Título *</label>
                            <input type="text" id="titulo" name="titulo" required
                                   value="<?php echo htmlspecialchars($recurso['titulo'] ?? $_POST['titulo'] ?? ''); ?>">
                        </div>

                        <!-- Tipo y Autor -->
                        <div class="form-row">
                            <div class="form-group">
                                <label for="id_tipo_recursos">Tipo de recurso</label>
                                <select id="id_tipo_recursos" name="id_tipo_recursos">
                                    <option value="">Seleccionar...</option>
                                    <?php foreach ($tiposRecurso as $tipo): ?>
                                        <option value="<?php echo $tipo['id_tipo_recursos']; ?>"
                                            <?php echo (($recurso['id_tipo_recursos'] ?? '') == $tipo['id_tipo_recursos']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($tipo['nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="id_autor">Autor</label>
                                <select id="id_autor" name="id_autor">
                                    <option value="">Seleccionar...</option>
                                    <?php foreach ($autores as $autor): ?>
                                        <option value="<?php echo $autor['id_autor']; ?>"
                                            <?php echo (($recurso['id_autor'] ?? '') == $autor['id_autor']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($autor['nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Año e ISBN -->
                        <div class="form-row">
                            <div class="form-group">
                                <label for="año">Año de publicación</label>
                                <input type="number" id="año" name="año" min="1900" max="2030"
                                       value="<?php echo htmlspecialchars($recurso['año'] ?? $_POST['año'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="isbn_issn_doi">ISBN / ISSN / DOI</label>
                                <input type="text" id="isbn_issn_doi" name="isbn_issn_doi"
                                       value="<?php echo htmlspecialchars($recurso['isbn_issn_doi'] ?? $_POST['isbn_issn_doi'] ?? ''); ?>">
                            </div>
                        </div>

                        <!-- Idioma y Pensamiento -->
                        <div class="form-row">
                            <div class="form-group">
                                <label for="id_idiomas">Idioma</label>
                                <select id="id_idiomas" name="id_idiomas">
                                    <option value="">Seleccionar...</option>
                                    <?php foreach ($idiomas as $idioma): ?>
                                        <option value="<?php echo $idioma['id_idiomas']; ?>"
                                            <?php echo (($recurso['id_idiomas'] ?? '') == $idioma['id_idiomas']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($idioma['nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="id_pensamientos">Pensamiento</label>
                                <select id="id_pensamientos" name="id_pensamientos">
                                    <option value="">Seleccionar...</option>
                                    <?php foreach ($pensamientos as $pensamiento): ?>
                                        <option value="<?php echo $pensamiento['id_pensamientos']; ?>"
                                            <?php echo (($recurso['id_pensamientos'] ?? '') == $pensamiento['id_pensamientos']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($pensamiento['nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- País -->
                        <div class="form-group">
                            <label for="id_pais">País de publicación</label>
                            <select id="id_pais" name="id_pais">
                                <option value="">Seleccionar...</option>
                                <?php foreach ($paises as $pais): ?>
                                    <option value="<?php echo $pais['id_pais']; ?>"
                                        <?php echo (($recurso['id_pais'] ?? '') == $pais['id_pais']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($pais['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Resumen -->
                        <div class="form-group">
                            <label for="resumen">Resumen</label>
                            <textarea id="resumen" name="resumen" rows="5"><?php echo htmlspecialchars($recurso['resumen'] ?? $_POST['resumen'] ?? ''); ?></textarea>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-admin primary">
                                <?php echo $id > 0 ? 'Guardar Cambios' : 'Crear Recurso'; ?>
                            </button>
                            <a href="recursos.php" class="btn-admin secondary">Cancelar</a>
                            <?php if ($id > 0): ?>
                            <a href="../pages/bucv_detalle_recurso.php?id=<?php echo $id; ?>"
                               target="_blank" class="btn-admin secondary">Ver en sitio</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
    <?php include 'components/bucv_admin_scripts.php'; ?>
</body>
</html>
