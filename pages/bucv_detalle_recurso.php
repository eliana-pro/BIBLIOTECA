<?php
/**
 * bucv_detalle_recurso.php
 * Página de detalle individual de un recurso
 */

$nivel = "pages";
$page_title = "Detalle del Recurso - Biblioteca UNICAB";

$recurso_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

require_once(__DIR__ . '/../repositories/bucv_1cc2s4B3.php');

$repo = new BucvRepository();
$recurso = null;
$relacionados = [];

if ($recurso_id > 0) {
    $recurso = $repo->obtenerRecursoPorId($recurso_id);
    if ($recurso) {
        $relacionados = $repo->obtenerRecursosRelacionados($recurso_id, 4);
        $page_title = $recurso['titulo'] . " - Biblioteca UNICAB";
    }
}

$iconos = [
    'Libro' => '&#128213;',
    'Artículo científico' => '&#128196;',
    'Artículo no científico' => '&#128240;',
    'Tesis' => '&#127891;',
    'Tesina' => '&#128203;',
    'Proyecto de aula' => '&#128194;',
    'Ensayo' => '&#128221;',
    'Video' => '&#127916;',
    'Audio' => '&#127911;',
    'Póster' => '&#128444;'
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include '../components/bucv_head.php'; ?>
</head>
<body>
    <?php include '../components/bucv_header.php'; ?>

    <div class="detail-page">
        <?php if ($recurso): ?>
            <!-- Breadcrumb -->
            <div class="breadcrumb">
                <a href="../index.php">Inicio</a> &gt;
                <a href="../index.php">Catálogo</a> &gt;
                <span><?php echo htmlspecialchars($recurso['titulo']); ?></span>
            </div>

            <!-- Detalle del recurso -->
            <div class="resource-detail">
                <!-- Cover -->
                <div class="resource-cover">
                    <span><?php echo $iconos[$recurso['tipo_recurso']] ?? '&#128218;'; ?></span>
                </div>

                <!-- Contenido -->
                <div class="resource-content">
                    <span class="resource-badge"><?php echo htmlspecialchars($recurso['tipo_recurso']); ?></span>
                    <h1 class="resource-title"><?php echo htmlspecialchars($recurso['titulo']); ?></h1>
                    <?php if ($recurso['autor']): ?>
                        <p class="resource-author">Por <?php echo htmlspecialchars($recurso['autor']); ?></p>
                    <?php endif; ?>

                    <!-- Info Grid -->
                    <div class="resource-info">
                        <div class="info-item">
                            <div class="info-label">Año de publicación</div>
                            <div class="info-value"><?php echo $recurso['año'] ?? 'Sin fecha'; ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">ISBN/ISSN/DOI</div>
                            <div class="info-value"><?php echo htmlspecialchars($recurso['isbn_issn_doi'] ?? 'No disponible'); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Idioma</div>
                            <div class="info-value"><?php echo htmlspecialchars($recurso['idioma'] ?? 'No especificado'); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Pensamiento</div>
                            <div class="info-value"><?php echo htmlspecialchars($recurso['pensamiento'] ?? 'No especificado'); ?></div>
                        </div>
                    </div>

                    <!-- Resumen -->
                    <div class="resource-description">
                        <h3>Resumen</h3>
                        <p><?php echo nl2br(htmlspecialchars($recurso['resumen'] ?? 'Sin resumen disponible')); ?></p>
                    </div>

                    <!-- Acciones -->
                    <div class="resource-actions">
                        <a href="./bucv_editarlibro.php?id=<?php echo $recurso_id; ?>" class="btn-primary btn-large">
                            Editar libro
                        </a>
                        <button type="button" class="btn-secondary btn-large" onclick="agregarFavoritos()">
                            Agregar a favoritos
                        </button>
                        <button type="button" class="btn-secondary btn-large" onclick="compartir()">
                            Compartir
                        </button>
                    </div>
                </div>
            </div>

            <!-- Recursos relacionados -->
            <?php if (!empty($relacionados)): ?>
            <section class="related-section">
                <h2>Recursos relacionados</h2>
                <div class="related-grid">
                    <?php foreach ($relacionados as $rel): ?>
                    <div class="book-card" onclick="window.location.href='bucv_detalle_recurso.php?id=<?php echo $rel['id_recursos']; ?>'">
                        <div class="book-cover">
                            <span><?php echo $iconos[$rel['tipo_recurso']] ?? '&#128218;'; ?></span>
                        </div>
                        <div class="book-info">
                            <span class="book-type"><?php echo htmlspecialchars($rel['tipo_recurso']); ?></span>
                            <h3 class="book-title"><?php echo htmlspecialchars($rel['titulo']); ?></h3>
                            <p class="book-meta"><?php echo $rel['año'] ?? 'S/F'; ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

        <?php else: ?>
            <!-- Recurso no encontrado -->
            <div class="not-found">
                <span style="font-size: 80px;">&#128218;</span>
                <h2>Recurso no encontrado</h2>
                <p>El recurso que buscas no existe o ha sido eliminado.</p>
                <a href="../index.php" class="btn-primary btn-large">Volver al catálogo</a>
            </div>
        <?php endif; ?>
    </div>

    <?php include '../components/bucv_footer.php'; ?>
    <?php include '../components/bucv_scripts.php'; ?>

    <script>
        const recursoTitulo = <?php echo json_encode($recurso['titulo'] ?? ''); ?>;

        function agregarFavoritos() {
            alert('"' + recursoTitulo + '" agregado a favoritos');
        }

        function compartir() {
            if (navigator.share) {
                navigator.share({
                    title: recursoTitulo,
                    text: 'Mira este recurso en la Biblioteca UNICAB: ' + recursoTitulo,
                    url: window.location.href
                });
            } else {
                navigator.clipboard.writeText(window.location.href);
                alert('Enlace copiado al portapapeles');
            }
        }
    </script>
</body>
</html>
