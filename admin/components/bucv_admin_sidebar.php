<?php
/**
 * bucv_admin_sidebar.php
 * Sidebar de navegación del panel admin
 */

$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<aside class="admin-sidebar">
    <div class="admin-logo">
        <h2>Biblioteca UNICAB</h2>
        <span>Panel de Administración</span>
    </div>

    <nav class="admin-nav">
        <a href="index.php" class="admin-nav-item <?php echo $current_page === 'index' ? 'active' : ''; ?>">
            <span class="nav-icon">&#127968;</span>
            Dashboard
        </a>

        <div class="admin-nav-divider"></div>

        <a href="recursos.php" class="admin-nav-item <?php echo $current_page === 'recursos' || $current_page === 'recursos_form' ? 'active' : ''; ?>">
            <span class="nav-icon">&#128218;</span>
            Recursos
        </a>

        <a href="autores.php" class="admin-nav-item <?php echo $current_page === 'autores' || $current_page === 'autores_form' ? 'active' : ''; ?>">
            <span class="nav-icon">&#128100;</span>
            Autores
        </a>

        <div class="admin-nav-divider"></div>

        <a href="../index.php" class="admin-nav-item" target="_blank">
            <span class="nav-icon">&#127760;</span>
            Ver sitio público
        </a>

        <a href="logout.php" class="admin-nav-item">
            <span class="nav-icon">&#128682;</span>
            Cerrar sesión
        </a>
    </nav>
</aside>
