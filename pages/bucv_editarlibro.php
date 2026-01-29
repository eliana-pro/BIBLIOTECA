<?php
/**
 * bucv_editarlibro.php
 * Página para editar recursos de la biblioteca
 */

$nivel = "pages";
$page_title = "Editar Recurso - Biblioteca UNICAB";

$recurso_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

require_once(__DIR__ . '/../repositories/bucv_1cc2s4B3.php');

$repo = new BucvRepository();
$recurso = null;
$mensaje = '';
$tipoMensaje = '';

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar'])) {
    $id = intval($_POST['id']);

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

    try {
        $repo->actualizarRecurso($id, $datos);
        $mensaje = 'Recurso actualizado correctamente';
        $tipoMensaje = 'success';
        $recurso_id = $id;
    } catch (Exception $e) {
        $mensaje = 'Error al actualizar: ' . $e->getMessage();
        $tipoMensaje = 'error';
    }
}

// Cargar datos
if ($recurso_id > 0) {
    $recurso = $repo->obtenerRecursoParaEdicion($recurso_id);
    if ($recurso) {
        $page_title = "Editar: " . $recurso['titulo'] . " - Biblioteca UNICAB";
    }
}

// Cargar opciones para selects
$tiposRecurso = $repo->obtenerGeneros();
$autores = $repo->obtenerAutores();
$idiomas = $repo->obtenerIdiomas();
$pensamientos = $repo->obtenerPensamientos();
$paises = $repo->obtenerPaises();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include '../components/bucv_head.php'; ?>
</head>
<body>
    <?php include '../components/bucv_header.php'; ?>

    <div class="edit-page">
        <?php if ($recurso): ?>
            <!-- Breadcrumb -->
            <div class="breadcrumb">
                <a href="../index.php">Inicio</a> &gt;
                <a href="bucv_catalogo.php">Catálogo</a> &gt;
                <a href="bucv_detalle_recurso.php?id=<?php echo $recurso_id; ?>">Detalle</a> &gt;
                <span>Editar</span>
            </div>

            <?php if ($mensaje): ?>
                <div class="alert alert-<?php echo $tipoMensaje; ?>">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>

            <div class="edit-form">
                <h1>Editar Recurso</h1>

                <form method="POST" action="" id="formEditar">
                    <input type="hidden" name="id" value="<?php echo $recurso_id; ?>">

                    <!-- Título -->
                    <div class="form-group">
                        <label for="titulo">Título *</label>
                        <input type="text" id="titulo" name="titulo" required
                               value="<?php echo htmlspecialchars($recurso['titulo'] ?? ''); ?>">
                    </div>

                    <!-- Tipo y Autor -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="id_tipo_recursos">Tipo de recurso</label>
                            <select id="id_tipo_recursos" name="id_tipo_recursos">
                                <option value="">Seleccionar...</option>
                                <?php foreach ($tiposRecurso as $tipo): ?>
                                    <option value="<?php echo $tipo['id_tipo_recursos']; ?>"
                                        <?php echo ($recurso['id_tipo_recursos'] == $tipo['id_tipo_recursos']) ? 'selected' : ''; ?>>
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
                                        <?php echo ($recurso['id_autor'] == $autor['id_autor']) ? 'selected' : ''; ?>>
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
                                   value="<?php echo htmlspecialchars($recurso['año'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="isbn_issn_doi">ISBN / ISSN / DOI</label>
                            <input type="text" id="isbn_issn_doi" name="isbn_issn_doi"
                                   value="<?php echo htmlspecialchars($recurso['isbn_issn_doi'] ?? ''); ?>">
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
                                        <?php echo ($recurso['id_idiomas'] == $idioma['id_idiomas']) ? 'selected' : ''; ?>>
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
                                        <?php echo ($recurso['id_pensamientos'] == $pensamiento['id_pensamientos']) ? 'selected' : ''; ?>>
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
                                    <?php echo ($recurso['id_pais'] == $pais['id_pais']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($pais['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Resumen -->
                    <div class="form-group">
                        <label for="resumen">Resumen</label>
                        <textarea id="resumen" name="resumen" rows="5"><?php echo htmlspecialchars($recurso['resumen'] ?? ''); ?></textarea>
                    </div>

                    <!-- Acciones -->
                    <div class="form-actions">
                        <button type="submit" name="guardar" class="btn-primary">
                            Guardar cambios
                        </button>
                        <a href="bucv_detalle_recurso.php?id=<?php echo $recurso_id; ?>" class="btn-secondary">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>

        <?php else: ?>
            <!-- Recurso no encontrado -->
            <div class="not-found">
                <span style="font-size: 80px;">&#128218;</span>
                <h2>Recurso no encontrado</h2>
                <p>El recurso que intentas editar no existe o ha sido eliminado.</p>
                <a href="bucv_catalogo.php" class="btn-primary btn-large">Volver al catálogo</a>
            </div>
        <?php endif; ?>
    </div>

    <?php include '../components/bucv_footer.php'; ?>
    <?php include '../components/bucv_scripts.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('formEditar');
            const tituloInput = document.getElementById('titulo');

            if (form) {
                form.addEventListener('submit', function(e) {
                    if (!tituloInput.value.trim()) {
                        e.preventDefault();
                        tituloInput.style.borderColor = 'var(--color-error)';
                        return false;
                    }
                });
            }

            if (tituloInput) {
                tituloInput.addEventListener('input', function() {
                    this.style.borderColor = '';
                });
            }
        });
    </script>
</body>
</html>
