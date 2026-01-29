<?php
/**
 * index.php
 * Dashboard del panel de administración
 */

$page_title = 'Dashboard - Admin Biblioteca UNICAB';
$page_header = 'Dashboard';

require_once(__DIR__ . '/components/bucv_admin_auth.php');
require_once(__DIR__ . '/../repositories/bucv_1cc2s4B3.php');

$repo = new BucvRepository();
$stats = $repo->obtenerEstadisticasAdmin();
$recursos_recientes = $repo->obtenerRecursos('', ['limite' => 5]);
$autores_top = $repo->obtenerAutoresConConteo();
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
                <!-- Estadísticas -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-card-icon blue">&#128218;</div>
                        <div class="stat-card-info">
                            <h3><?php echo $stats['total_recursos'] ?? 0; ?></h3>
                            <p>Total Recursos</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-card-icon green">&#128100;</div>
                        <div class="stat-card-info">
                            <h3><?php echo $stats['total_autores'] ?? 0; ?></h3>
                            <p>Autores</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-card-icon orange">&#128213;</div>
                        <div class="stat-card-info">
                            <h3><?php echo $stats['por_tipo']['Libro'] ?? 0; ?></h3>
                            <p>Libros</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-card-icon purple">&#128196;</div>
                        <div class="stat-card-info">
                            <h3><?php echo ($stats['por_tipo']['Artículo científico'] ?? 0) + ($stats['por_tipo']['Artículo no científico'] ?? 0); ?></h3>
                            <p>Artículos</p>
                        </div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: var(--spacing-xl);">
                    <!-- Recursos Recientes -->
                    <div class="admin-table-container">
                        <div class="admin-table-header">
                            <h2>Recursos Recientes</h2>
                            <a href="recursos.php" class="btn-admin secondary">Ver todos</a>
                        </div>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Tipo</th>
                                    <th>Año</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recursos_recientes)): ?>
                                    <?php foreach ($recursos_recientes as $recurso): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($recurso['titulo']); ?></td>
                                        <td><?php echo htmlspecialchars($recurso['tipo_recurso'] ?? 'N/A'); ?></td>
                                        <td><?php echo $recurso['año'] ?? 'S/F'; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" style="text-align: center; padding: 30px;">No hay recursos</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Autores con más recursos -->
                    <div class="admin-table-container">
                        <div class="admin-table-header">
                            <h2>Top Autores</h2>
                        </div>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Autor</th>
                                    <th>Recursos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $top_autores = array_slice($autores_top, 0, 5);
                                if (!empty($top_autores)):
                                    foreach ($top_autores as $autor):
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($autor['nombre']); ?></td>
                                    <td><?php echo $autor['cantidad_recursos']; ?></td>
                                </tr>
                                <?php
                                    endforeach;
                                else:
                                ?>
                                    <tr>
                                        <td colspan="2" style="text-align: center; padding: 30px;">No hay autores</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Estadísticas por Tipo -->
                <div class="admin-table-container" style="margin-top: var(--spacing-xl);">
                    <div class="admin-table-header">
                        <h2>Recursos por Tipo</h2>
                    </div>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Tipo de Recurso</th>
                                <th>Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($stats['por_tipo'])): ?>
                                <?php foreach ($stats['por_tipo'] as $tipo => $cantidad): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($tipo); ?></td>
                                    <td><?php echo $cantidad; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" style="text-align: center; padding: 30px;">No hay datos</td>
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
