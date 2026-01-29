<?php
/**
 * bucv_autores.php
 * Página pública de listado de autores
 */

$nivel = "pages";
$page_title = "Autores - Biblioteca UNICAB";

require_once(__DIR__ . '/../repositories/bucv_1cc2s4B3.php');

$repo = new BucvRepository();
$autores = $repo->obtenerAutoresConConteo();

// Búsqueda de autor
$busqueda = trim($_GET['q'] ?? '');
if ($busqueda) {
    $autores = array_filter($autores, function($autor) use ($busqueda) {
        return stripos($autor['nombre'], $busqueda) !== false;
    });
    $autores = array_values($autores);
}

// Ordenar alfabéticamente
usort($autores, function($a, $b) {
    return strcasecmp($a['nombre'], $b['nombre']);
});
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include '../components/bucv_head.php'; ?>
    <style>
        .authors-page {
            max-width: 1200px;
            margin: 0 auto;
            padding: var(--spacing-2xl);
        }

        .authors-header {
            text-align: center;
            margin-bottom: var(--spacing-3xl);
        }

        .authors-header h1 {
            font-size: var(--font-size-4xl);
            color: var(--color-dark);
            margin-bottom: var(--spacing-md);
        }

        .authors-header p {
            color: var(--color-gray);
            font-size: var(--font-size-lg);
        }

        .authors-search {
            max-width: 500px;
            margin: var(--spacing-2xl) auto;
        }

        .authors-search form {
            display: flex;
            gap: var(--spacing-sm);
        }

        .authors-search input {
            flex: 1;
            padding: 14px var(--spacing-lg);
            border: 2px solid var(--color-gray-lighter);
            border-radius: var(--border-radius-full);
            font-size: var(--font-size-base);
            transition: border-color var(--transition-fast);
        }

        .authors-search input:focus {
            outline: none;
            border-color: var(--color-primary);
        }

        .authors-search button {
            padding: 14px var(--spacing-xl);
            background: var(--color-primary);
            color: var(--color-white);
            border: none;
            border-radius: var(--border-radius-full);
            font-weight: 600;
            cursor: pointer;
            transition: background var(--transition-fast);
        }

        .authors-search button:hover {
            background: var(--color-primary-dark);
        }

        .authors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: var(--spacing-lg);
        }

        .author-card {
            background: var(--color-white);
            border-radius: var(--border-radius-lg);
            padding: var(--spacing-xl);
            box-shadow: var(--shadow-sm);
            transition: transform var(--transition-fast), box-shadow var(--transition-fast);
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .author-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .author-avatar {
            width: 80px;
            height: 80px;
            background: var(--color-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto var(--spacing-lg);
            font-size: 36px;
            color: var(--color-white);
        }

        .author-name {
            font-size: var(--font-size-xl);
            font-weight: 600;
            color: var(--color-dark);
            text-align: center;
            margin-bottom: var(--spacing-sm);
        }

        .author-count {
            text-align: center;
            color: var(--color-gray);
            font-size: var(--font-size-base);
        }

        .author-count span {
            color: var(--color-secondary);
            font-weight: 600;
        }

        .no-authors {
            text-align: center;
            padding: var(--spacing-4xl);
            color: var(--color-gray);
        }

        .no-authors span {
            font-size: 64px;
            display: block;
            margin-bottom: var(--spacing-lg);
        }

        .breadcrumb {
            margin-bottom: var(--spacing-xl);
        }

        .breadcrumb a {
            color: var(--color-primary);
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .authors-page {
                padding: var(--spacing-lg);
            }

            .authors-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include '../components/bucv_header.php'; ?>

    <div class="authors-page">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="../index.php">Inicio</a> &gt;
            <span>Autores</span>
        </div>

        <div class="authors-header">
            <h1>Nuestros Autores</h1>
            <p>Explora los recursos por autor. Haz clic en un autor para ver todas sus obras.</p>
        </div>

        <!-- Búsqueda -->
        <div class="authors-search">
            <form method="GET" action="">
                <input type="text" name="q" placeholder="Buscar autor por nombre..."
                       value="<?php echo htmlspecialchars($busqueda); ?>">
                <button type="submit">Buscar</button>
            </form>
        </div>

        <?php if ($busqueda): ?>
            <p style="text-align: center; margin-bottom: var(--spacing-xl); color: var(--color-gray);">
                Mostrando resultados para: <strong>"<?php echo htmlspecialchars($busqueda); ?>"</strong>
                - <a href="bucv_autores.php">Ver todos</a>
            </p>
        <?php endif; ?>

        <?php if (!empty($autores)): ?>
            <div class="authors-grid">
                <?php foreach ($autores as $autor): ?>
                    <a href="bucv_catalogo.php?autor=<?php echo urlencode($autor['nombre']); ?>" class="author-card">
                        <div class="author-avatar">
                            <?php echo mb_strtoupper(mb_substr($autor['nombre'], 0, 1)); ?>
                        </div>
                        <h3 class="author-name"><?php echo htmlspecialchars($autor['nombre']); ?></h3>
                        <p class="author-count">
                            <span><?php echo $autor['total_recursos']; ?></span>
                            recurso<?php echo $autor['total_recursos'] != 1 ? 's' : ''; ?>
                        </p>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-authors">
                <span>&#128100;</span>
                <h3>No se encontraron autores</h3>
                <?php if ($busqueda): ?>
                    <p>No hay autores que coincidan con "<?php echo htmlspecialchars($busqueda); ?>"</p>
                    <a href="bucv_autores.php" class="btn-primary" style="display: inline-block; margin-top: var(--spacing-lg);">Ver todos los autores</a>
                <?php else: ?>
                    <p>Aún no hay autores registrados en el sistema.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php include '../components/bucv_footer.php'; ?>
    <?php include '../components/bucv_scripts.php'; ?>
</body>
</html>
