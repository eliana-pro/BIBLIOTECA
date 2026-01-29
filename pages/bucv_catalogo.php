<?php
/**
 * bucv_catalogo.php
 * Página del catálogo completo de recursos
 */

$nivel = "pages";
$page_title = "Catálogo - Biblioteca UNICAB";

// Verificar si hay filtro de autor desde URL
$autorFiltro = isset($_GET['autor']) ? trim($_GET['autor']) : '';
if ($autorFiltro) {
    $page_title = "Obras de " . $autorFiltro . " - Biblioteca UNICAB";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include '../components/bucv_head.php'; ?>
</head>
<body>
    <?php include '../components/bucv_header.php'; ?>

    <!-- Header del Catálogo -->
    <section class="catalog-header">
        <div class="catalog-header-content">
            <?php if ($autorFiltro): ?>
                <h1>Obras de <?php echo htmlspecialchars($autorFiltro); ?></h1>
                <p>Explora todos los recursos de este autor</p>
                <a href="bucv_catalogo.php" class="btn-secondary" style="margin-top: var(--spacing-md);">
                    Ver catálogo completo
                </a>
            <?php else: ?>
                <h1>Catálogo de Recursos</h1>
                <p>Explora nuestra colección completa de libros, artículos, tesis y más</p>
            <?php endif; ?>

            <!-- Barra de búsqueda -->
            <div class="search-container" style="margin-top: var(--spacing-xl);">
                <div class="search-box-main">
                    <span class="search-icon">&#128269;</span>
                    <input type="text" id="searchInput" placeholder="Buscar por título, autor, ISBN, tema..." autocomplete="off">
                    <button class="btn-search" id="btnBuscar">Buscar</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Contenedor Principal -->
    <div class="container">
        <!-- Sidebar de filtros -->
        <?php include '../components/bucv_sidebar.php'; ?>

        <!-- Resultados -->
        <div class="results">
            <div class="results-header">
                <h2>Recursos Disponibles</h2>
                <div class="results-info">
                    <span id="resultCount">Cargando...</span>
                    <div class="view-toggle">
                        <button class="view-btn active" id="btnVistaLista" title="Vista lista">&#9776;</button>
                        <button class="view-btn" id="btnVistaGrid" title="Vista cuadrícula">&#9783;</button>
                    </div>
                </div>
            </div>

            <!-- Filtros activos -->
            <div class="active-filters" id="activeFilters"></div>

            <!-- Loading -->
            <div class="loading-container" id="loadingSpinner" style="display: none;">
                <div class="loading-spinner"></div>
                <p>Cargando recursos...</p>
            </div>

            <!-- Grid de libros -->
            <div class="book-grid" id="bookGrid"></div>

            <!-- Paginación -->
            <div class="pagination" id="pagination"></div>
        </div>
    </div>

    <!-- Modal de detalle del libro -->
    <div class="modal" id="bookModal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title-section">
                    <span class="modal-badge" id="modalBadge">Libro</span>
                    <h2 id="modalTitle">Título del documento</h2>
                </div>
                <span class="close-btn" onclick="cerrarModal()">&times;</span>
            </div>
            <div class="modal-body" id="modalBody"></div>
            <div class="modal-actions">
                <button class="btn-secondary" id="btnFavoritos">Guardar en favoritos</button>
                <button class="btn-primary" id="btnConsultar">Consultar documento</button>
            </div>
        </div>
    </div>

    <?php include '../components/bucv_footer.php'; ?>
    <?php include '../components/bucv_scripts.php'; ?>

    <script>
        // Aplicar filtro de autor si viene en la URL
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const autorParam = urlParams.get('autor');

            if (autorParam) {
                // Esperar a que se carguen los autores y luego marcar el checkbox
                setTimeout(() => {
                    const checkboxes = document.querySelectorAll('[data-filter="autor"] input[type="checkbox"]');
                    checkboxes.forEach(cb => {
                        if (cb.value === autorParam) {
                            cb.checked = true;
                            aplicarFiltrosDesdeUI();
                        }
                    });
                }, 500);
            }
        });
    </script>

    <style>
        .catalog-header {
            background: var(--color-primary);
            padding: 60px var(--spacing-3xl);
            text-align: center;
            color: var(--color-white);
        }

        .catalog-header-content {
            max-width: 800px;
            margin: 0 auto;
        }

        .catalog-header h1 {
            font-size: var(--font-size-4xl);
            margin-bottom: var(--spacing-md);
        }

        .catalog-header p {
            font-size: var(--font-size-lg);
            opacity: 0.9;
        }

        .catalog-header .search-container {
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        @media (max-width: 768px) {
            .catalog-header {
                padding: 40px var(--spacing-lg);
            }

            .catalog-header h1 {
                font-size: var(--font-size-2xl);
            }
        }
    </style>
</body>
</html>
