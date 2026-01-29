<?php
/**
 * index.php
 * Página principal de la Biblioteca UNICAB
 * Landing page con búsqueda y accesos rápidos
 */

$nivel = "raiz";
$page_title = "Biblioteca UNICAB - Inicio";

require_once(__DIR__ . '/repositories/bucv_1cc2s4B3.php');
$repo = new BucvRepository();
$recursosDestacados = $repo->obtenerRecursos('', ['limite' => 6]);
$autoresTop = $repo->obtenerAutoresConConteo();
$autoresTop = array_slice($autoresTop, 0, 4);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include('components/bucv_head.php'); ?>
    <style>
        /* Secciones de la landing */
        .section {
            padding: 80px var(--spacing-2xl);
        }

        .section-header {
            text-align: center;
            margin-bottom: var(--spacing-3xl);
        }

        .section-header h2 {
            font-size: var(--font-size-3xl);
            color: var(--color-dark);
            margin-bottom: var(--spacing-md);
        }

        .section-header p {
            color: var(--color-gray);
            font-size: var(--font-size-lg);
            max-width: 600px;
            margin: 0 auto;
        }

        /* Hero Illustration */
        .hero-illustration {
            max-width: 200px;
            width: 100%;
            height: auto;
            margin: 10px auto 15px;
            display: block;
        }

        /* Quick Access Cards */
        .quick-access {
            background: var(--color-light);
        }

        .access-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--spacing-xl);
            max-width: 1000px;
            margin: 0 auto;
        }

        .access-card {
            background: var(--color-white);
            padding: var(--spacing-2xl);
            border-radius: var(--border-radius-xl);
            text-align: center;
            text-decoration: none;
            color: inherit;
            box-shadow: var(--shadow-sm);
            transition: transform var(--transition-fast), box-shadow var(--transition-fast);
        }

        .access-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .access-card-icon {
            font-size: 48px;
            margin-bottom: var(--spacing-lg);
        }

        .access-card h3 {
            font-size: var(--font-size-xl);
            color: var(--color-dark);
            margin-bottom: var(--spacing-sm);
        }

        .access-card p {
            color: var(--color-gray);
            font-size: var(--font-size-base);
        }

        /* Featured Resources */
        .featured-resources {
            background: var(--color-white);
        }

        .resources-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: var(--spacing-lg);
            max-width: 1200px;
            margin: 0 auto var(--spacing-2xl);
        }

        .resource-card {
            background: var(--color-white);
            border-radius: var(--border-radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: transform var(--transition-fast), box-shadow var(--transition-fast);
            cursor: pointer;
            display: flex;
        }

        .resource-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }

        .resource-cover {
            width: 100px;
            min-height: 140px;
            background: var(--color-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            color: var(--color-white);
            flex-shrink: 0;
        }

        .resource-info {
            padding: var(--spacing-lg);
            flex: 1;
        }

        .resource-type {
            display: inline-block;
            padding: 2px 8px;
            background: var(--color-success-bg);
            color: var(--color-secondary-dark);
            border-radius: var(--border-radius-sm);
            font-size: var(--font-size-xs);
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: var(--spacing-xs);
        }

        .resource-title {
            font-size: var(--font-size-lg);
            font-weight: 600;
            color: var(--color-dark);
            margin-bottom: var(--spacing-xs);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .resource-author {
            color: var(--color-primary);
            font-size: var(--font-size-sm);
        }

        .view-all-btn {
            display: block;
            text-align: center;
        }

        /* Top Authors */
        .top-authors {
            background: var(--color-light);
        }

        .authors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: var(--spacing-lg);
            max-width: 900px;
            margin: 0 auto var(--spacing-2xl);
        }

        .author-card {
            background: var(--color-white);
            padding: var(--spacing-xl);
            border-radius: var(--border-radius-lg);
            text-align: center;
            text-decoration: none;
            color: inherit;
            box-shadow: var(--shadow-sm);
            transition: transform var(--transition-fast);
        }

        .author-card:hover {
            transform: translateY(-3px);
        }

        .author-avatar {
            width: 60px;
            height: 60px;
            background: var(--color-secondary);
            color: var(--color-dark);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 600;
            margin: 0 auto var(--spacing-md);
        }

        .author-card h4 {
            font-size: var(--font-size-base);
            color: var(--color-dark);
            margin-bottom: var(--spacing-xs);
        }

        .author-card span {
            font-size: var(--font-size-sm);
            color: var(--color-gray);
        }

        @media (max-width: 768px) {
            .section {
                padding: 50px var(--spacing-lg);
            }

            .resources-grid {
                grid-template-columns: 1fr;
            }

            .resource-card {
                flex-direction: column;
            }

            .resource-cover {
                width: 100%;
                min-height: 120px;
            }
        }
    </style>
</head>
<body>
    <?php include('components/bucv_header.php'); ?>

    <!-- Sección Hero de Búsqueda -->
    <section class="hero-search">
        <div class="hero-content">
            <img src="assets/images/ilustracion_busqueda.webp" alt="Búsqueda" class="hero-illustration">
            <h1>¿Qué buscas?</h1>
            <p class="hero-subtitle">Explora nuestra colección de libros, artículos, tesis y recursos académicos</p>

            <!-- Barra de búsqueda -->
            <div class="search-container">
                <form action="pages/bucv_catalogo.php" method="GET">
                    <div class="search-box-main">
                        <span class="search-icon">&#128269;</span>
                        <input type="text" name="q" placeholder="Buscar por título, autor, ISBN, tema..." autocomplete="off">
                        <button type="submit" class="btn-search">Buscar</button>
                    </div>
                </form>
            </div>

            <!-- Búsquedas populares -->
            <div class="search-suggestions">
                <span>Búsquedas populares:</span>
                <a href="pages/bucv_catalogo.php?q=bioética">Bioética</a>
                <a href="pages/bucv_catalogo.php?q=inteligencia artificial">IA</a>
                <a href="pages/bucv_catalogo.php?q=matemáticas">Matemáticas</a>
                <a href="pages/bucv_catalogo.php?q=sociología">Sociología</a>
            </div>

            <!-- Iconos Sociales -->
            <div class="social-icons">
                <a href="#" class="social-icon">
                    <img src="assets/images/facebook.webp" alt="Facebook">
                </a>
                <a href="#" class="social-icon">
                    <img src="assets/images/correo.webp" alt="Correo">
                </a>
                <a href="#" class="social-icon">
                    <img src="assets/images/Instagram.webp" alt="Instagram">
                </a>
                <a href="#" class="social-icon">
                    <img src="assets/images/youTube.webp" alt="YouTube">
                </a>
                <a href="#" class="social-icon">
                    <img src="assets/images/WhatsApp.webp" alt="WhatsApp">
                </a>
            </div>
        </div>
    </section>

    <!-- Acceso Rápido -->
    <section class="section quick-access">
        <div class="section-header">
            <h2>Explora la Biblioteca</h2>
            <p>Accede rápidamente a las diferentes secciones de nuestra colección</p>
        </div>

        <div class="access-grid">
            <a href="pages/bucv_catalogo.php" class="access-card">
                <div class="access-card-icon">&#128218;</div>
                <h3>Catálogo Completo</h3>
                <p>Explora todos los recursos disponibles con filtros avanzados</p>
            </a>

            <a href="pages/bucv_autores.php" class="access-card">
                <div class="access-card-icon">&#128100;</div>
                <h3>Buscar por Autor</h3>
                <p>Encuentra obras de tus autores favoritos</p>
            </a>
        </div>
    </section>

    <!-- Recursos Destacados -->
    <?php if (!empty($recursosDestacados)): ?>
    <section class="section featured-resources">
        <div class="section-header">
            <h2>Recursos Recientes</h2>
            <p>Descubre las últimas incorporaciones a nuestra colección</p>
        </div>

        <div class="resources-grid">
            <?php
            $iconos = [
                'Libro' => '&#128213;',
                'Artículo científico' => '&#128196;',
                'Artículo no científico' => '&#128240;',
                'Tesis' => '&#127891;',
                'Tesina' => '&#128203;',
                'Ensayo' => '&#128221;',
                'Video' => '&#127916;'
            ];
            foreach ($recursosDestacados as $recurso):
                $icono = $iconos[$recurso['tipo_recurso']] ?? '&#128218;';
            ?>
            <a href="pages/bucv_detalle_recurso.php?id=<?php echo $recurso['id_recursos']; ?>" class="resource-card">
                <div class="resource-cover">
                    <span><?php echo $icono; ?></span>
                </div>
                <div class="resource-info">
                    <span class="resource-type"><?php echo htmlspecialchars($recurso['tipo_recurso'] ?? 'Recurso'); ?></span>
                    <h3 class="resource-title"><?php echo htmlspecialchars($recurso['titulo']); ?></h3>
                    <?php if (!empty($recurso['autor'])): ?>
                        <p class="resource-author"><?php echo htmlspecialchars($recurso['autor']); ?></p>
                    <?php endif; ?>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

        <div class="view-all-btn">
            <a href="pages/bucv_catalogo.php" class="btn-primary btn-large">Ver todo el catálogo</a>
        </div>
    </section>
    <?php endif; ?>

    <!-- Top Autores -->
    <?php if (!empty($autoresTop)): ?>
    <section class="section top-authors">
        <div class="section-header">
            <h2>Autores Destacados</h2>
            <p>Los autores con más recursos en nuestra colección</p>
        </div>

        <div class="authors-grid">
            <?php foreach ($autoresTop as $autor): ?>
            <a href="pages/bucv_catalogo.php?autor=<?php echo urlencode($autor['nombre']); ?>" class="author-card">
                <div class="author-avatar">
                    <?php echo mb_strtoupper(mb_substr($autor['nombre'], 0, 1)); ?>
                </div>
                <h4><?php echo htmlspecialchars($autor['nombre']); ?></h4>
                <span><?php echo $autor['total_recursos']; ?> recurso<?php echo $autor['total_recursos'] != 1 ? 's' : ''; ?></span>
            </a>
            <?php endforeach; ?>
        </div>

        <div class="view-all-btn">
            <a href="pages/bucv_autores.php" class="btn-secondary btn-large">Ver todos los autores</a>
        </div>
    </section>
    <?php endif; ?>

    <?php include('components/bucv_footer.php'); ?>
</body>
</html>
